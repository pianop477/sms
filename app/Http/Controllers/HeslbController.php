<?php
// app/Http/Controllers/HeslbController.php (Frontend)

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ResolveApplicantTrait;

class HeslbController extends Controller
{
    use ResolveApplicantTrait;

    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.finance_api_base_url', 'http://localhost:8000/api/v1.0');
    }

    /**
     * Display HESLB deductions list
     */
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->get($this->apiBaseUrl . '/heslb/deductions', [
                    'school_id' => $schoolId
                ]);

            $deductionsData = $response->successful() ? $response->json()['data'] : [];

            // ✅ Enrich deductions with employee details using trait
            $deductions = [
                'active' => [],
                'inactive' => []
            ];

            // Process active deductions
            foreach ($deductionsData['active'] ?? [] as $deduction) {
                // Get employee details using staff_id
                $staffDetails = $this->resolveApplicantDetails($deduction['staff_id'], $schoolId);

                $deductions['active'][] = [
                    'id' => $deduction['id'],
                    'staff_id' => $deduction['staff_id'],
                    'employee_name' => ($staffDetails['first_name'] ?? '') . ' ' . ($staffDetails['last_name'] ?? ''),
                    'staff_type' => $staffDetails['staff_type'] ?? $deduction['staff_type'],
                    'loan_number' => $deduction['loan_number'],
                    'monthly_amount' => $deduction['monthly_amount'],
                    'start_date' => $deduction['start_date'],
                    'end_date' => $deduction['end_date'],
                    'is_active' => $deduction['is_active'],
                    'created_at' => $deduction['created_at'],
                    'created_by' => $deduction['created_by']
                ];
            }

            // Process inactive deductions
            foreach ($deductionsData['inactive'] ?? [] as $deduction) {
                $staffDetails = $this->resolveApplicantDetails($deduction['staff_id'], $schoolId);

                $deductions['inactive'][] = [
                    'id' => $deduction['id'],
                    'staff_id' => $deduction['staff_id'],
                    'employee_name' => ($staffDetails['first_name'] ?? '') . ' ' . ($staffDetails['last_name'] ?? ''),
                    'staff_type' => $staffDetails['staff_type'] ?? $deduction['staff_type'],
                    'loan_number' => $deduction['loan_number'],
                    'monthly_amount' => $deduction['monthly_amount'],
                    'start_date' => $deduction['start_date'],
                    'end_date' => $deduction['end_date'],
                    'is_active' => $deduction['is_active'],
                    'created_at' => $deduction['created_at'],
                    'created_by' => $deduction['created_by']
                ];
            }

            // Log for debugging
            // Log::info('HESLB Deductions processed', [
            //     'active_count' => count($deductions['active']),
            //     'inactive_count' => count($deductions['inactive'])
            // ]);

        } catch (\Exception $e) {
            $deductions = ['active' => [], 'inactive' => []];
            // Log::error('Failed to fetch HESLB deductions', ['error' => $e->getMessage()]);
        }

        return view('heslb.index', compact('deductions'));
    }

    /**
     * Store new HESLB deduction
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string',
            'staff_type' => 'required|in:Teacher,Transport Staff,Other Staff',
            'loan_number' => 'nullable|string',
            'monthly_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string'
        ]);

        // ✅ Validate that employee exists using trait
        $schoolId = Auth::user()->school_id;
        $employeeDetails = $this->resolveApplicantDetails($request->staff_id, $schoolId);

        if ($employeeDetails['staff_type'] === 'Unknown') {
            Alert()->toast('Employee not found. Please check Staff ID.', 'error')->withInput();
            return back();
        }

        $data = [
            'school_id' => $schoolId,
            'staff_id' => $request->staff_id,
            'staff_type' => $request->staff_type,
            'loan_number' => $request->loan_number,
            'monthly_amount' => $request->monthly_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'created_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name
        ];

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->post($this->apiBaseUrl . '/heslb/deductions', $data);

            if (!$response->successful()) {
                $errorMsg = $response->json()['message'] ?? 'Failed to add HESLB deduction';
                Alert()->toast($errorMsg, 'error')->withInput();
                return back()->with('error', );
            }

            Alert()->toast('HESLB deduction added successfully', 'success');
            return redirect()->route('heslb.index');

        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error')->withInput();
            return back();
        }
    }

    /**
     * Stop HESLB deduction
     */
    public function stop($id)
    {
        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->post($this->apiBaseUrl . '/heslb/deductions/' . $id . '/stop');

            if (!$response->successful()) {
                Alert()->toast($response->json()['message'] ?? 'Failed to stop deduction', 'error');
                return back();
            }

            return back();

        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * Update monthly amount
     */
    public function updateAmount(Request $request, $id)
    {
        $request->validate([
            'monthly_amount' => 'required|numeric|min:0'
        ]);

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->put($this->apiBaseUrl . '/heslb/deductions/' . $id, [
                    'monthly_amount' => $request->monthly_amount
                ]);

            if (!$response->successful()) {
                Alert()->toast($response->json()['message'] ?? 'Failed to update amount', 'error');
                return back();
            }

            Alert()->toast('Monthly amount updated successfully', 'success');
            return back();

        } catch (\Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }
}
