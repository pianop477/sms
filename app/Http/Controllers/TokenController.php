<?php

namespace App\Http\Controllers;

use App\Models\FeeClearanceToken;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Services\FeeClearanceService;
use App\Services\NextSmsService;
use App\Traits\formatPhoneTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Concerns\FormatsMessages;
use Illuminate\Support\Facades\Artisan;

class TokenController extends Controller
{
    protected $feeClearanceService;
    protected $appBaseUrl;

    use formatPhoneTrait;

    public function __construct(FeeClearanceService $feeClearanceService)
    {
        $this->feeClearanceService = $feeClearanceService;

        $this->appBaseUrl = config('app.url', 'http://localhost');
    }

    /**
     * Show token verification form
     */
    public function showVerificationForm()
    {
        return view('tokens.verify');
    }

    /**
     * Verify token
     */
    public function verifyToken(Request $request)
    {
        // Log incoming request for debugging
        // Log::info('Token verification request received', [
        //     'token' => $request->token,
        //     'all_input' => $request->all(),
        //     'ip' => $request->ip()
        // ]);

        // Validate token
        $request->validate([
            'token' => 'required|string|min:4|max:4'
        ]);

        try {
            $result = $this->feeClearanceService->verifyToken($request->token);

            if ($result['valid']) {
                $student = $result['student'];

                // Load student class
                $student->load('class');

                // Get parent phone
                $parentPhone = null;
                if ($student->parent_id) {
                    $parent = \App\Models\Parents::with('user')->find($student->parent_id);
                    if ($parent && $parent->user) {
                        $parentPhone = $parent->user->phone;
                    }
                }

                // Log success
                // Log::info('Token verified successfully', [
                //     'token' => $request->token,
                //     'student_id' => $result['token']->student_id,
                //     'installment_id' => $result['token']->installment_id
                // ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Token ni sahihi! Mwanafunzi anaweza kuingia.',
                    'data' => [
                        'student' => [
                            'id' => $student->id,
                            'first_name' => $student->first_name,
                            'last_name' => $student->last_name,
                            'admission_number' => $student->admission_number,
                            'image' => $student->image,
                            'parent_phone' => $parentPhone,
                            'has_transport' => !is_null($student->transport_id),
                            'class' => $student->class ? [
                                'id' => $student->class->id,
                                'class_name' => $student->class->class_name
                            ] : null
                        ],
                        'installment' => [
                            'id' => $result['installment']->id,
                            'name' => $result['installment']->name,
                            'order' => $result['installment']->order
                        ],
                        'token' => [
                            'token' => $result['token']->token,
                            'expires_at' => Carbon::parse($result['expires_at'])->format('d-F-Y')
                        ]
                    ]
                ]);
            } else {
                // Log invalid token
                Log::warning('Invalid token attempt', [
                    'token' => $request->token,
                    'reason' => $result['reason'] ?? 'unknown',
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Token si sahihi au imekwisha muda wake.'
                ], 400);
            }
        } catch (\Exception $e) {
            // Log error
            Log::error('Token verification failed', [
                'token' => $request->token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kuhakiki token. Tafadhali jaribu tena.'
            ], 500);
        }
    }

    public function showResendForm()
    {
        return view('tokens.resend');
    }

    /**
     * Resend token to parent
     */
    /**
     */
    public function resendToken(Request $request)
    {
        $request->validate([
            'admission_number' => 'required|string|min:9'
        ]);

        try {
            $currentAcademicYear = date('Y');

            // ✅ Find student by admission number
            $student = Student::where('admission_number', $request->admission_number)
                ->first();

            if (!$student) {
                Log::warning('🔴 RESEND TOKEN - Student not found', [
                    'admission_number' => $request->admission_number
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Mwanafunzi hapatikani. Tafadhali hakiki namba ya usajili.'
                ], 404);
            }

            // ✅ Get parent phone - Correct relationship path
            // Student -> parent_id -> Parents -> user_id -> Users -> phone
            $parentPhone = null;
            $parentName = null;

            if ($student->parent_id) {
                $parent = Parents::with('user')->where('id', $student->parent_id)->first();

                if ($parent) {
                    // Log::info('🔵 RESEND TOKEN - Parent found', [
                    //     'parent_id' => $parent->id,
                    //     'has_user' => $parent->user ? 'YES' : 'NO'
                    // ]);

                    if ($parent->user) {
                        $parentPhone = $parent->user->phone;
                        $parentName = $parent->user->first_name . ' ' . $parent->user->last_name;

                        // Log::info('🔵 RESEND TOKEN - Parent phone found', [
                        //     'parent_id' => $parent->id,
                        //     'parent_name' => $parentName,
                        //     'phone' => $parentPhone
                        // ]);
                    } else {
                        Log::warning('🔴 RESEND TOKEN - Parent has no user record', [
                            'parent_id' => $parent->id
                        ]);
                    }
                } else {
                    Log::warning('🔴 RESEND TOKEN - Parent not found', [
                        'parent_id' => $student->parent_id
                    ]);
                }
            } else {
                Log::warning('🔴 RESEND TOKEN - Student has no parent_id', [
                    'student_id' => $student->id
                ]);
            }

            if (!$parentPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Namba ya simu ya mzazi haipatikani. Tafadhali wasiliana na ofisi ya shule.'
                ], 404);
            }

            // ✅ Check for active token
            $activeToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $currentAcademicYear)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->with('installment')
                ->first();

            // Log::info('🔵 RESEND TOKEN - Token search result', [
            //     'student_id' => $student->id,
            //     'token_found' => $activeToken ? 'YES' : 'NO'
            // ]);

            // ✅ If no active token, try to create one
            if (!$activeToken) {
                $service = new FeeClearanceService();
                $evaluation = $service->evaluate($student, $currentAcademicYear);

                if (!$evaluation['eligible']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hakuna token inayotumika kwa mwanafunzi huyu. Anaweza kuwa hajakidhi masharti ya malipo.'
                    ], 400);
                }

                $activeToken = $service->process($student, $currentAcademicYear);

                if (!$activeToken) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Imeshindwa kutengeneza token. Tafadhali jaribu tena.'
                    ], 500);
                }
            }

            // ✅ Send SMS
            $school = school::find($student->school_id);
            $formattedToken = $activeToken->token;
            $expiryDate = Carbon::parse($activeToken->expires_at)->format('d/m/Y');
            $link = $this->appBaseUrl . '/tokens/verify';
            $installmentName = $activeToken->installment->name;

            $message = "GATEPASS NA. ni: {$formattedToken}\n" .
                "Jina la Mtoto: {$student->first_name} {$student->last_name}\n" .
                "Awamu: {$installmentName}\n" .
                "Mwisho wa Awamu: {$expiryDate}\n" .
                "Hakiki uwapo shuleni/kwenye Basi" ;

            try {
                $smsService = new NextSmsService();
                $payload = [
                    'from' => $school->sender_id ?? 'SHULE APP',
                    'to' => $this->formatPhoneNumberForSms($parentPhone),
                    'text' => $message,
                    'reference' => 'resend_token_' . time()
                ];

                $smsService->sendSmsByNext(
                    $payload['from'],
                    $payload['to'],
                    $payload['text'],
                    $payload['reference']
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Token imetumwa tena kwa simu ya mzazi.',
                    'data' => [
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                        'admission_number' => $student->admission_number,
                        'token' => $formattedToken,
                        'phone' => $parentPhone,
                        'expires_at' => $expiryDate
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('🔴 RESEND TOKEN - SMS sending failed', [
                    'student_id' => $student->id,
                    'phone' => $parentPhone,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Token imetengenezwa. Token: ' . $formattedToken,
                    'data' => [
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                        'admission_number' => $student->admission_number,
                        'token' => $formattedToken,
                        'expires_at' => $expiryDate
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('🔴 RESEND TOKEN - General exception', [
                'admission_number' => $request->admission_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kutuma token. Tafadhali jaribu tena.'
            ], 500);
        }
    }

    public function getStudentTokens()
    {
        $currentAcademicYear = Carbon::now('Y');
        $tokens = FeeClearanceToken::with(['student'])
                                    ->whereYear('academic_year', $currentAcademicYear)
                                    ->orderBy('created_at', 'desc')
                                    ->orderBy('updated_at', 'desc')
                                    ->get();
        return view('tokens.student_tokens', compact('tokens'));
    }

    public function syncOffline(Request $request)
    {
        try {
            Artisan::call('tokens:sync-offline', ['--academic-year' => date('Y')]);
            $output = Artisan::output();
            return response()->json([
                'success' => true,
                'message' => 'Token sync started successfully.',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync tokens: ' . $e->getMessage()
            ], 500);
        }
    }
}
