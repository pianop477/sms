<?php

namespace App\Http\Controllers;

use App\Models\FeeClearanceToken;
use App\Models\Parents;
use App\Models\school;
use App\Models\Student;
use App\Services\FeeClearanceService;
use App\Services\NextSmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    protected $feeClearanceService;
    protected $appBaseUrl;

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
            'token' => 'required|string|min:6|max:6'
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
                            'expires_at' => $result['expires_at']
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
                    'message' => 'Token si sahihi au imekwisha muda wake. Tafadhali wasiliana na ofisi ya shule.'
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
     * Resend token to parent
     */
    public function resendToken(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|min:3',
            'identifier_type' => 'required|in:admission,phone'
        ]);

        try {
            // Find student
            $student = null;

            if ($request->identifier_type === 'admission') {
                $student = Student::where('admission_number', $request->identifier)
                    ->with(['parents.user', 'feeAssignment.feeStructure'])
                    ->first();
            } else {
                // Find by parent phone - parents.user_id -> users.id
                $parent = Parents::whereHas('user', function ($q) use ($request) {
                    $q->where('phone', 'like', "%{$request->identifier}%");
                })->first();

                if ($parent) {
                    $student = Student::where('parent_id', $parent->id)
                        ->with(['parents.user', 'feeAssignment.feeStructure'])
                        ->first();
                }
            }

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mwanafunzi hapatikani. Tafadhali hakiki namba ya admission au simu.'
                ], 404);
            }

            // Check for active token
            $activeToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->with('installment')
                ->first();

            if (!$activeToken) {
                // Check if student is eligible
                $service = new FeeClearanceService();
                $evaluation = $service->evaluate($student);

                if ($evaluation['eligible']) {
                    // Generate new token
                    $activeToken = $service->generateToken($student, $evaluation['installment']);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hakuna token inayotumika kwa mwanafunzi huyu. Anaweza kuwa hajakidhi masharti ya malipo.'
                    ], 400);
                }
            }

            // Get parent phone - correct relationship: parent.user.phone
            $parent = $student->parents;
            $parentPhone = null;

            if ($parent && $parent->user) {
                $parentPhone = $parent->user->phone;
            }

            if (!$parentPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Namba ya simu ya mzazi haipatikani. Tafadhali wasiliana na ofisi ya shule.'
                ], 404);
            }

            $school = school::find($student->school_id);

            // Format token
            $formattedToken = substr($activeToken->token, 0, 3) . '-' . substr($activeToken->token, 3, 3);
            $installmentName = $activeToken->installment->name ?? 'Current Term';
            $expiryDate = Carbon::parse($activeToken->expires_at)->format('d/m/Y');

            $link =  $this->appBaseUrl . '/tokens/verify';
            // Prepare message
            $message = "Habari, Gate Pass No yako ni:.\n\n" .
                "{$formattedToken}\n\n" .
                "Kwa ajili ya: {$student->first_name} {$student->last_name}\n" .
                "Muda wa kuisha: {$expiryDate}\n\n" .
                "Hakiki kupitia: {$link}\n\n" .
                "Onesha Getini au Kwenye School Bus.\n\n" .
                "Asante.";

            // Send SMS
            try {
                $smsService = new NextSmsService();
                $smsService->sendSmsByNext(
                    $school->sender_id ?? 'SHULE APP',
                    $parentPhone,
                    $message,
                    'resend_token_' . time()
                );

                // Log::info('Token resent successfully', [
                //     'student_id' => $student->id,
                //     'token' => $activeToken->token,
                //     'phone' => $parentPhone
                // ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Token imetumwa tena kwa simu ya mzazi.',
                    'data' => [
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                        'token' => $formattedToken,
                        'phone' => $parentPhone,
                        'expires_at' => $expiryDate
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('SMS sending failed', [
                    'error' => $e->getMessage(),
                    'phone' => $parentPhone
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Token imetengenezwa lakini imeshindwa kutumwa. Tafadhali wasiliana na ofisi ya shule.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Token resend failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hitilafu katika kutuma token. Tafadhali jaribu tena.'
            ], 500);
        }
    }
}
