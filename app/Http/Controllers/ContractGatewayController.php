<?php

namespace App\Http\Controllers;

use App\Models\contract_otp_validation;
use App\Models\school;
use App\Models\school_constracts;
use App\Models\Teacher;
use App\Services\NextSmsService;
use App\Traits\ResolveApplicantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class ContractGatewayController extends Controller
{
    //
    private $otpExpiryMinutes = 10;
    private $tokenExpiryHours = 1;
    private $maxAttempts = 3;

    use ResolveApplicantTrait;

    public function init()
    {
        return view('Contract.gateway_spa');
    }

    private function getCurrentApplicant()
    {
        // ===== CASE 1: Authenticated teacher =====
        if (Auth::check()) {
            $user = Auth::user();

            // Get teacher record to access role_id
            $teacher = Teacher::where('user_id', $user->id)->first();

            // Check if user is teacher (usertype 3) with appropriate role from teachers table
            if ($user->usertype == 3 && $teacher && in_array($teacher->role_id, [1, 2, 3, 4])) {

                $applicant = $this->resolveApplicantDetails($user->id, $user->school_id);

                if ($applicant['staff_type'] !== 'Unknown') {
                    return [
                        'id' => $applicant['staff_id'],
                        'details' => $applicant,
                        'auth_type' => 'teacher',
                        'school_id' => $user->school_id,
                        'user' => $user,
                        'teacher' => $teacher
                    ];
                }
            }

            // Alternative: If user is any staff type (not just teacher)
            // Try to resolve applicant details directly
            $applicant = $this->resolveApplicantDetails($user->id, $user->school_id);

            if ($applicant['staff_type'] !== 'Unknown') {
                // Log::info('User authenticated as: ' . $applicant['staff_type']);
                return [
                    'id' => $applicant['staff_id'],
                    'details' => $applicant,
                    'auth_type' => 'staff',
                    'school_id' => $user->school_id,
                    'user' => $user
                ];
            }
        }

        // ===== CASE 2: Non-teaching with OTP token =====
        $token = request()->bearerToken() ??
            request()->session()->get('contract_auth_token') ??
            request()->input('auth_token');

        if ($token) {
            // Check if token exists in request (from middleware)
            $otpSession = request()->get('otp_session');

            if (!$otpSession) {
                $otpSession = contract_otp_validation::where('auth_token', $token)
                    ->where('is_verified', true)
                    ->where('is_used', false)
                    ->where('expires_at', '>', now())
                    ->first();
            }

            if ($otpSession) {
                $applicant = $this->resolveApplicantDetails($otpSession->user_id, null);

                if ($applicant['staff_type'] !== 'Unknown') {
                    return [
                        'id' => $applicant['staff_id'],
                        'details' => $applicant,
                        'auth_type' => 'non-teaching',
                        'token' => $token,
                        'school_id' => $applicant['school_id'] ?? null,
                        'otp_session' => $otpSession
                    ];
                }
            }
        }

        // ===== CASE 3: No valid authentication =====
        return null;
    }

    public function verifyStaffId(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string|max:50'
        ]);

        // Rate limiting by IP
        $ipKey = 'staff-verify-' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Majaribio ni mengi sana. Tafadhali jaribu tena baada ya muda. ' .
                    RateLimiter::availableIn($ipKey) . ' seconds.',
                'retry_after' => RateLimiter::availableIn($ipKey)
            ], 429);
        }

        RateLimiter::hit($ipKey, 60);

        // Find staff across all tables
        $staffData = $this->findStaffByStaffId($request->staff_id);

        if (!$staffData) {
            return response()->json([
                'success' => false,
                'message' => 'Namba ya Utambulisho sio sahihi.'
            ], 404);
        }

        // ===== CHECK FOR EXISTING ACTIVE SESSION =====
        $existingSession = contract_otp_validation::where('user_id', $request->staff_id)
            ->where('is_verified', true)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        // If there's an active session, return it directly
        if ($existingSession) {
            // Refresh the session expiry (optional - extend by 1 more hour)
            $newExpiry = now()->addHours($this->tokenExpiryHours);
            $existingSession->update([
                'expires_at' => $newExpiry
            ]);

            // Update cache
            Cache::put('auth_' . $existingSession->auth_token, [
                'user_id' => $request->staff_id,
                'staff_data' => $staffData,
                'school' => school::find($staffData['school_id'] ?? null),
                'expires_at' => $newExpiry
            ], now()->addHours($this->tokenExpiryHours));

            return response()->json([
                'success' => true,
                'message' => 'Muda wa session bado upo, Subiri...',
                'data' => [
                    'has_active_session' => true,
                    'auth_token' => $existingSession->auth_token,
                    'staff_name' => $staffData['first_name'] . ' ' . ($staffData['last_name'] ?? ''),
                    'staff_type' => $staffData['staff_type'],
                    'expires_at' => $existingSession->expires_at,
                    'redirect_to' =>  '/contracts/dashboard',
                ]
            ]);
        }

        // ===== NO ACTIVE SESSION - PROCEED WITH OTP =====

        // Check if staff has phone number
        if (empty($staffData['phone'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna namba ya simu iliyosajiliwa kwa Namba hii. Tafadhali wasiliana na uongozi.'
            ], 400);
        }

        // Get school details
        $school = null;
        if (!empty($staffData['school_id'])) {
            $school = School::find($staffData['school_id']);
        }

        // Generate temporary token for next step
        $tempToken = Str::random(40);

        // Store in cache for 15 minutes
        Cache::put('staff_temp_' . $tempToken, [
            'staff_id' => $request->staff_id,
            'staff_data' => $staffData,
            'school' => $school,
            'ip' => $request->ip()
        ], now()->addMinutes(15));

        return response()->json([
            'success' => true,
            'message' => 'Namba ya Utambulisho imethibitishwa kikamilifu.',
            'data' => [
                'has_active_session' => false,
                'temp_token' => $tempToken,
                'staff_name' => $staffData['first_name'] . ' ' . ($staffData['last_name'] ?? ''),
                'phone_masked' => $this->maskPhone($staffData['phone']),
                'staff_type' => $staffData['staff_type']
            ]
        ]);
    }

    /*
      Step 2: Request OTP
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'temp_token' => 'required|string'
        ]);

        // Get cached data
        $tempData = Cache::get('staff_temp_' . $request->temp_token);

        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'Muda wa session umekwisha, tafadhali anza upya'
            ], 401);
        }

        // Verify IP matches
        if ($tempData['ip'] !== $request->ip()) {
            return response()->json([
                'success' => false,
                'message' => 'IP address mismatch. Please start over.'
            ], 403);
        }

        $staffId = $tempData['staff_id'];
        $staffData = $tempData['staff_data'];

        // Rate limiting by staff_id
        $otpKey = 'otp-' . $staffId;
        if (RateLimiter::tooManyAttempts($otpKey, 3)) {
            $seconds = RateLimiter::availableIn($otpKey);
            return response()->json([
                'success' => false,
                'message' => "Majaribio ni mengi sana, tafadhali jaribu tena baada ya sekunde {$seconds}.",
                'retry_after' => $seconds
            ], 429);
        }

        // Check for existing active OTP
        $existingOtp = contract_otp_validation::where('user_id', $staffId)
            ->where('is_verified', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingOtp) {
            $waitMinutes = now()->diffInMinutes($existingOtp->expires_at);
            return response()->json([
                'success' => false,
                'message' => "OTP ilishatumwa, tafadhali subiri baada ya dakika {$waitMinutes}.",
                'wait_minutes' => $waitMinutes
            ], 429);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database
        $otpRecord = contract_otp_validation::create([
            'user_id' => strtolower($staffId),
            'otp_code' => Hash::make($otp),
            'requested_at' => now(),
            'expires_at' => now()->addMinutes($this->otpExpiryMinutes),
            'ip_address' => $request->ip(),
            'is_active' => true,
            'is_used' => false,
            'is_expired' => false,
            'is_verified' => false,
        ]);

        // Send OTP via SMS
        try {
            $this->sendOtpSms($staffData['phone'], $otp, $tempData['school']);

            RateLimiter::hit($otpKey, 300); // 5 minutes decay

            return response()->json([
                'success' => true,
                'message' => 'OTP Imetumwa kikamilifu',
                'data' => [
                    'otp_id' => $otpRecord->id,
                    'expires_in' => $this->otpExpiryMinutes * 60, // seconds
                    'phone_masked' => $this->maskPhone($staffData['phone'])
                ]
            ]);
        } catch (\Exception $e) {
            // Delete the OTP record if SMS fails
            $otpRecord->delete();

            // Log::error('OTP SMS failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Imeshindwa kutuma OTP, jaribu tena'
            ], 500);
        }
    }

    /**
     * Step 3: Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_id' => 'required|integer',
            'otp_code' => 'required|string|size:6',
            'temp_token' => 'required|string'
        ]);

        // Get cached data
        $tempData = Cache::get('staff_temp_' . $request->temp_token);

        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'Session yako imekwisha muda wake, tafadhali anza upya'
            ], 401);
        }

        // Find OTP record
        $otpRecord = contract_otp_validation::where('id', $request->otp_id)
            ->where('user_id', $tempData['staff_id'])
            ->where('is_verified', false)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Code ya OTP Haitambuliki.'
            ], 400);
        }

        // Verify OTP
        if (!Hash::check($request->otp_code, $otpRecord->otp_code)) {
            // Increment attempts (optional)
            return response()->json([
                'success' => false,
                'message' => 'Code ya OTP haitambuliki'
            ], 400);
        }

        // Generate auth token
        $authToken = Str::random(64);
        $expiresAt = now()->addHours($this->tokenExpiryHours);

        // Update OTP record
        $otpRecord->update([
            'verified_at' => now(),
            'auth_token' => $authToken,
            'token_ttl' => $this->tokenExpiryHours * 60,
            'is_verified' => true,
            'is_active' => true,
            'expires_at' => $expiresAt
        ]);

        // Clear temp data
        Cache::forget('staff_temp_' . $request->temp_token);

        // Store auth token in cache for quick access
        Cache::put('auth_' . $authToken, [
            'user_id' => $tempData['staff_id'],
            'staff_data' => $tempData['staff_data'],
            'school' => $tempData['school'],
            'expires_at' => $expiresAt
        ], now()->addHours($this->tokenExpiryHours));

        // Store in session for web requests
        session(['contract_auth_token' => $authToken]);

        return response()->json([
            'success' => true,
            'message' => 'OTP imehakikiwa kikamilifu',
            'data' => [
                'auth_token' => $authToken,
                'expires_in' => $this->tokenExpiryHours * 3600, // seconds
                'staff_name' => $tempData['staff_data']['first_name'] . ' ' . ($tempData['staff_data']['last_name'] ?? ''),
                'staff_type' => $tempData['staff_data']['staff_type']
            ]
        ]);
    }

    /**
     * Step 4: Get Dashboard Data
     */
    public function dashboard(Request $request)
    {
        $authToken = $request->bearerToken() ?? session('contract_auth_token');

        if (!$authToken) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna token ya uthibitisho wako'
            ], 401);
        }

        // Verify token
        $session = $this->validateAuthToken($authToken);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session yako sio hai au imekwisha muda wake'
            ], 401);
        }

        // Get contracts for this applicant - LOAD ALL DATA
        $contracts = school_constracts::where('applicant_id', $session['user_id'])
            ->with(['statusHistories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add approval URL to each contract
        $contracts->each(function ($contract) {
            $contract->approval_letter_url = route('contract.approval.letter', [
                'id' => Hashids::encode($contract->id)
            ]);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'staff' => [
                    'first_name' => $session['staff_data']['first_name'],
                    'last_name' => $session['staff_data']['last_name'] ?? '',
                    'staff_type' => $session['staff_data']['staff_type'],
                    'staff_id' => $session['user_id'],
                    'phone' => $session['staff_data']['phone'],
                ],
                'school' => $session['school'],
                'contracts' => $contracts, // Sasa kila contract ina approval_letter_url
                'auth_token' => $authToken,
                'expires_at' => $session['expires_at']
            ]
        ]);
    }
    // ContractGatewayController.php
    public function showDashboard()
    {
        $applicant = $this->getCurrentApplicant();

        if (!$applicant) {
            return redirect()->route('contract.gateway.init');
        }

        $contracts = school_constracts::where('applicant_id', $applicant['id'])
            ->with(['statusHistories'])
            ->get();

        $school = School::find($applicant['school_id']);
        $staff = $applicant['details'];

        return view('Contract.non_teaching.dashboard', compact('contracts', 'school', 'staff'));
    }
    /**
     * Logout / Invalidate session
     */
    public function logout(Request $request)
    {
        $authToken = $request->bearerToken() ?? session('contract_auth_token');

        if ($authToken) {
            // Invalidate in database
            contract_otp_validation::where('auth_token', $authToken)
                ->update([
                    'is_used' => true,
                    'is_active' => false,
                    'is_expired' => true
                ]);

            // Remove from cache
            Cache::forget('auth_' . $authToken);

            // Clear session
            session()->forget('contract_auth_token');
        }

        return response()->json([
            'success' => true,
            'message' => 'Session imefutwa kikamilifu, sasa upo salama'
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'temp_token' => 'required|string'
        ]);

        $tempData = Cache::get('staff_temp_' . $request->temp_token);

        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'Session imekwisha muda wake, tafadhali anza upya'
            ], 401);
        }

        // Invalidate old OTPs
        contract_otp_validation::where('user_id', $tempData['staff_id'])
            ->where('is_verified', false)
            ->update([
                'is_expired' => true,
                'is_active' => false
            ]);

        // Trigger new OTP request
        return $this->requestOtp($request);
    }

    /**
     * Helper: Validate auth token
     */
    private function validateAuthToken($token)
    {
        // Try cache first
        $cached = Cache::get('auth_' . $token);
        if ($cached) {
            return $cached;
        }

        // Check database
        $record = contract_otp_validation::where('auth_token', $token)
            ->where('is_verified', true)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return null;
        }

        // Get staff data
        $staffData = $this->findStaffByStaffId($record->user_id);

        if (!$staffData) {
            return null;
        }

        // Get school
        $school = null;
        if (!empty($staffData['school_id'])) {
            $school = School::find($staffData['school_id']);
        }

        $session = [
            'user_id' => $record->user_id,
            'staff_data' => $staffData,
            'school' => $school,
            'expires_at' => $record->expires_at
        ];

        // Cache for future requests
        Cache::put('auth_' . $token, $session, now()->diffInMinutes($record->expires_at));

        return $session;
    }

    /**
     * Helper: Send OTP SMS
     */
    private function sendOtpSms($phone, $otp, $school = null)
    {
        $senderId = $school->sender_id ?? 'SHULE APP';

        $message = "{$senderId}: OTP ya dirisha la maombi ya mkataba ni: {$otp}. Itadumu kwa dakika {$this->otpExpiryMinutes}. Usimpe mtu Msimbo huu. Na kama haujatuma ombi hili, tafafhali achana nayo.";

        $nextSmsService = new NextSmsService();
        $destination = $this->formatPhoneNumber($phone);

        Log::info("OTP code: " . $message);
        // return $nextSmsService->sendSmsByNext($senderId, $destination, $message, uniqid());
    }

    /**
     * Helper: Format phone number
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Helper: Mask phone number for display
     */
    private function maskPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($phone);

        if ($length >= 8) {
            return substr($phone, 0, 4) . '****' . substr($phone, -4);
        }

        return '****' . substr($phone, -4);
    }


    private function findStaffByStaffId($staffId)
    {
        // Use trait method directly
        $applicant = $this->resolveApplicantDetails($staffId, null);

        // Check if found
        if ($applicant['staff_type'] !== 'Unknown') {
            return [
                'first_name' => $applicant['first_name'],
                'last_name' => $applicant['last_name'] ?? '',
                'phone' => $applicant['phone'],
                'school_id' => $applicant['school_id'] ?? null,
                'staff_type' => $applicant['staff_type'],
                'staff_id' => $applicant['staff_id']
            ];
        }

        return null;
    }

    public function extendSession(Request $request)
    {
        $authToken = $request->bearerToken() ?? session('contract_auth_token');

        if (!$authToken) {
            return response()->json(['success' => false, 'message' => 'No token'], 401);
        }

        $otpRecord = contract_otp_validation::where('auth_token', $authToken)
            ->where('is_verified', true)
            ->where('is_used', false)
            ->first();

        if (!$otpRecord) {
            return response()->json(['success' => false, 'message' => 'Invalid session'], 401);
        }

        // Extend by 1 hour
        $newExpiry = now()->addHours(1);
        $otpRecord->update(['expires_at' => $newExpiry]);

        return response()->json([
            'success' => true,
            'message' => 'Session extended',
            'expires_at' => $newExpiry
        ]);
    }
}
