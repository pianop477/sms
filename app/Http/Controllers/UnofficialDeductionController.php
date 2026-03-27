<?php

namespace App\Http\Controllers;

use App\Traits\ResolveApplicantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class UnofficialDeductionController extends Controller
{
    //
    use ResolveApplicantTrait;
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.finance_api_base_url', 'http://localhost:8000/api/v1.0');
    }

    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $token = session('finance_api_token');

        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->get($this->apiBaseUrl . '/deductions/unofficial', [
                    'school_id' => $schoolId
                ]);

            $deductionsData = $response->successful() ? $response->json()['data'] : [];

            // Enrich deductions with employee details using trait
            $deductions = [
                'pending' => [],
                'history' => []
            ];

            // Process pending deductions
            foreach ($deductionsData['pending'] ?? [] as $deduction) {
                // Get employee details using staff_id
                $staffDetails = $this->resolveApplicantDetails($deduction['staff_id'], $schoolId);

                $deductions['pending'][] = [
                    'id' => $deduction['id'],
                    'staff_id' => $deduction['staff_id'],
                    'employee_name' => ($staffDetails['first_name'] ?? '') . ' ' . ($staffDetails['last_name'] ?? ''),
                    'staff_type' => $staffDetails['staff_type'] ?? $deduction['staff_type'],
                    'deduction_type' => $deduction['deduction_type'],
                    'description' => $deduction['description'],
                    'amount' => $deduction['amount'],
                    'is_recurring' => $deduction['is_recurring'],
                    'recurring_months' => $deduction['recurring_months'],
                    'remaining_months' => $deduction['remaining_months'],
                    'reference_number' => $deduction['reference_number'],
                    'created_at' => $deduction['created_at'],
                    'created_by' => $deduction['created_by']
                ];
            }

            // Process history deductions (already deducted)
            foreach ($deductionsData['history'] ?? [] as $deduction) {
                $staffDetails = $this->resolveApplicantDetails($deduction['staff_id'], $schoolId);

                $deductions['history'][] = [
                    'id' => $deduction['id'],
                    'staff_id' => $deduction['staff_id'],
                    'employee_name' => ($staffDetails['first_name'] ?? '') . ' ' . ($staffDetails['last_name'] ?? ''),
                    'staff_type' => $staffDetails['staff_type'] ?? 'N/A',
                    'deduction_type' => $deduction['deduction_type'],
                    'description' => $deduction['description'],
                    'amount' => $deduction['amount'],
                    'deducted_at' => $deduction['deducted_at'],
                    'payroll_month' => $deduction['payroll_month']
                ];
            }

            // Log for debugging
            // Log::info('Unofficial Deductions processed', [
            //     'pending_count' => count($deductions['pending']),
            //     'history_count' => count($deductions['history'])
            // ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch unofficial deductions', [
                'error' => $e->getMessage()
            ]);
            $deductions = ['pending' => [], 'history' => []];
            session()->flash('error', 'Could not connect to finance system');
        }

        return view('staff-loans.index', compact('deductions'));
    }

    /**
     * Store new deduction (loan, advance, etc.)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string',
            'staff_type' => 'required|string|in:Teacher,Transport Staff,Other Staff',
            'deduction_type' => 'required|in:loan,advance,penalty,fine,other',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:1000',
            'is_recurring' => 'boolean',
            'recurring_months' => 'required_if:is_recurring,true|nullable|integer|min:1', // Add nullable
            'authorization_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $schoolId = Auth::user()->school_id;
        $token = session('finance_api_token');

        // Check if token exists
        if (!$token) {
            Log::error('Finance API token missing', [
                'user_id' => Auth::id(),
                'school_id' => $schoolId
            ]);
            Alert()->toast('Finance system not connected. Please login to finance system.', 'error');
            return redirect()->back()->withInput();
        }

        // Auto-generate reference number
        $referenceNumber = $this->generateReferenceNumber($schoolId, $request->deduction_type);

        // Log::info('Preparing to send deduction to API', [
        //     'school_id' => $schoolId,
        //     'staff_id' => $request->staff_id,
        //     'reference_number' => $referenceNumber,
        //     'api_url' => $this->apiBaseUrl . '/deductions/staff-loan/store'
        // ]);

        try {
            $payload = [
                'school_id' => $schoolId,
                'staff_id' => $request->staff_id,
                'staff_type' => $request->staff_type,
                'deduction_type' => $request->deduction_type,
                'description' => $request->description,
                'amount' => $request->amount,
                'reference_number' => $referenceNumber,
                'is_recurring' => $request->has('is_recurring') ? true : false,
                'recurring_months' => $request->recurring_months,
                'authorized_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'authorization_notes' => $request->authorization_notes
            ];

            // Log::info('Sending payload to API', $payload);

            $response = Http::withToken($token)
                ->timeout(30)
                ->post($this->apiBaseUrl . '/deductions/unofficial', $payload);

            // Log full response for debugging
            // Log::info('API Response', [
            //     'status' => $response->status(),
            //     'body' => $response->body(),
            //     'successful' => $response->successful()
            // ]);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Deduction added successfully', $responseData);
                Alert()->toast($responseData['message'] ?? 'Loan deduction added successfully', 'success');
                return redirect()->route('deductions.unofficial');
            }

            // Log error response
            $errorMessage = $response->json()['message'] ?? 'Failed to add deduction';
            $errorDetails = $response->json()['errors'] ?? [];

            Log::error('API Error Response', [
                'status' => $response->status(),
                'message' => $errorMessage,
                'errors' => $errorDetails,
                'full_response' => $response->body()
            ]);

            Alert()->toast($errorMessage, 'error');
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            Log::error('Exception storing deduction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Alert()->toast('Could not connect to finance system. Please try again later. Error: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Generate unique reference number for deduction
     * Format: DED-YYYYMMDD-XXXX (where XXXX is random or sequential)
     */
    private function generateReferenceNumber($schoolId, $deductionType)
    {
        $prefix = strtoupper(substr($deductionType, 0, 2));
        $date = date('Ymd-His');
        $random = strtoupper(substr(uniqid(), -6));

        // Format: LN-20250327-001 (Loan)
        // Format: AD-20250327-001 (Advance)
        $typeCode = [
            'loan' => 'LN',
            'advance' => 'AD',
            'penalty' => 'PN',
            'fine' => 'FN',
            'other' => 'OT'
        ];

        $code = $typeCode[$deductionType] ?? 'DD';

        return $code . '-' . $date;
    }

    /**
     * Cancel pending deduction
     */
    public function cancel($id)
    {
        $token = session('finance_api_token');

        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->post($this->apiBaseUrl . '/deductions/staff-loan/' . $id . '/cancel');

            if ($response->successful()) {
                Alert()->toast($response->json()['message'] ?? 'Loan deduction cancelled successfully', 'success');
                return redirect()->route('staff.loans.index');
            }

            $errorMessage = $response->json()['message'] ?? 'Failed to cancel deduction';
            Alert()->toast($errorMessage, 'error');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Exception cancelling deduction', [
                'deduction_id' => $id,
                'error' => $e->getMessage()
            ]);

            Alert()->toast();
            return redirect()->back($e->getMessage(), 'error');
        }
    }
}
