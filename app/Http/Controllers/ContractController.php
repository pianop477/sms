<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\contract_otp_validation;
use App\Models\ContractStatusHistory;
use App\Models\other_staffs;
use App\Models\school;
use App\Models\school_constracts;
use App\Models\Teacher;
use App\Models\Transport;
use App\Models\User;
use App\Services\NextSmsService;
use App\Traits\ResolveApplicantTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ContractController extends Controller
{
    //
    use ResolveApplicantTrait;

    private function getCurrentApplicant(?Request $request = null)
    {
        $request = $request ?? request();
        $startTime = microtime(true);

        // Log::info('🔐 AUTHENTICATION STARTED', [
        //     'url' => $request->fullUrl(),
        //     'method' => $request->method(),
        //     'ip' => $request->ip(),
        //     'user_agent' => $request->userAgent()
        // ]);

        // ===== SECURITY CHECK 1 =====
        $this->checkRateLimit($request);

        // ===== SECURITY CHECK 2 =====
        $this->protectAgainstTokenHarvesting($request);

        /*
    |--------------------------------------------------------------------------
    | CASE 1: AUTHENTICATED USER (TEACHER)
    |--------------------------------------------------------------------------
    */

        if (Auth::check()) {

            $user = Auth::user();

            // Log::info('Auth check passed', [
            //     'user_id' => $user->id,
            //     'usertype' => $user->usertype
            // ]);

            if ($user->status != 1) {
                // Log::warning('Inactive user attempted access', [
                //     'user_id' => $user->id
                // ]);

                Auth::logout();
                return null;
            }

            $teacher = Teacher::where('user_id', $user->id)
                ->where('status', 1)
                ->first();

            if ($user->usertype == 3 && $teacher && in_array($teacher->role_id, [1, 3, 4])) {

                $applicant = $this->resolveApplicantDetails($user->id, $user->school_id);

                if ($applicant && $applicant['staff_type'] !== 'Unknown') {

                    // Log::info('✅ Authenticated as teacher', [
                    //     'staff_id' => $applicant['staff_id'],
                    //     'school_id' => $user->school_id,
                    //     'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
                    // ]);

                    return [
                        'id' => $applicant['staff_id'],
                        'details' => $applicant,
                        'auth_type' => 'teacher',
                        'school_id' => $user->school_id,
                        'user' => $user,
                        'teacher' => $teacher,
                        'auth_time' => now()->toDateTimeString()
                    ];
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | CASE 2: OTP TOKEN AUTHENTICATION
    |--------------------------------------------------------------------------
    */

        $token = $this->extractTokenSecurely($request);

        if ($token) {

            // Log::info('Token extracted', [
            //     'token_prefix' => substr($token, 0, 10) . '...',
            //     'source' => $this->getTokenSource($request)
            // ]);

            if (!$this->isValidTokenFormat($token)) {
                // Log::warning('Invalid token format detected', [
                //     'token_prefix' => substr($token, 0, 10)
                // ]);
                return null;
            }

            $otpSession = contract_otp_validation::where('auth_token', $token)
                ->where('is_verified', true)
                ->where('is_used', false)
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->where('ip_address', $request->ip())
                ->first();

            if ($otpSession) {

                // Log::info('Valid OTP session found', [
                //     'session_id' => $otpSession->id,
                //     'user_id' => $otpSession->user_id,
                //     'expires_at' => $otpSession->expires_at
                // ]);

                if ($otpSession->ip_address !== $request->ip()) {

                    // Log::warning('🔴 SECURITY ALERT: Token used from different IP', [
                    //     'original_ip' => $otpSession->ip_address,
                    //     'current_ip' => $request->ip()
                    // ]);

                    return null;
                }

                $staffId = $otpSession->user_id;

                // ===== SINGLE LOOKUP USING TRAIT =====
                $applicant = $this->resolveApplicantDetails($staffId, null);

                if ($applicant && $applicant['staff_type'] !== 'Unknown') {

                    // Log::info('✅ Authenticated via resolveApplicantDetails', [
                    //     'staff_id' => $applicant['staff_id'],
                    //     'staff_type' => $applicant['staff_type']
                    // ]);

                    $request->session()->put('contract_auth_token', $token);
                    $request->session()->put('auth_time', now()->toDateTimeString());
                    $request->session()->put('auth_ip', $request->ip());

                    return [
                        'id' => $applicant['staff_id'],
                        'details' => $applicant,
                        'auth_type' => 'non-teaching',
                        'token' => $token,
                        'school_id' => $applicant['school_id'] ?? null,
                        'otp_session' => $otpSession
                    ];
                }

                // Log::warning('Applicant details not found', [
                //     'user_id' => $staffId
                // ]);
            } else {

                $this->logInvalidTokenReason($token, $request);
            }
        }

        // Log::info('❌ Authentication failed', [
        //     'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
        // ]);

        return null;
    }

    private function extractTokenSecurely(Request $request)
    {
        // Priority: Session > Query > Bearer > Input > Cookie
        // But validate each source

        // Check session first (most secure for ongoing requests)
        if ($request->session()->has('contract_auth_token')) {
            $token = $request->session()->get('contract_auth_token');

            // Verify session IP matches
            if ($request->session()->get('auth_ip') === $request->ip()) {
                return $token;
            } else {
                // Log::warning('Session IP mismatch', [
                //     'session_ip' => $request->session()->get('auth_ip'),
                //     'current_ip' => $request->ip()
                // ]);
                $request->session()->forget('contract_auth_token');
            }
        }

        // Check query string (for new tab openings)
        if ($request->has('auth_token')) {
            $token = $request->query('auth_token');

            // Immediately store in session for subsequent requests
            if ($this->isValidTokenFormat($token)) {
                $request->session()->put('contract_auth_token', $token);
                $request->session()->put('auth_ip', $request->ip());
                $request->session()->put('auth_time', now()->toDateTimeString());
            }
            return $token;
        }

        // Check bearer token
        if ($request->bearerToken()) {
            return $request->bearerToken();
        }

        // Check input
        if ($request->has('auth_token')) {
            return $request->input('auth_token');
        }

        // Check cookie
        if ($request->cookie('contract_auth_token')) {
            return $request->cookie('contract_auth_token');
        }

        return null;
    }

    /**
     * Security: Validate token format
     */
    private function isValidTokenFormat($token)
    {
        // Tokens should be at least 20 chars and alphanumeric
        return strlen($token) >= 20 && preg_match('/^[a-zA-Z0-9]+$/', $token);
    }

    /**
     * Security: Get token source for logging
     */
    private function getTokenSource(Request $request)
    {
        if ($request->session()->has('contract_auth_token')) return 'session';
        if ($request->has('auth_token')) return 'query';
        if ($request->bearerToken()) return 'bearer';
        if ($request->input('auth_token')) return 'input';
        if ($request->cookie('contract_auth_token')) return 'cookie';
        return 'unknown';
    }

    /**
     * Security: Rate limiting
     */
    private function checkRateLimit(Request $request)
    {
        $key = 'auth_attempts_' . $request->ip();
        $attempts = Cache::get($key, 0);

        if ($attempts > 10) { // Max 10 attempts per minute
            // Log::warning('Rate limit exceeded', ['ip' => $request->ip()]);
            abort(429, 'Too many authentication attempts');
        }

        Cache::put($key, $attempts + 1, now()->addMinute());
    }

    /**
     * Security: Protect against token harvesting
     */
    private function protectAgainstTokenHarvesting(Request $request)
    {
        // Check for suspicious patterns
        $userAgent = $request->userAgent();

        if (empty($userAgent) || strlen($userAgent) < 10) {
            // Log::warning('Suspicious user agent', ['ip' => $request->ip()]);
            abort(403, 'Access denied');
        }
    }

    /**
     * Security: Log invalid token reason
     */
    private function logInvalidTokenReason($token, Request $request)
    {
        $tokenRecord = contract_otp_validation::where('auth_token', $token)->first();

        if (!$tokenRecord) {
            // Log::warning('Token not found in database', [
            //     'ip' => $request->ip(),
            //     'token_prefix' => substr($token, 0, 10)
            // ]);
        } elseif (!$tokenRecord->is_verified) {
            // Log::warning('Token not verified', ['id' => $tokenRecord->id]);
        } elseif ($tokenRecord->is_used) {
            // Log::warning('Token already used', ['id' => $tokenRecord->id]);
        } elseif (!$tokenRecord->is_active) {
            // Log::warning('Token inactive', ['id' => $tokenRecord->id]);
        } elseif ($tokenRecord->is_expired || $tokenRecord->expires_at <= now()) {
            // Log::warning('Token expired', ['id' => $tokenRecord->id]);
            // } elseif ($tokenRecord->ip_address !== $request->ip()) {
            // Log::warning('🔴 SECURITY: Token IP mismatch', [
            //     'id' => $tokenRecord->id,
            //     'token_ip' => $tokenRecord->ip_address,
            //     'request_ip' => $request->ip()
            // ]);

            // Flag for potential theft
            $tokenRecord->is_expired = true;
            $tokenRecord->save();
        }
    }

    /**
     * Security: Flag potential token theft
     */
    private function flagPotentialTokenTheft($otpSession, Request $request)
    {
        $otpSession->is_expired = true;
        $otpSession->save();

        // Log::critical('🔴🔴 POTENTIAL TOKEN THEFT DETECTED', [
        //     'session_id' => $otpSession->id,
        //     'user_id' => $otpSession->user_id,
        //     'original_ip' => $otpSession->ip_address,
        //     'current_ip' => $request->ip(),
        //     'user_agent' => $request->userAgent()
        // ]);

        // You could also send email alert to admin here
    }

    public function index(Request $request)
    {
        // ===== STEP 1: Get current applicant (Teacher or Non-Teaching) =====
        $applicant = $this->getCurrentApplicant($request);

        // If no valid authentication found
        if (!$applicant) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login or use OTP gateway.'
                ], 401);
            }

            // For web requests, redirect to appropriate login
            if (Auth::check()) {
                // User is logged in but not authorized for contracts
                Alert()->toast('You are not authorized to access contracts', 'error');
                return redirect()->back();  // Rudisha kwa page waliyotoka
            } else {
                // Not logged in - send to OTP gateway
                return redirect()->route('contract.gateway.init')
                    ->with('info', 'Please authenticate using your Staff ID');
            }
        }

        // ===== STEP 2: Get applicant details =====
        $applicantDetails = $applicant['details'];
        $applicantId = $applicant['id'];
        $authType = $applicant['auth_type']; // 'teacher' or 'non-teaching'

        // ===== STEP 3: Fetch contracts with status histories =====
        $contracts = school_constracts::with(['statusHistories' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])
            ->where('applicant_id', $applicantId)
            ->orderBy('approved_at', 'DESC')
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->map(function ($contract) use ($applicantDetails) {
                // Add applicant details to each contract
                $contract->applicant_first_name = $applicantDetails['first_name'] ?? 'Unknown';
                $contract->applicant_last_name = $applicantDetails['last_name'] ?? '';
                $contract->applicant_phone = $applicantDetails['phone'] ?? null;
                $contract->applicant_staff_id = $applicantDetails['staff_id'] ?? null;
                $contract->applicant_staff_type = $applicantDetails['staff_type'] ?? 'Unknown';

                // Get termination history if exists
                $terminationHistory = $contract->statusHistories
                    ->where('new_status', 'terminated')
                    ->first();

                if ($terminationHistory) {
                    $contract->terminated_at = $terminationHistory->created_at;
                    $contract->termination_reason = $terminationHistory->reason;
                    $contract->termination_type = $terminationHistory->metadata['termination_type'] ?? null;
                    $contract->terminated_by = $terminationHistory->metadata['terminated_by'] ?? $terminationHistory->changed_by;
                    $contract->termination_document = $terminationHistory->metadata['document_path'] ?? null;
                    $contract->termination_notes = $terminationHistory->metadata['notes'] ?? null;
                }

                return $contract;
            });

        // ===== STEP 4: Add authentication token for non-teaching staff =====
        $authToken = null;
        if ($authType === 'non-teaching') {
            $authToken = $applicant['token'] ?? session('contract_auth_token');
        }

        // ===== STEP 5: Return appropriate response based on request type =====

        // For API requests (JSON) - Used by non-teaching SPA
        if ($request->expectsJson() || $authType === 'non-teaching') {
            return response()->json([
                'success' => true,
                'data' => [
                    'applicant' => [ // Badili kutoka 'staff' kuwa 'applicant' kwa consistency
                        'first_name' => $applicantDetails['first_name'] ?? 'Unknown',
                        'last_name' => $applicantDetails['last_name'] ?? '',
                        'full_name' => trim(($applicantDetails['first_name'] ?? '') . ' ' . ($applicantDetails['last_name'] ?? '')),
                        'staff_id' => $applicantDetails['staff_id'] ?? null,
                        'member_id' => $applicantDetails['member_id'] ?? null,
                        'staff_type' => $applicantDetails['staff_type'] ?? 'Unknown',
                        'gender' => $applicantDetails['gender'] ?? 'male',
                        'phone' => $applicantDetails['phone'] ?? null,
                        'profile_image' => $applicantDetails['profile_image'] ?? null, // <-- IMPORTANT
                    ],
                    'contracts' => $contracts->map(function ($contract) {
                        return [
                            'id' => $contract->id,
                            'contract_type' => $contract->contract_type,
                            'job_title' => $contract->job_title,
                            'status' => $contract->status,
                            'applied_at' => $contract->applied_at,
                            'approved_at' => $contract->approved_at,
                            'activated_at' => $contract->activated_at,
                            'duration' => $contract->duration,
                            'start_date' => $contract->start_date,
                            'end_date' => $contract->end_date,
                            'basic_salary' => $contract->basic_salary,
                            'allowances' => $contract->allowances,
                            'is_active' => $contract->is_active,
                            'terminated_at' => $contract->terminated_at ?? null,
                            'termination_reason' => $contract->termination_reason ?? null,
                            'termination_type' => $contract->termination_type ?? null,
                            'has_termination_document' => !empty($contract->termination_document),
                            'applicant_file_path' => $contract->applicant_file_path,
                            'contract_file_path' => $contract->contract_file_path,
                            'qr_code_path' => $contract->qr_code_path,
                        ];
                    }),
                    'auth_token' => $authToken,
                    'auth_type' => $authType
                ]
            ]);
        }

        // For web requests (Teachers) - Return Blade view
        return view('Teachers.contract_renew', compact('contracts', 'authType', 'authToken'));
    }

    public function store(Request $request)
    {
        try {
            // ===== STEP 1: Get current applicant (Teacher or Non-Teaching) =====
            $applicantData = $this->getCurrentApplicant($request);

            // If no valid authentication found
            if (!$applicantData) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated. Please login or use OTP gateway.'
                    ], 401);
                }

                // Try to get token from session
                $token = session('contract_auth_token');
                if ($token) {
                    return redirect()->back()->with('info', 'Please try again');
                }


                if (Auth::check()) {
                    return redirect()->route('dashboard')
                        ->with('error', 'You are not authorized to access contracts');
                } else {
                    return redirect()->route('contract.gateway.init')
                        ->with('info', 'Please authenticate using your Staff ID');
                }
            }

            // ===== STEP 2: Extract applicant details =====
            $applicant = $applicantData['details'];
            $applicantId = $applicantData['id'];
            $authType = $applicantData['auth_type'];
            $schoolId = $applicantData['school_id'] ?? $this->getSchoolIdFromApplicant($applicant);

            if ($applicant['staff_type'] == 'Unknown') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Staff details not found in our records'
                    ], 404);
                }
                Alert()->toast('Staff details not found', 'error');
                return redirect()->back();
            }

            // ===== STEP 3: Validate request =====
            $request->validate([
                'contract_type' => 'required|in:provision,new',
                'application_letter' => 'required|mimes:pdf|max:2048',
            ]);

            // ===== STEP 4: Check existing contracts =====
            $existingContracts = school_constracts::where('applicant_id', $applicantId)
                ->where('school_id', $schoolId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Check for ACTIVE contract
            $activeContract = $existingContracts->filter(function ($contract) {
                return $contract->status == 'activated' &&
                    $contract->is_active == true &&
                    now()->lessThan($contract->end_date);
            })->first();

            if ($activeContract) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have an active contract. Cannot submit new application.'
                    ], 400);
                }
                Alert()->toast('You already have an active contract. Cannot submit new application.', 'error');
                return redirect()->back();
            }

            // Check for PENDING contract
            $pendingContract = $existingContracts->where('status', 'pending')->first();

            if ($pendingContract) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have a pending contract application. Please wait for review.'
                    ], 400);
                }
                Alert()->toast('You already have a pending contract application. Please wait for review.', 'error');
                return redirect()->back();
            }

            // Check for REJECTED contract (allow reapply)
            $rejectedContract = $existingContracts->where('status', 'rejected')->first();

            // Check for EXPIRED contract (allow new application)
            $expiredContract = $existingContracts->filter(function ($contract) {
                return $contract->status == 'activated' &&
                    $contract->is_active == false &&
                    now()->greaterThan($contract->end_date);
            })->first();

            // ===== STEP 5: Handle file upload =====
            $filePath = null;
            if ($request->hasFile('application_letter')) {
                $file = $request->file('application_letter');
                $fileName = 'application_' . time() . '_' . $applicantId . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('contracts/applications', $fileName, 'public');
            }

            // ===== STEP 6: Determine if this is a reapply =====
            $isReapply = $request->has('is_reapply') && $request->is_reapply == 1;
            $originalContractId = $request->original_contract_id ?? null;

            // ===== STEP 7: Process application (Reapply OR New) =====
            $message = '';
            $contract = null;

            // CASE 1: Reapply - Only allowed if there's a rejected contract
            if ($rejectedContract && !$activeContract && !$pendingContract) {
                // Security check: Verify ownership
                if ($rejectedContract->applicant_id != $applicantId) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized access'
                        ], 403);
                    }
                    Alert()->toast('Unauthorized access', 'error');
                    return redirect()->back();
                }

                // Delete old file
                if ($rejectedContract->applicant_file_path) {
                    Storage::disk('public')->delete($rejectedContract->applicant_file_path);
                }

                // Update contract with new data
                $rejectedContract->contract_type = $request->contract_type;
                $rejectedContract->applicant_file_path = $filePath;
                $rejectedContract->status = 'pending';
                $rejectedContract->applied_at = now();
                $rejectedContract->rejected_at = null;
                $rejectedContract->remarks = null;
                $rejectedContract->reapplied_at = now();
                $rejectedContract->reapply_count = ($rejectedContract->reapply_count ?? 0) + 1;
                $rejectedContract->save();

                $contract = $rejectedContract;
                $message = 'Contract resubmitted successfully!';
            }
            // CASE 2: New application - Only if no active/pending contracts
            elseif (!$activeContract && !$pendingContract) {
                $contract = new school_constracts();
                $contract->applicant_id = $applicantId;
                $contract->school_id = $schoolId;
                $contract->contract_type = $request->contract_type;
                $contract->job_title = $applicant['staff_type'];
                $contract->applicant_file_path = $filePath;
                $contract->status = 'pending';
                $contract->applied_at = now();
                $contract->is_active = false;
                $contract->reapply_count = 0;
                $contract->save();

                $message = 'Contract application submitted successfully!';
            } else {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not eligible to submit a contract at this time.'
                    ], 400);
                }
                Alert()->toast('You are not eligible to submit a contract at this time.', 'error');
                return redirect()->back();
            }

            // ===== STEP 8: Send SMS notification =====
            if (!empty($applicant['phone'])) {
                try {
                    $this->sendApplicationSms(
                        $applicant['phone'],
                        $applicant['first_name'],
                        $rejectedContract ? true : false,
                        $schoolId
                    );
                } catch (\Exception $e) {
                    // Log::warning('SMS failed', ['error' => $e->getMessage()]);
                    // Don't stop the process
                }
            }

            // ===== STEP 9: Return appropriate response =====
            if ($request->expectsJson()) {
                // For pure API requests (JSON response)
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'contract' => [
                            'id' => $contract->id,
                            'contract_type' => $contract->contract_type,
                            'status' => $contract->status,
                            'applied_at' => $contract->applied_at,
                            'job_title' => $contract->job_title,
                        ]
                    ]
                ], 201);
            }

            // For teachers (web)
            if ($authType === 'teacher') {
                Alert()->toast($message, 'success');
                return redirect()->route('contract.index');
            }

            // For non-teaching (gateway users) - REDIRECT WITH FLASH MESSAGE
            if ($authType === 'non-teaching') {
                // Store message in session flash
                session()->flash('success', $message);

                // Get token for redirect
                $token = $applicantData['token'] ?? $request->input('auth_token') ?? session('contract_auth_token');

                // Redirect to dashboard with token
                return redirect()->route('contract.dashboard', ['auth_token' => $token]);
            }

            // Fallback
            Alert()->toast($message, 'success');
            return redirect()->route('contract.index');
        } catch (\Exception $e) {
            // Log::error('Contract store error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error submitting application: ' . $e->getMessage()
                ], 500);
            }

            Alert()->toast('Error submitting application: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Helper: Get school ID from applicant data
     */
    private function getSchoolIdFromApplicant($applicant)
    {
        // Try to get from applicant details
        if (!empty($applicant['school_id'])) {
            return $applicant['school_id'];
        }

        // If not found, try to get from staff table
        if (!empty($applicant['staff_table_id']) && !empty($applicant['staff_type'])) {
            switch ($applicant['staff_type']) {
                case 'Teacher':
                    $teacher = Teacher::find($applicant['staff_table_id']);
                    return $teacher->school_id ?? null;
                case 'Transport Staff':
                    $transport = Transport::find($applicant['staff_table_id']);
                    return $transport->school_id ?? null;
                case 'Other Staff':
                    $other = other_staffs::find($applicant['staff_table_id']);
                    return $other->school_id ?? null;
            }
        }

        return null;
    }

    /**
     * Helper: Send SMS notification
     */
    private function sendApplicationSms($phone, $firstName, $isReapply = false, $schoolId = null)
    {
        $school = null;
        if ($schoolId) {
            $school = school::find($schoolId);
        }

        $senderId = $school->sender_id ?? "SHULE APP";
        $destination = $this->formatPhoneNumber($phone);

        $smsMessage = $isReapply
            ? "Habari {$firstName}, Ombi lako la mkataba limepokelewa kikamilifu baada ya marekebisho. Utapokea taarifa baada ya kufanyiwa kazi."
            : "Habari {$firstName}, Ombi lako la mkataba limepokelewa kikamilifu. Utapokea taarifa baada ya kufanyiwa kazi.";

        $nextSmsService = new NextSmsService();

        return $nextSmsService->sendSmsByNext(
            $senderId,
            $destination,
            $smsMessage,
            uniqid()
        );

        // Log::info($smsMessage);
    }

    //preview teachers his/her contract application
    public function previewMyApplication($id)
    {
        $decode = Hashids::decode($id);
        $contract = Contract::find($decode[0]);

        if (!$contract) {
            // Alert::error('Failed', 'No such contract records found');
            Alert()->toast('No such contract records found', 'error');
            return back();
        }

        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        if ($contract->teacher_id != $teacher->id) {
            // Alert::error('Error', 'The selected Contract is not uploaded by you');
            Alert()->toast('You are not authorized to view this page', 'error');
            return back();
        }

        $filePath = 'public/' . $contract->application_file; // Adjust path as needed

        if (!file_exists(storage_path('app/public/' . $contract->application_file))) {
            // Alert::error('Error', 'The application file is missing');
            Alert()->toast('The application file is missing', 'error');
            return back();
        }

        return response()->file(storage_path('app/' . $filePath));
    }

    public function edit($id)
    {
        $decode = Hashids::decode($id);
        $contract = Contract::find($decode[0]);

        if (!$contract) {
            // Alert::error('Failed', 'No such contract records found');
            Alert()->toast('No such contract records found', 'error');
            return back();
        }

        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        if ($contract->teacher_id != $teacher->id) {
            // Alert::error('Error', 'The selected Contract is not uploaded by you');
            Alert()->toast('You are not authorized to view this page', 'error');
            return back();
        }

        return view('Contract.edit', compact('contract'));
    }

    public function update(Request $request, $id)
    {
        $decode = Hashids::decode($id);
        $contract = Contract::find($decode[0]);
        if (!$contract) {
            // Alert::error('Failed', 'No such contract records found');
            Alert()->toast('No such contract records found', 'error');
            return back();
        }

        $this->validate($request, [
            'contract_type' => 'required|string|in:new,provision',
            'application_letter' => 'required|file|mimes:pdf|max:512'
        ], [
            'contract_type.required' => 'Select Contract type',
            'application_letter.required' => 'Application letter is required',
            'application_letter.mimes' => 'Application letter file must be a PDF file',
            'application_letter.max' => 'Application letter file must not exceed 512 KB',
        ]);

        // scan image file for virus
        $scanResult = $this->scanFileForViruses($request->file('application_letter'));
        if (!$scanResult['clean']) {
            Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
            return redirect()->back();
        }

        try {
            $filePath = '';

            if ($request->hasFile('application_letter')) {
                $file = $request->file('application_letter'); // Correctly access the file

                // Generate a unique file name
                $fileName = time() . '.' . $file->getClientOriginalExtension();

                // Define the destination path (within 'storage/app/public')
                $destinationPath = storage_path('app/public/contracts/contract_application');

                // Ensure the destination directory exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                // Delete the existing file, if any
                if ($contract->application_file) {
                    $existingFile = storage_path('app/public/' . $contract->application_file);
                    if (file_exists($existingFile)) {
                        unlink($existingFile);
                    }
                }

                // Move the new file to the destination path
                $file->move($destinationPath, $fileName);

                // Save the relative file path for database storage
                $filePath = 'contracts/contract_application/' . $fileName;

                // Update the contract's application_file column
                $contract->update(['application_file' => $filePath]);
            }


            $contract->update([
                'status' => $request->input('status', 'pending'),
                'contract_type' => $request->input('contract_type'),
                'application_file' => $filePath,
            ]);

            // Alert::success('Done', 'Application has been updated successfully');
            Alert()->toast('Application has been updated successfully', 'success');
            return redirect()->route('contract.index');
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        $decode = Hashids::decode($id);

        if (empty($decode)) {
            return redirect()->back()->with('error', 'Invalid contract ID');
        }

        $contract = school_constracts::find($decode[0]);

        if (!$contract) {
            return redirect()->back()->with('error', 'No such contract found');
        }

        try {
            // Delete application file if exists
            if ($contract->application_file) {
                $existingFile = storage_path('app/public/' . $contract->application_file);
                if (file_exists($existingFile)) {
                    unlink($existingFile);
                }
            }

            // Get auth type and token before deletion
            $authType = null;
            $token = null;

            if (Auth::check()) {
                $authType = 'teacher';
            } else {
                $authType = 'non-teaching';
                $token = session('contract_auth_token') ?? request()->input('auth_token');
            }

            $contract->delete();

            // Set flash message based on auth type
            if ($authType === 'teacher') {
                // For teachers, use Alert and redirect to contract.index
                Alert()->toast('Application has been deleted successfully', 'success');
                return redirect()->route('contract.index');
            } else {
                // For non-teaching, use session flash and redirect to dashboard
                return redirect()->route('contract.dashboard', ['auth_token' => $token])
                    ->with('success', 'Ombi limefutwa kikamilifu!');
            }
        } catch (\Exception $e) {
            // Log::error('Contract delete error: ' . $e->getMessage());

            if (Auth::check()) {
                Alert::error('Error', $e->getMessage());
                return back();
            } else {
                $token = session('contract_auth_token') ?? request()->input('auth_token');
                return redirect()->route('contract.dashboard', ['auth_token' => $token])
                    ->with('error', 'Hitilafu imetokea: ' . $e->getMessage());
            }
        }
    }

    public function contractManager()
    {
        $user = Auth::user();

        // Get all contracts for the school
        $contracts = school_constracts::where('school_id', $user->school_id)
            ->orderBy('applied_at', 'DESC')
            ->get();

        // Group activated contracts by year
        $contractsByYear = school_constracts::where('status', 'activated')
            ->where('school_id', $user->school_id)
            ->whereNotNull('activated_at')
            ->orderBy('activated_at', 'DESC')
            ->get()
            ->groupBy(function ($contract) {
                return Carbon::parse($contract->activated_at)->format('Y');
            });

        // Pending contract requests from ALL staff types
        $contractRequests = $this->getAllPendingContracts($user->school_id);

        // APPROVED CONTRACTS - Waiting for signed copy from applicant
        $approvedContracts = $this->getAllApprovedContracts($user->school_id);

        return view('Contract.manager_contact_group', compact(
            'contractsByYear',
            'contractRequests',
            'approvedContracts', // New variable
            'contracts'
        ));
    }

    /**
     * Get approved contracts waiting for signed copy
     */
    private function getAllApprovedContracts($schoolId)
    {
        // Get approved contracts (status = approved) that are not yet activated
        $approvedContracts = school_constracts::where('school_id', $schoolId)
            ->where('status', 'approved')
            ->whereNull('activated_at') // Not yet activated
            ->whereNotNull('approved_at') // Has been approved
            ->orderBy('approved_at', 'DESC')
            ->get();

        // Enrich with applicant details
        $enrichedContracts = collect();

        foreach ($approvedContracts as $contract) {
            $applicantDetails = $this->resolveApplicantDetails($contract->applicant_id, $schoolId);

            if ($applicantDetails) {
                $contractData = (object) array_merge(
                    $contract->toArray(),
                    $applicantDetails
                );
                $enrichedContracts->push($contractData);
            }
        }

        return $enrichedContracts;
    }

    /**
     * Get pending contracts from ALL staff types (Teachers, Transport, Other Staff)
     * This is the MAGIC method that solves your challenge!
     */
    private function getAllPendingContracts($schoolId)
    {
        // Get pending contracts
        $pendingContracts = school_constracts::where('school_id', $schoolId)
            ->whereIn('status', ['pending'])
            ->orderBy('applied_at', 'DESC')
            ->get();

        // Enrich each contract with applicant details based on their source table
        $enrichedContracts = collect();

        foreach ($pendingContracts as $contract) {
            $applicantDetails = $this->resolveApplicantDetails($contract->applicant_id, $schoolId);

            if ($applicantDetails) {
                // Merge contract data with applicant details
                $contractData = (object) array_merge(
                    $contract->toArray(),
                    $applicantDetails
                );
                $enrichedContracts->push($contractData);
            }
        }

        return $enrichedContracts;
    }

    /**
     * Get single contract details with applicant info
     */
    public function getContractDetails($id)
    {
        $decodedId = Hashids::decode($id)[0];
        $contract = school_constracts::findOrFail($decodedId);

        $applicantDetails = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

        return response()->json([
            'contract' => $contract,
            'applicant' => $applicantDetails
        ]);
    }

    /**
     * Approve Contract - Works for ALL staff types
     */
    public function approveContract(Request $request, $id)
    {
        $decodedId = Hashids::decode($id)[0];
        $contract = school_constracts::findOrFail($decodedId);
        $user = Auth::user();

        // Validate request
        $request->validate([
            'job_title' => 'required|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1|max:60',
            'remarks' => 'nullable|string',
            'contract_file' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        // Calculate dates
        $start_date = now();
        $end_date = now()->addMonths($request->duration);

        // Upload unsigned contract
        $contractFilePath = null;
        if ($request->hasFile('contract_file')) {
            $file = $request->file('contract_file');
            $fileName = 'unsigned_contract_' . time() . '_' . $contract->id . '.' . $file->getClientOriginalExtension();
            $contractFilePath = $file->storeAs('contracts/unsigned', $fileName, 'public');
        }

        // Update contract
        $contract->update([
            'holder_id' => $user->id,
            'job_title' => $request->job_title,
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'duration' => $request->duration,
            'approved_at' => now(),
            'approved_by' => $user->first_name . ' ' . $user->last_name,
            'remarks' => $request->remarks,
            'contract_file_path' => $contractFilePath,
            'status' => 'approved',
        ]);

        // Get applicant details for notification
        $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

        // Send SMS notification if phone number exists
        if (!empty($applicant['phone'])) {
            try {
                $nextSmsService = new NextSmsService();
                $destination = $this->formatPhoneNumber($applicant['phone']);

                // Get school name for the message
                $schoolName = $school->name ?? 'Shule yako';

                // Create portal login link - adjust according to your portal URL
                $portalLink = route('welcome'); // Or specific staff portal URL

                if ($contract->contract_type == 'provision') {
                    $message = "Habari {$applicant['first_name']}, Ombi lako la mkataba wa muda wa matazamio limeshughulikiwa. Ingia kwenye portal kupitia $portalLink pakua barua, saini na kisha irudishwe ofisini. Asante.";
                } else {
                    $message = "Habari {$applicant['first_name']}, Ombi lako la mkataba mpya wa ajira limeshughulikiwa. Ingia kwenye portal kupitia $portalLink kupakua mkataba, saini na kisha irudishwe ofisini. Asante.";
                }
                $payload = [
                    'from' => $school->sender_id ?? "SHULE APP",
                    'to' => $destination,
                    'text' => $message,
                    'reference' => uniqid(),
                ];

                // Log::info('Sending SMS for contract activation', [
                //     'contract_id' => $contract->id,
                //     'applicant_id' => $contract->applicant_id,
                //     'phone' => $destination,
                //     'message' => $message
                // ]);

                $response = $nextSmsService->sendSmsByNext(
                    $payload['from'],
                    $payload['to'],
                    $payload['text'],
                    $payload['reference']
                );

                if (!$response['success']) {
                    // Log SMS failure but don't stop the process
                    // Log::warning('SMS failed for contract activation', [
                    //     'contract_id' => $contract->id,
                    //     'applicant_id' => $contract->applicant_id,
                    //     'error' => $response['error'] ?? 'Unknown error'
                    // ]);

                    Alert()->toast('Contract approved successfully! SMS sending failed: ' . ($response['error'] ?? 'Unknown error'), 'warning');
                    return redirect()->back();
                }

                Alert()->toast("Contract approved successfully! SMS sent to {$applicant['first_name']}", 'success');
            } catch (\Exception $e) {
                // Log exception but don't break the contract activation
                // Log::error('SMS exception for contract activation', [
                //     'contract_id' => $contract->id,
                //     'error' => $e->getMessage()
                // ]);

                Alert()->toast('Contract activated successfully! SMS could not be sent.', 'warning');
            }
        } else {
            Alert()->toast("Contract activated successfully! (No phone number available for SMS)", 'info');
        }

        Alert()->toast("Contract approved successfully. Waiting for signed copy.", 'success');
        return redirect()->back();
    }

    /**
     * Reject Contract - Works for ALL staff types
     */
    public function rejectContract(Request $request, $id)
    {
        $decodedId = Hashids::decode($id)[0];
        $contract = school_constracts::findOrFail($decodedId);

        // Load school relationship
        $school = school::find($contract->school_id); // Assume relationship exists

        $user = Auth::user();
        $request->validate([
            'remarks' => 'required|string|min:5',
        ]);

        $contract->update([
            'holder_id' => $user->id,
            'remarks' => $request->remarks,
            'rejected_at' => now(),
            'status' => 'rejected',
        ]);

        // Get applicant details for notification
        $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

        // Send SMS notification if phone number exists
        if (!empty($applicant['phone'])) {
            try {
                $nextSmsService = new NextSmsService();
                $destination = $this->formatPhoneNumber($applicant['phone']);

                // Get school name for the message
                $schoolName = $school->name ?? 'Shule yako';

                // Create portal login link - adjust according to your portal URL
                $portalLink = route('welcome'); // Or specific staff portal URL

                if ($contract->contract_type == 'provision') {
                    $message = "Habari {$applicant['first_name']}, Ombi lako la mkataba wa muda wa matazamio limekataliwa. Sababu: {$request->remarks}. Ingia kwenye portal kupitia $portalLink kwa maelekezo zaidi.";
                } else {
                    $message = "Habari {$applicant['first_name']}, Ombi lako la mkataba mpya wa ajira limekataliwa. Sababu: {$request->remarks}. Ingia kwenye portal kupitia $portalLink kwa maelekezo zaidi.";
                }

                $payload = [
                    'from' => $school->sender_id ?? "SHULE APP",
                    'to' => $destination,
                    'text' => $message,
                    'reference' => uniqid(),
                ];

                // Log::info('Sending SMS for contract rejection', [
                //     'contract_id' => $contract->id,
                //     'applicant_id' => $contract->applicant_id,
                //     'phone' => $destination,
                //     'message' => $message
                // ]);
                $response = $nextSmsService->sendSmsByNext(
                    $payload['from'],
                    $payload['to'],
                    $payload['text'],
                    $payload['reference']
                );

                if (!$response['success']) {
                    // Log SMS failure but don't stop the process
                    // Log::warning('SMS failed for contract rejection', [
                    //     'contract_id' => $contract->id,
                    //     'applicant_id' => $contract->applicant_id,
                    //     'error' => $response['error'] ?? 'Unknown error'
                    // ]);

                    Alert()->toast("Contract rejected successfully! SMS sending failed: " . ($response['error'] ?? 'Unknown error'), 'warning');
                    return redirect()->back();
                }

                Alert()->toast("Contract rejected successfully! SMS sent to {$applicant['first_name']}", 'info');
            } catch (\Exception $e) {
                // Log exception but don't break the contract rejection
                // Log::error('SMS exception for contract rejection', [
                //     'contract_id' => $contract->id,
                //     'error' => $e->getMessage()
                // ]);

                Alert()->toast('Contract rejected successfully! SMS could not be sent.', 'warning');
            }
        } else {
            Alert()->toast("Contract rejected successfully! (No phone number available for SMS)", 'info');
        }

        return redirect()->back();
    }

    /**
     * Upload Signed Contract - Step 2
     */
    public function uploadSignedContract(Request $request, $id)
    {
        $decodedId = Hashids::decode($id)[0];
        $contract = school_constracts::findOrFail($decodedId);

        // Load school relationship
        $school = school::find($contract->school_id); // Assume relationship exists

        $request->validate([
            'signed_contract' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        // Generate verification token
        $verifyToken = Str::random(32) . '_' . time();

        // ===== ALTERNATIVE: Generate QR Code as XML format =====
        $qrCodePath = 'qrcodes/contract_' . $contract->id . '_' . time() . '.svg';

        // Use XML format which doesn't need any image extension
        $verifyUrl = route('contracts.verify', ['token' => $verifyToken]);
        $qrCode = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->generate($verifyUrl);

        Storage::disk('public')->put($qrCodePath, $qrCode);

        // Upload signed contract
        if ($request->hasFile('signed_contract')) {
            $file = $request->file('signed_contract');
            $fileName = 'signed_contract_' . time() . '_' . $contract->id . '.' . $file->getClientOriginalExtension();
            $signedContractPath = $file->storeAs('contracts/signed', $fileName, 'public');

            // Delete old unsigned contract
            if ($contract->contract_file_path && Storage::disk('public')->exists($contract->contract_file_path)) {
                Storage::disk('public')->delete($contract->contract_file_path);
            }
        }

        // Activate contract
        $contract->update([
            'contract_file_path' => $signedContractPath,
            'activated_at' => now(),
            'verify_token' => $verifyToken,
            'qr_code_path' => $qrCodePath,
            'is_active' => true,
            'status' => 'activated',
        ]);

        // Get applicant details for SMS
        $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

        // Send SMS notification if phone number exists
        if (!empty($applicant['phone'])) {
            try {
                $nextSmsService = new NextSmsService();
                $destination = $this->formatPhoneNumber($applicant['phone']);

                // Get school name for the message
                $schoolName = $school->name ?? 'Shule yako';
                $startDate = Carbon::parse($contract->start_date)->format('d M Y');
                $endDate = Carbon::parse($contract->end_date)->format('d M Y');

                // Create portal login link - adjust according to your portal URL
                $portalLink = route('welcome'); // Or specific staff portal URL

                if ($contract->contract_type == 'provision') {
                    $message = "Habari {$applicant['first_name']}, Barua ya mkataba wa muda wa matazamio umekamilika na utaanza kutumika: {$startDate} hadi {$endDate}. Ingia kwenye portal kupitia $portalLink ili kupakua. Asante.";
                } else {
                    $message = "Habari {$applicant['first_name']}, Mkataba wako wa ajira umekamilika na utaanza kutumika: {$startDate} hadi {$endDate}. Ingia kwenye portal kupitia $portalLink ili kupakua. Asante.";
                }
                $payload = [
                    'from' => $school->sender_id ?? "SHULE APP",
                    'to' => $destination,
                    'text' => $message,
                    'reference' => uniqid(),
                ];

                // Log::info('Sending SMS for contract activation', [
                //     'contract_id' => $contract->id,
                //     'applicant_id' => $contract->applicant_id,
                //     'phone' => $destination,
                //     'message' => $message
                // ]);

                $response = $nextSmsService->sendSmsByNext(
                    $payload['from'],
                    $payload['to'],
                    $payload['text'],
                    $payload['reference']
                );

                if (!$response['success']) {
                    // Log SMS failure but don't stop the process
                    // Log::warning('SMS failed for contract activation', [
                    //     'contract_id' => $contract->id,
                    //     'applicant_id' => $contract->applicant_id,
                    //     'error' => $response['error'] ?? 'Unknown error'
                    // ]);

                    Alert()->toast('Contract activated successfully! SMS sending failed: ' . ($response['error'] ?? 'Unknown error'), 'warning');
                    return redirect()->back();
                }

                Alert()->toast("Contract activated successfully! SMS sent to {$applicant['first_name']}", 'success');
            } catch (\Exception $e) {
                // Log exception but don't break the contract activation
                // Log::error('SMS exception for contract activation', [
                //     'contract_id' => $contract->id,
                //     'error' => $e->getMessage()
                // ]);

                Alert()->toast('Contract activated successfully! SMS could not be sent.', 'warning');
            }
        } else {
            Alert()->toast("Contract activated successfully! (No phone number available for SMS)", 'info');
        }

        return redirect()->back();
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure the number starts with the country code (e.g., 255 for Tanzania)
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }


    public function adminPreviewFile($id)
    {
        $decode = Hashids::decode($id);
        $contract = school_constracts::find($decode[0]);

        if (!$contract) {
            // Alert::error('Failed', 'No such contract records found');
            Alert()->toast('No such contract records found', 'error');
            return back();
        }

        $user = Auth::user();

        $filePath = 'public/' . $contract->application_file; // Adjust path as needed

        if (!file_exists(storage_path('app/public/' . $contract->application_file))) {
            Alert::error('Error', 'The application file is missing');
            return back();
        }

        return response()->file(storage_path('app/' . $filePath));
    }

    public function contractByMonths($year)
    {
        $user = Auth::user();

        // Fetch and group contracts by month - now using activated_at instead of approved_at
        $contractsByMonth = school_constracts::where('status', 'activated')
            ->where('school_id', $user->school_id)
            ->whereYear('activated_at', $year) // Filter by year using activated_at
            ->orderBy('activated_at', 'DESC')
            ->get()
            ->groupBy(function ($contract) {
                return \Carbon\Carbon::parse($contract->activated_at)->format('F'); // Group by month name
            });

        return view('Contract.contract_by_months', compact('year', 'contractsByMonth'));
    }

    public function getAllActivatedContract($year, $month)
    {
        $user = Auth::user();

        // Month mapping
        $monthsArray = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12
        ];

        $targetMonth = $monthsArray[$month] ?? null;

        if (!$targetMonth) {
            Alert()->toast('Invalid month selected', 'error');
            return redirect()->back();
        }

        // Get all activated/expired/terminated contracts for the month
        $contracts = school_constracts::where('school_id', $user->school_id)
            ->whereIn('status', ['activated', 'expired', 'terminated'])
            ->whereYear('activated_at', $year)
            ->whereMonth('activated_at', $targetMonth)
            ->orderBy('activated_at', 'desc')
            ->get();

        // Enrich contracts with applicant details AND termination history
        $allContracts = $contracts->map(function ($contract) use ($user) {
            $applicantDetails = $this->resolveApplicantDetails(
                $contract->applicant_id,
                $contract->school_id
            );

            // Get termination history if exists
            $terminationHistory = \App\Models\ContractStatusHistory::where('contract_id', $contract->id)
                ->where('new_status', 'terminated')
                ->latest()
                ->first();

            // Get all status history for timeline
            $statusHistory = \App\Models\ContractStatusHistory::where('contract_id', $contract->id)
                ->orderBy('created_at', 'asc')
                ->get();

            // Create enriched contract object
            $enrichedContract = new \stdClass();

            // Copy all contract properties
            foreach ($contract->toArray() as $key => $value) {
                $enrichedContract->$key = $value;
            }

            // Add applicant details
            $enrichedContract->first_name = $applicantDetails['first_name'] ?? 'Unknown';
            $enrichedContract->last_name = $applicantDetails['last_name'] ?? '';
            $enrichedContract->gender = $applicantDetails['gender'] ?? 'Not Specified';
            $enrichedContract->phone = $applicantDetails['phone'] ?? null;
            $enrichedContract->staff_id = $applicantDetails['staff_id'] ?? $contract->applicant_id;
            $enrichedContract->staff_type = $applicantDetails['staff_type'] ?? 'Unknown';

            // Add termination data from history if exists
            if ($terminationHistory) {
                $metadata = $terminationHistory->metadata ?? [];
                $enrichedContract->terminated_at = $terminationHistory->created_at;
                $enrichedContract->termination_reason = $terminationHistory->reason;
                $enrichedContract->termination_type = $metadata['termination_type'] ?? null;
                $enrichedContract->terminated_by = $metadata['terminated_by'] ?? $terminationHistory->changed_by;
                $enrichedContract->termination_document_path = $metadata['document_path'] ?? null;
                $enrichedContract->termination_notes = $metadata['notes'] ?? null;
            } else {
                $enrichedContract->terminated_at = null;
                $enrichedContract->termination_reason = null;
                $enrichedContract->termination_type = null;
                $enrichedContract->terminated_by = null;
                $enrichedContract->termination_document_path = null;
                $enrichedContract->termination_notes = null;
            }

            // Add status history for timeline
            $enrichedContract->status_history = $statusHistory;

            return $enrichedContract;
        });

        return view('Contract.approved_contract', compact('year', 'month', 'allContracts'));
    }

    public function generateContractLetter($id)
    {
        try {
            $decodedId = Hashids::decode($id)[0];
            $contract = school_constracts::findOrFail($decodedId);

            // Get applicant details
            $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

            // Get school details
            $school = school::find($contract->school_id);

            $logoPath = null;
            if ($school->logo) {
                // Try multiple possible paths
                $possiblePaths = [
                    storage_path('app/public/logo/' . $school->logo),
                    public_path('storage/logo/' . $school->logo),
                    storage_path('app/logo/' . $school->logo),
                    public_path('logo/' . $school->logo),
                ];

                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $logoPath = $path;
                        // Log::info('Logo found at: ' . $path);
                        break;
                    }
                }

                if (!$logoPath) {
                    // Log::warning('Logo file not found in any path', [
                    //     'logo' => $school->logo,
                    //     'school_id' => $school->id
                    // ]);
                }
            }

            // Calculate net pay
            $basicSalary = $contract->basic_salary ?? 0;
            $allowances = $contract->allowances ?? 0;
            $netPay = $basicSalary + $allowances;

            // Get Authorized User
            $authorizedUser = \App\Models\User::find($contract->holder_id);
            // return $authorizedUser;

            $position = '';
            if ($authorizedUser) {
                if ($authorizedUser->usertype == 2) {
                    $position = 'School Manager';
                } elseif ($authorizedUser->usertype == 3) {
                    $position = 'Head Teacher';
                }
            }

            // Generate QR code image
            $qrCodePath = storage_path('app/public/' . $contract->qr_code_path);
            $qrImage = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($qrCodePath));

            $contractData = [
                'school_name' => $school->school_name,
                'postal_address' => $school->postal_address,
                'postal_name' => $school->postal_name,
                'country' => $school->country,
                'logo' => $logoPath ? $this->imageToBase64($logoPath) : null,
                'first_name' => $applicant['first_name'] ?? 'Unknown',
                'last_name' => $applicant['last_name'] ?? '',
                'address' => $applicant['address'] ?? $school->postal_address ?? 'N/A',
                'approved_at' => $contract->activated_at ?? now(),
                'duration' => $contract->duration,
                'start_date' => $contract->start_date ?? now(),
                'end_date' => $contract->end_date ?? now()->addMonths($contract->duration),
                'contract_type' => $contract->contract_type ?? 'new',
                'basic_salary' => $basicSalary,
                'allowances' => $allowances,
                'net_pay' => $netPay,
                'authorized_person_name' => $contract->approved_by ?? 'Authorized Officer',
                'position' => $position
            ];

            $pdf = PDF::loadView('Contract.contract_file', [
                'contract' => $contractData,
                'qrImage' => $qrImage
            ]);

            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream(
                'contract_letter_' .
                    $contractData['first_name'] . '_' .
                    $contractData['last_name'] . '.pdf'
            );
        } catch (\Exception $e) {
            // Log::error('Contract letter generation error: ' . $e->getMessage());
            Alert()->toast('Error generating contract letter', 'error');
            return redirect()->back();
        }
    }

    public function reapply(Request $request, $id)
    {
        try {
            $decodedId = Hashids::decode($id)[0];
            $oldContract = school_constracts::findOrFail($decodedId);

            // ===== STEP 1: Get current applicant =====
            $applicantData = $this->getCurrentApplicant($request);

            if (!$applicantData) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated. Please login or use OTP gateway.'
                    ], 401);
                }
                return redirect()->route('contract.gateway.init')
                    ->with('info', 'Please authenticate using your Staff ID');
            }

            // ===== STEP 2: Extract user details =====
            $currentUserDetails = $applicantData['details'];
            $currentUserId = $applicantData['id'];
            $authType = $applicantData['auth_type'];

            // ===== STEP 3: Get original contract applicant details =====
            $originalApplicant = $this->resolveApplicantDetails($oldContract->applicant_id, null);

            // ===== STEP 4: Verify ownership =====
            if ($originalApplicant['staff_id'] != $currentUserId) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to reapply this contract'
                    ], 403);
                }
                Alert()->toast('You are not authorized to reapply this contract', 'error');
                return redirect()->back();
            }

            // ===== STEP 5: Verify contract is rejected =====
            if ($oldContract->status != 'rejected') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only rejected contracts can be reapplied'
                    ], 400);
                }
                Alert()->toast('Only rejected contracts can be reapplied', 'error');
                return redirect()->back();
            }

            // ===== STEP 6: Check for pending contract =====
            $existingPending = school_constracts::where('applicant_id', $currentUserId)
                ->where('status', 'pending')
                ->where('id', '!=', $oldContract->id)
                ->first();

            if ($existingPending) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have a pending application.'
                    ], 400);
                }
                Alert()->toast('You already have a pending application.', 'error');
                return redirect()->back();
            }

            // ===== STEP 7: For API requests - return JSON =====
            if ($request->expectsJson() || $request->wantsJson()) {
                $reapplyData = [
                    'contract_id' => $oldContract->id,
                    'contract_type' => $oldContract->contract_type,
                    'remarks' => $oldContract->remarks,
                    'staff_id' => $currentUserId,
                    'staff_name' => $currentUserDetails['first_name'] . ' ' . $currentUserDetails['last_name'],
                    'contract' => [
                        'id' => $oldContract->id,
                        'contract_type' => $oldContract->contract_type,
                        'job_title' => $oldContract->job_title,
                        'applied_at' => $oldContract->applied_at,
                        'rejected_at' => $oldContract->rejected_at,
                        'remarks' => $oldContract->remarks,
                        'applicant_file_path' => $oldContract->applicant_file_path ? asset('storage/' . $oldContract->applicant_file_path) : null,
                    ]
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Ready to reapply',
                    'data' => $reapplyData
                ]);
            }

            // ===== STEP 8: For WEB requests (both teachers AND non-teaching) =====
            // Store token in session if available
            if ($applicantData['token'] ?? null) {
                session(['contract_auth_token' => $applicantData['token']]);
            }

            // Store reapply data in session
            session(['reapply_data' => [
                'contract_id' => $oldContract->id,
                'contract_type' => $oldContract->contract_type,
                'remarks' => $oldContract->remarks,
            ]]);

            // ===== CHOOSE VIEW BASED ON AUTH TYPE =====
            if ($authType === 'teacher') {
                // Teacher logged in - use framed layout
                return view('Contract.contract_reapply', [
                    'oldContract' => $oldContract,
                    'authToken' => $applicantData['token'] ?? null
                ]);
            } else {
                // Non-teaching using gateway - use gateway layout
                return view('Contract.contract_reapply_gateway', [
                    'oldContract' => $oldContract,
                    'authToken' => $applicantData['token'] ?? null
                ]);
            }
        } catch (\Exception $e) {
            // Log::error('Contract reapply error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error preparing reapplication: ' . $e->getMessage()
                ], 500);
            }

            Alert()->toast('Error preparing reapplication', 'error');
            return redirect()->back();
        }
    }

    public function contractManagement()
    {
        $user = Auth::user();

        // Get the current user's staff details
        $applicantDetails = $this->resolveApplicantDetails($user->id, $user->school_id);

        // Get contracts using staff_id, not user_id
        $contracts = school_constracts::where('applicant_id', $applicantDetails['staff_id'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('Teachers.contract_renew', compact('contracts'));
    }

    public function downloadContract($id)
    {
        $decode = Hashids::decode($id);
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $contract = Contract::query()
            ->join('teachers', 'teachers.id', '=', 'contracts.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->join('schools', 'schools.id', '=', 'contracts.school_id')
            ->select(
                'contracts.*',
                'teachers.member_id',
                'users.first_name',
                'users.last_name',
                'users.gender',
                'users.phone',
                'schools.school_name',
                'teachers.address',
                'schools.school_reg_no',
                'schools.postal_address',
                'schools.postal_name',
                'schools.logo',
                'schools.country'
            )
            ->where('contracts.teacher_id', $teacher->id)
            ->find($decode[0]);
        if (! $contract) {
            // Alert::error('Failed', 'No such contract record was found');
            Alert()->toast('No such contract record was found', 'error');
            return redirect()->back();
        }

        $qrPayload = route('contracts.verify', $contract->verify_token);

        $qrSvg = QrCode::format('svg')      // ← SVG haina utegemezi wa imagick
            ->size(150)
            ->margin(1)
            ->generate($qrPayload);

        $qrImage = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $pdf = Pdf::loadView('Contract.contract_file', [
            'contract'  => $contract,
            'qrImage'   => $qrImage,
            'verifyUrl' => $qrPayload,
        ]);

        return $pdf->stream('Contract_' . $contract->member_id . '.pdf');
    }

    public function generateApprovalLetter(Request $request, $id)
    {
        $startTime = microtime(true);

        try {
            // Log::info('📄 APPROVAL LETTER REQUEST', [
            //     'id' => $id,
            //     'ip' => $request->ip(),
            //     'has_token_in_query' => $request->has('auth_token'),
            //     'user_agent' => $request->userAgent()
            // ]);

            // Decode ID securely
            $decodedId = Hashids::decode($id);
            if (empty($decodedId) || !is_array($decodedId) || count($decodedId) === 0) {
                // Log::warning('Invalid contract ID format', ['id' => $id, 'ip' => $request->ip()]);
                abort(404);
            }

            $contractId = $decodedId[0];
            $contract = school_constracts::findOrFail($contractId);

            // ===== CRITICAL: Handle token from query string =====
            // If token is in query string, store it in session for this request
            if ($request->has('auth_token')) {
                $tokenFromQuery = $request->query('auth_token');
                // Log::info('Found token in query string', ['token_prefix' => substr($tokenFromQuery, 0, 10) . '...']);

                // Store in session
                $request->session()->put('contract_auth_token', $tokenFromQuery);
                $request->session()->put('auth_ip', $request->ip());

                // Also merge into request for this request
                $request->merge(['auth_token' => $tokenFromQuery]);
            }

            // ===== STEP 1: Get authenticated applicant =====
            $applicantData = $this->getCurrentApplicant($request);

            // If no authentication, redirect to gateway with return URL
            if (!$applicantData) {
                // Log::warning('Approval letter: No authentication', [
                //     'contract_id' => $contractId,
                //     'ip' => $request->ip()
                // ]);

                // Build return URL
                $returnUrl = route('contract.approval.letter', ['id' => $id]);

                // If we have token, add it
                if ($request->has('auth_token')) {
                    $returnUrl .= '?auth_token=' . $request->query('auth_token');
                }

                // For non-teaching, redirect to gateway
                return redirect()->route('contract.gateway.init', ['return_to' => urlencode($returnUrl)])
                    ->with('info', 'Tafadhali hakiki utambulisho wako');
            }

            // ===== STEP 2: Verify authorization =====
            $currentUserId = $applicantData['id'];

            if ($contract->applicant_id != $currentUserId) {
                // Log::warning('🔴 SECURITY: Unauthorized approval letter access', [
                //     'contract_owner' => $contract->applicant_id,
                //     'current_user' => $currentUserId,
                //     'ip' => $request->ip()
                // ]);

                Alert()->toast('Unauthorized access', 'error');
                return redirect()->back();
            }

            // ===== STEP 3: Check contract status =====
            if (!in_array($contract->status, ['approved', 'activated', 'expired', 'terminated'])) {
                // Log::warning('Approval letter requested for invalid status', [
                //     'contract_id' => $contractId,
                //     'status' => $contract->status
                // ]);

                Alert()->toast('Approval letter not available for this contract status', 'error');
                return redirect()->back();
            }

            // ===== STEP 4: Get applicant details =====
            $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

            if (!$applicant || $applicant['staff_type'] === 'Unknown') {
                // Log::error('Failed to resolve applicant details', [
                //     'applicant_id' => $contract->applicant_id,
                //     'contract_id' => $contractId
                // ]);
                throw new \Exception('Applicant details not found');
            }

            // ===== STEP 5: Get school details =====
            $school = school::find($contract->school_id);

            if (!$school) {
                // Log::error('School not found', ['school_id' => $contract->school_id]);
                throw new \Exception('School details not found');
            }

            // ===== LOGO PROCESSING =====
            $logoBase64 = null;
            if ($school->logo) {
                $logoPath = storage_path('app/public/logo/' . $school->logo);
                if (file_exists($logoPath)) {
                    $logoBase64 = $this->imageToBase64($logoPath);
                } else {
                    $logoPath = public_path('storage/logo/' . $school->logo);
                    if (file_exists($logoPath)) {
                        $logoBase64 = $this->imageToBase64($logoPath);
                    }
                }
            }

            // ===== STEP 6: Calculate financials =====
            $basicSalary = $contract->basic_salary ?? 0;
            $allowances = $contract->allowances ?? 0;
            $netPay = $basicSalary + $allowances;

            // ===== STEP 7: Generate QR code =====
            $qrImage = $this->generateSecureQRCode($contract);

            // ===== STEP 8: Get authorized person =====
            $authorized = User::find($contract->holder_id);
            $position = $this->getPositionTitle($authorized);

            // ===== STEP 9: Prepare contract data =====
            $contractData = [
                'school_name' => $school->school_name,
                'postal_address' => $school->postal_address,
                'postal_name' => $school->postal_name,
                'country' => $school->country,
                'logo' => $logoBase64,
                'first_name' => $applicant['first_name'] ?? 'Unknown',
                'last_name' => $applicant['last_name'] ?? '',
                'address' => $applicant['address'] ?? $school->postal_address,
                'approved_at' => $contract->approved_at,
                'duration' => $contract->duration,
                'activated_at' => $contract->activated_at,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'contract_type' => $contract->contract_type,
                'basic_salary' => $basicSalary,
                'allowances' => $allowances,
                'net_pay' => $netPay,
                'authorized_person_name' => $contract->approved_by,
                'position' => $position,
                'status' => $contract->status,
                'terminated_at' => $contract->terminated_at,
                'is_active' => $contract->is_active ?? false,
                'verification_token' => $contract->verify_token,
            ];

            // ===== STEP 10: Generate PDF =====
            $pdf = PDF::loadView('Contract.contract_file', [
                'contract' => $contractData,
                'qrImage' => $qrImage
            ]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => false, // Set to false kwa sababu tunatumia base64
                'isHtml5ParserEnabled' => true
            ]);

            // ===== STEP 11: Log success =====
            // Log::info('✅ Approval letter generated successfully', [
            //     'contract_id' => $contractId,
            //     'user_id' => $currentUserId,
            //     'auth_type' => $applicantData['auth_type'],
            //     'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            // ]);

            // ===== STEP 12: Return PDF directly for ALL requests =====
            $filename = 'approval_letter_' . $contractData['first_name'] . '_' . $contractData['last_name'] . '.pdf';

            // Set proper headers for PDF download
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public');
        } catch (\Exception $e) {
            // Log::error('❌ Approval letter generation error', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            //     'id' => $id ?? null,
            //     'ip' => $request->ip() ?? null
            // ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating approval letter: ' . $e->getMessage()
                ], 500);
            }

            Alert()->toast('Error generating approval letter', 'error');
            return redirect()->back();
        }
    }

    private function imageToBase64($path)
    {
        try {
            if (!file_exists($path)) {
                // Log::warning('Image file does not exist', ['path' => $path]);
                return null;
            }

            $imageData = file_get_contents($path);
            if (!$imageData) {
                return null;
            }

            $mimeType = mime_content_type($path);
            return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            // Log::error('Failed to convert image to base64', [
            //     'path' => $path,
            //     'error' => $e->getMessage()
            // ]);
            return null;
        }
    }

    /**
     * Generate secure QR code
     */
    private function generateSecureQRCode($contract)
    {
        try {
            if ($contract->qr_code_path && Storage::disk('public')->exists($contract->qr_code_path)) {
                $qrCodePath = storage_path('app/public/' . $contract->qr_code_path);
                return 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($qrCodePath));
            }

            // Generate secure verification URL
            $verifyToken = $contract->verify_token ?? bin2hex(random_bytes(16));
            $verifyUrl = route('contracts.verify', ['token' => $verifyToken]);

            return 'data:image/svg+xml;base64,' . base64_encode(
                QrCode::format('svg')
                    ->size(200)
                    ->errorCorrection('H')
                    ->generate($verifyUrl)
            );
        } catch (\Exception $e) {
            // Log::error('QR Code generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get position title
     */
    private function getPositionTitle($user)
    {
        if (!$user) return 'School Administrator';

        return match ($user->usertype) {
            1 => 'Head Teacher',
            2 => 'School Manager',
            default => 'School Administrator'
        };
    }

    public function verify($token)
    {
        // Find contract by token
        $contract = school_constracts::where('verify_token', $token)->first();

        if (!$contract) {
            abort(404, 'Contract not found');
        }

        // Get applicant details using our helper function
        $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

        // Get school details
        $school = school::find($contract->school_id);

        // Determine contract status
        $isActive = ($contract->status == 'activated' && now()->lessThan($contract->end_date));
        $isExpired = ($contract->status == 'activated' && now()->greaterThan($contract->end_date));

        // Prepare data for view
        $verificationData = [
            'contract' => $contract,
            'applicant' => $applicant,
            'school' => $school,
            'verified_at' => now(),
            'verification_id' => uniqid('VER-', true),
            'isActive' => $isActive,
            'isExpired' => $isExpired
        ];

        return view('Contract.verify', $verificationData);
    }

    public function terminate(Request $request, $id)
    {
        try {
            $decodedId = Hashids::decode($id)[0];
            $contract = school_constracts::findOrFail($decodedId);

            // Validate - only activated contracts can be terminated
            if ($contract->status != 'activated') {
                Alert()->toast('Only activated contracts can be terminated', 'error');
                return redirect()->back();
            }

            $request->validate([
                'termination_reason' => 'required|string|min:5',
                'termination_type' => 'required|in:mutual,resignation,dismissal,breach,end_of_contract,other',
                'effective_date' => 'required|date',
                'document' => 'nullable|mimes:pdf,doc,docx|max:2048',
            ]);

            // Upload termination document if provided
            $documentPath = null;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = 'termination_' . time() . '_' . $contract->id . '.' . $file->getClientOriginalExtension();
                $documentPath = $file->storeAs('contracts/terminations', $fileName, 'public');
            }

            // Save previous status for history
            $previousStatus = $contract->status;

            // Update main contract - minimal changes
            $contract->update([
                'is_active' => false,
                'status' => 'terminated',
            ]);

            // Save detailed termination info in status history
            $terminationHistory = ContractStatusHistory::create([
                'contract_id' => $contract->id,
                'previous_status' => $previousStatus,
                'new_status' => 'terminated',
                'changed_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'reason' => $request->termination_reason,
                'metadata' => [
                    'termination_type' => $request->termination_type,
                    'effective_date' => $request->effective_date,
                    'document_path' => $documentPath,
                    'terminated_by' => Auth::user()->name,
                    'notes' => $request->notes ?? null,
                    'terminated_at' => now()->toDateTimeString(),
                ]
            ]);

            // Send notification to applicant
            $applicant = $this->resolveApplicantDetails($contract->applicant_id, $contract->school_id);

            if (!empty($applicant['phone'])) {
                try {
                    $nextSmsService = new NextSmsService();
                    $destination = $this->formatPhoneNumber($applicant['phone']);

                    $message = "Habari {$applicant['first_name']}, mkataba wako wa ajira umesitishwa. Sababu: {$request->termination_reason}. Tafadhali wasiliana na ofisi kwa maelezo zaidi.";

                    $nextSmsService->sendSmsByNext(
                        $request->from ?? "SHULE APP",
                        $destination,
                        $message,
                        uniqid()
                    );
                } catch (\Exception $e) {
                    // \Log::warning('Termination SMS failed: ' . $e->getMessage());
                }
            }

            Alert()->toast('Contract terminated successfully', 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            // \Log::error('Termination error: ' . $e->getMessage());
            Alert()->toast('Error terminating contract: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    private function scanFileForViruses($file): array
    {
        // For production, use actual API
        if (app()->environment('production')) {
            $apiKey = config('services.virustotal.key');
            try {
                $response = Http::withHeaders(['x-apikey' => $apiKey])
                    ->attach('file', fopen($file->path(), 'r'))
                    ->post('https://www.virustotal.com/api/v3/files');

                if ($response->successful()) {
                    $scanId = $response->json()['data']['id'];
                    $analysis = Http::withHeaders(['x-apikey' => $apiKey])
                        ->get("https://www.virustotal.com/api/v3/analyses/{$scanId}");

                    return [
                        'clean' => $analysis->json()['data']['attributes']['stats']['malicious'] === 0,
                        'message' => $analysis->json()['data']['attributes']['status']
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'clean' => false,
                    'message' => 'Scan failed: ' . $e->getMessage()
                ];
            }
        }

        // For local development, just mock a successful scan
        return ['clean' => true, 'message' => 'Development mode - scan bypassed'];
    }

    /**
     * Get detailed profile information for the authenticated user
     */
    public function getProfileDetails(Request $request)
    {
        $applicant = $this->getCurrentApplicant($request);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $applicantId = $applicant['id'];
        $applicantDetails = $applicant['details'];
        $authType = $applicant['auth_type'];

        $profileData = [];

        // Get additional details based on staff type
        if ($applicantDetails['staff_type'] === 'Teacher') {
            $teacherDetails = DB::table('teachers')
                ->where('user_id', $applicantDetails['user_id'] ?? null)
                ->first();

            if ($teacherDetails) {
                $profileData = array_merge($profileData, [
                    'nida' => $teacherDetails->nida ?? null,
                    'bank_account_number' => $teacherDetails->bank_account_number ?? null,
                    'bank_account_name' => $teacherDetails->bank_account_name ?? null,
                    'bank_name' => $teacherDetails->bank_name ?? null,
                    'alternative_phone' => $teacherDetails->alternative_phone ?? null,
                    'form_four_index_number' => $teacherDetails->form_four_index_number ?? null,
                    'form_four_completion_year' => $teacherDetails->form_four_completion_year ?? null,
                    'dob' => $teacherDetails->dob ?? null,
                    'qualification' => $teacherDetails->qualification ?? null,
                    'address' => $teacherDetails->address ?? null,
                    'joined' => $teacherDetails->joined ?? null,
                    'member_id' => $teacherDetails->member_id ?? null,
                ]);
            }
        } elseif ($applicantDetails['staff_type'] === 'Transport Staff') {
            $transportDetails = DB::table('transports')
                ->where('id', $applicantDetails['staff_table_id'] ?? null)
                ->first();

            if ($transportDetails) {
                $profileData = array_merge($profileData, [
                    'nida' => $transportDetails->nida ?? null,
                    'bank_account_number' => $transportDetails->bank_account_number ?? null,
                    'bank_account_name' => $transportDetails->bank_account_name ?? null,
                    'bank_name' => $transportDetails->bank_name ?? null,
                    'alternative_phone' => $transportDetails->alternative_phone ?? null,
                    'bus_no' => $transportDetails->bus_no ?? null,
                    'driver_name' => $transportDetails->driver_name ?? null,
                    'routine' => $transportDetails->routine ?? null,
                ]);
            }
        } elseif ($applicantDetails['staff_type'] === 'Transport Staff') {
            // Already handled above
        } else {
            // Other staff
            $otherStaffDetails = DB::table('other_staffs')
                ->where('id', $applicantDetails['staff_table_id'] ?? null)
                ->first();

            if ($otherStaffDetails) {
                $profileData = array_merge($profileData, [
                    'nida' => $otherStaffDetails->nida ?? null,
                    'bank_account_number' => $otherStaffDetails->bank_account_number ?? null,
                    'bank_account_name' => $otherStaffDetails->bank_account_name ?? null,
                    'bank_name' => $otherStaffDetails->bank_name ?? null,
                    'alternative_phone' => $otherStaffDetails->alternative_phone ?? null,
                    'job_title' => $otherStaffDetails->job_title ?? null,
                    'joining_year' => $otherStaffDetails->joining_year ?? null,
                    'educational_level' => $otherStaffDetails->educational_level ?? null,
                    'street_address' => $otherStaffDetails->street_address ?? null,
                    'date_of_birth' => $otherStaffDetails->date_of_birth ?? null,
                ]);
            }
        }

        // Get user details
        if (!empty($applicantDetails['user_id'])) {
            $userDetails = DB::table('users')
                ->where('id', $applicantDetails['user_id'])
                ->first();

            if ($userDetails) {
                $profileData = array_merge($profileData, [
                    'email' => $userDetails->email ?? $applicantDetails['email'],
                    'profile_image' => $userDetails->image ?? $applicantDetails['profile_image'],
                    'user_status' => $userDetails->status ?? null,
                    'usertype' => $userDetails->usertype ?? null,
                ]);
            }
        }

        // Merge with existing applicant details
        $fullProfile = array_merge($applicantDetails, $profileData);

        return response()->json([
            'success' => true,
            'data' => $fullProfile
        ]);
    }
}
