<?php
// app/Http/Controllers/PayrollController.php (ShuleApp)

namespace App\Http\Controllers;

use App\Imports\PayrollImport;
use App\Models\school_constracts;
use App\Traits\HashIdTrait;
use App\Traits\ResolveApplicantTrait;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    use ResolveApplicantTrait;
    use HashIdTrait;

    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = Config::get('app.finance_api_base_url', 'http://localhost:8000/api/v1.0');
    }

    /**
     * GET EMPLOYEES WITH ACTIVE CONTRACTS
     * Hii inarudisha data ya wafanyakazi kwa ajili ya frontend preview
     */
    public function getEmployeesWithContracts(Request $request)
    {
        $schoolId = $request->input('school_id', Auth::user()->school_id);
        $staffTypeFilter = $request->input('staff_type', '');
        $departmentFilter = $request->input('department', '');

        $contracts = school_constracts::where('school_id', $schoolId)
            ->whereIn('status', ['activated', 'approved'])
            ->whereNull('terminated_at')
            ->orderBy('approved_at', 'desc')
            ->get();

        $employees = [];

        foreach ($contracts as $contract) {
            $staffId = $contract->applicant_id;
            if (!$staffId) continue;

            $staffDetails = $this->resolveApplicantDetails($staffId, $schoolId);
            if ($staffDetails['staff_type'] === 'Unknown') continue;

            if ($staffTypeFilter && $this->mapStaffType($staffDetails['staff_type']) !== $staffTypeFilter) continue;
            if ($departmentFilter && $contract->job_title !== $departmentFilter) continue;

            $employees[] = [
                'staff_id' => $staffDetails['staff_id'],
                'employee_name' => $staffDetails['first_name'] . ' ' . ($staffDetails['last_name'] ?? ''),
                'staff_type' => $this->mapStaffType($staffDetails['staff_type']),
                'basic_salary' => (float) $contract->basic_salary,
                'allowances' => $contract->allowances ? json_decode($contract->allowances, true) : [],
                'contract_type' => $contract->contract_type ?? 'new',
                'department' => $contract->job_title ?? null,
                'bank_name' => $staffDetails['bank_name'] ?? null,
                'bank_account_name' => $staffDetails['bank_account_name'] ?? null,
                'bank_account_number' => $staffDetails['bank_account_number'] ?? null,
                'phone' => $staffDetails['phone'] ?? null,
                'email' => $staffDetails['email'] ?? null,
                'nida' => $staffDetails['nida'] ?? null,
                'address' => $staffDetails['address'] ?? null,
                'qualification' => $staffDetails['qualification'] ?? null
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $employees,
            'total' => count($employees)
        ]);
    }


    /**
     * ========================================================================
     * SEARCH EMPLOYEES (For Manual Entry)
     * ========================================================================
     */
    public function searchEmployees(Request $request)
    {
        $schoolId = $request->input('school_id', Auth::user()->school_id);
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['success' => true, 'data' => [], 'message' => 'Enter at least 2 characters']);
        }

        $employees = [];

        // Search teachers
        $teachers = DB::table('teachers')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->where('teachers.school_id', $schoolId)
            ->where('teachers.status', 1)
            ->where('users.status', 1)
            ->where(function ($q) use ($query) {
                $q->where('teachers.member_id', 'LIKE', "%{$query}%")
                    ->orWhere('users.first_name', 'LIKE', "%{$query}%")
                    ->orWhere('users.last_name', 'LIKE', "%{$query}%");
            })
            ->select('users.first_name', 'users.last_name', 'teachers.member_id as staff_id', DB::raw("'teacher' as staff_type"))
            ->limit(10)
            ->get();

        foreach ($teachers as $teacher) {
            $contract = school_constracts::where('applicant_id', $teacher->staff_id)
                ->where('school_id', $schoolId)
                ->whereIn('status', ['activated', 'approved'])
                ->first();

            $employees[] = [
                'staff_id' => $teacher->staff_id,
                'employee_name' => $teacher->first_name . ' ' . ($teacher->last_name ?? ''),
                'staff_type' => 'teacher',
                'basic_salary' => $contract ? (float) $contract->basic_salary : 0,
                'allowances' => $contract && $contract->allowances ? json_decode($contract->allowances, true) : [],
                'has_contract' => $contract ? true : false
            ];
        }

        // Search transports
        $transports = DB::table('transports')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->where(function ($q) use ($query) {
                $q->where('staff_id', 'LIKE', "%{$query}%")
                    ->orWhere('driver_name', 'LIKE', "%{$query}%");
            })
            ->select('driver_name as first_name', DB::raw("'' as last_name"), 'staff_id', DB::raw("'transport' as staff_type"))
            ->limit(10)
            ->get();

        foreach ($transports as $transport) {
            $contract = school_constracts::where('applicant_id', $transport->staff_id)
                ->where('school_id', $schoolId)
                ->whereIn('status', ['activated', 'approved'])
                ->first();

            $employees[] = [
                'staff_id' => $transport->staff_id,
                'employee_name' => $transport->first_name,
                'staff_type' => 'transport',
                'basic_salary' => $contract ? (float) $contract->basic_salary : 0,
                'allowances' => $contract && $contract->allowances ? json_decode($contract->allowances, true) : [],
                'has_contract' => $contract ? true : false
            ];
        }

        // Search other staff
        $otherStaff = DB::table('other_staffs')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->where(function ($q) use ($query) {
                $q->where('staff_id', 'LIKE', "%{$query}%")
                    ->orWhere('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%");
            })
            ->select('first_name', 'last_name', 'staff_id', DB::raw("'other_staff' as staff_type"))
            ->limit(10)
            ->get();

        foreach ($otherStaff as $staff) {
            $contract = school_constracts::where('applicant_id', $staff->staff_id)
                ->where('school_id', $schoolId)
                ->whereIn('status', ['activated', 'approved'])
                ->first();

            $employees[] = [
                'staff_id' => $staff->staff_id,
                'employee_name' => $staff->first_name . ' ' . ($staff->last_name ?? ''),
                'staff_type' => 'other_staff',
                'basic_salary' => $contract ? (float) $contract->basic_salary : 0,
                'allowances' => $contract && $contract->allowances ? json_decode($contract->allowances, true) : [],
                'has_contract' => $contract ? true : false
            ];
        }

        $employees = collect($employees)->unique('staff_id')->values()->all();

        return response()->json([
            'success' => true,
            'data' => $employees,
            'total' => count($employees)
        ]);
    }

    /**
     * ========================================================================
     * GET STAFF DETAILS BY TABLE ID
     * ========================================================================
     */
    private function getStaffDetailsById($sourceTableId, $schoolId)
    {
        // Tafuta kwa source_table_id kupata staff_id
        $teacher = DB::table('teachers')
            ->where('id', $sourceTableId)
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->first();

        if ($teacher) {
            return $this->resolveApplicantDetails($teacher->member_id, $schoolId);
        }

        $transport = DB::table('transports')
            ->where('id', $sourceTableId)
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->first();

        if ($transport) {
            return $this->resolveApplicantDetails($transport->staff_id, $schoolId);
        }

        $otherStaff = DB::table('other_staffs')
            ->where('id', $sourceTableId)
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->first();

        if ($otherStaff) {
            return $this->resolveApplicantDetails($otherStaff->staff_id, $schoolId);
        }

        return null;
    }

    /**
     * ========================================================================
     * GET ACTIVE CONTRACT FOR STAFF
     * ========================================================================
     */
    private function getActiveContractForStaff($staffId, $schoolId)
    {
        // Contract inatafutwa kwa applicant_id (ambayo ni staff_id)
        $contract = school_constracts::where('school_id', $schoolId)
            ->where('applicant_id', $staffId)  // HAPA: applicant_id ndio staff_id
            ->whereIn('status', ['activated', 'approved'])
            ->whereNull('terminated_at')
            ->orderBy('approved_at', 'desc')
            ->first();

        return $contract;
    }

    /**
     * ========================================================================
     * MAP STAFF TYPE
     * ========================================================================
     */
    private function mapStaffType($staffType): string
    {
        $map = ['Teacher' => 'teacher', 'Transport Staff' => 'transport', 'Other Staff' => 'other_staff'];
        return $map[$staffType] ?? 'other_staff';
    }

    /**
     * ========================================================================
     * 1. DISPLAY ALL PAYROLL BATCHES
     * ========================================================================
     * GET /payroll
     */
    public function index(Request $request)
    {
        return view('payroll.index');
    }

    // app/Http/Controllers/PayrollController.php (ShuleApp)

    /**
     * SHOW CREATE PAYROLL FORM
     * URL: GET /payroll/create
     */
    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $previousSchedules = [];

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->get($this->apiBaseUrl . '/payroll/schedules', [
                    'school_id' => $schoolId
                ]);

            if ($response->successful()) {
                $previousSchedules = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch previous schedules', [
                'error' => $e->getMessage(),
                'school_id' => $schoolId
            ]);
        }

        return view('payroll.create', compact('previousSchedules'));
    }

    /**
     * ========================================================================
     * 3. GENERATE NEW PAYROLL
     * ========================================================================
     * POST /payroll/generate
     */

    public function generate(Request $request)
    {
        $rules = [
            'month' => 'required|date_format:Y-m',
            'generation_method' => 'required|in:contracts,excel_upload,previous_batch,manual_entry',
            'previous_batch_id' => 'required_if:generation_method,previous_batch',
            'excel_file' => 'required_if:generation_method,excel_upload|file|mimes:xlsx,xls,csv',
        ];

        if ($request->generation_method === 'contracts') {
            $rules['contracts_data'] = 'required|json';
        }

        if ($request->generation_method === 'manual_entry' && $request->has('manual_employees') && !is_null($request->manual_employees)) {
            $rules['manual_employees'] = 'json';
        }

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return back()->with('error', 'Validation failed: ' . implode(', ', $validator->errors()->all()))->withInput();
        }

        $schoolId = Auth::user()->school_id;
        $generatedBy = Auth::user()->first_name . ' ' . Auth::user()->last_name;

        $data = [
            'school_id' => $schoolId,
            'month' => $request->month,
            'generation_method' => $request->generation_method,
            'generated_by' => $generatedBy,
            'filters' => $request->only(['department', 'staff_type'])
        ];

        // Handle contracts data
        if ($request->generation_method === 'contracts' && $request->has('contracts_data')) {
            try {
                $selectedEmployees = json_decode($request->contracts_data, true);
                $data['selected_employees'] = $selectedEmployees;
                $data['use_frontend_selection'] = true;
            } catch (\Exception $e) {
                return back()->with('error', 'Invalid contracts data format')->withInput();
            }
        }

        // Handle manual employees
        if ($request->generation_method === 'manual_entry' && $request->has('manual_employees') && !is_null($request->manual_employees)) {
            try {
                $manualEmployees = json_decode($request->manual_employees, true);
                $data['manual_employees'] = $manualEmployees;
            } catch (\Exception $e) {
                return back()->with('error', 'Invalid manual employees data')->withInput();
            }
        }

        // Handle previous batch
        if ($request->generation_method === 'previous_batch' && $request->has('previous_batch_id') && $request->previous_batch_id) {
            $data['previous_batch_id'] = $request->previous_batch_id;
        }

        // Handle Excel file
        if ($request->generation_method === 'excel_upload' && $request->hasFile('excel_file')) {
            $data['excel_data'] = $this->parseExcelFile($request->file('excel_file'));
        }

        $token = session('finance_api_token');

        if (empty($token)) {
            return back()->with('error', 'Authentication token missing. Please login again.')->withInput();
        }

        $fullUrl = $this->apiBaseUrl . '/payroll/generate';

        try {
            $response = Http::withToken($token)
                ->timeout(60)
                ->post($fullUrl, $data);

            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Unknown error';
                return back()->with('error', 'API Error: ' . $errorMessage)->withInput();
            }

            Alert()->toast('Payroll generated successfully', 'success');
            return redirect()->route('payroll.index');
        } catch (ConnectionException $e) {
            return back()->with('error', 'Cannot connect to Finance API. Please check if the service is running on port 8000.')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Unexpected error: ' . $e->getMessage())->withInput();
        }
    }

    private function parseExcelFile($file)
    {
        $import = new PayrollImport();
        Excel::import($import, $file);

        $data = $import->getData();

        // Filter out rows without staff_id
        $data = array_filter($data, function ($row) {
            return !empty($row['staff_id']);
        });

        // Reindex array
        $data = array_values($data);

        return $data;
    }

    /**
     * ========================================================================
     * 4. SHOW PAYROLL DETAILS
     * ========================================================================
     * GET /payroll/{id}
     */
    public function show($hash)
    {
        // Decrypt hash
        $id = $this->decryptId($hash);

        if (!$id) {
            Log::warning('Payroll Show - Invalid hash', ['hash' => $hash]);
            Alert()->toast('Invalid payroll link. Please check the URL.', 'error');
            return redirect()->route('payroll.index');
        }

        try {
            // Call API
            $apiUrl = $this->apiBaseUrl . '/payroll/batches/' . $id;
            // Log::info('Payroll Show - Calling API', ['url' => $apiUrl]);

            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->get($apiUrl);

            if (!$response->successful()) {
                $errorMsg = $response->json()['message'] ?? 'Payroll not found';
                Alert()->toast($errorMsg, 'error');
                return redirect()->route('payroll.index');
            }

            $data = $response->json()['data'];
            $batch = $data['batch'];
            $summary = $data['summary'];

            // ENRICH EMPLOYEES WITH FULL NAMES FROM LOCAL DATABASE
            $batch['payroll_employees'] = $this->enrichEmployeesWithDetails(
                $batch['payroll_employees'] ?? [],
                Auth::user()->school_id
            );

            // Add hash to batch for view
            $batch['hash'] = $hash;
        } catch (\Exception $e) {
            Log::error('Payroll Show - Exception', [
                'batch_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Alert()->toast('Connection error: ' . $e->getMessage(), 'error');
            return redirect()->route('payroll.index');
        }

        return view('payroll.show', compact('batch', 'summary'));
    }

    private function enrichEmployeesWithDetails($employees, $schoolId)
    {
        if (empty($employees)) {
            return [];
        }

        $enrichedEmployees = [];

        foreach ($employees as $employee) {
            // Get employee details using staff_id from local database
            $staffDetails = $this->resolveApplicantDetails($employee['staff_id'], $schoolId);

            // Merge original employee data with enriched details
            $enrichedEmployees[] = array_merge($employee, [
                'employee_full_name' => $staffDetails['first_name'] . ' ' . $staffDetails['last_name'],
                'staff_type' => $staffDetails['staff_type'] ?? $employee['staff_type'],
                'first_name' => $staffDetails['first_name'],
                'last_name' => $staffDetails['last_name'],
                'staff_table_id' => $staffDetails['staff_table_id'] ?? null,
                'user_id' => $staffDetails['user_id'] ?? null
            ]);
        }

        return $enrichedEmployees;
    }

    /**
     * ========================================================================
     * 5. CALCULATE PAYROLL
     * ========================================================================
     * POST /payroll/{id}/calculate
     */
    public function calculate($hash)
    {
        $id = $this->decryptId($hash);

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid payroll link']);
        }

        // ✅ SAHIHISHA URL - Ongeza /batches/
        $url = $this->apiBaseUrl . '/payroll/batches/' . $id . '/calculate';

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(60)
                ->post($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Calculation failed'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payroll calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($hash)
    {
        $id = $this->decryptId($hash);

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid payroll link']);
        }

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(60)
                ->delete($this->apiBaseUrl . '/payroll/batches/' . $id);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Delete failed'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payroll deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * ========================================================================
     * 6. FINALIZE PAYROLL
     * ========================================================================
     * POST /payroll/{id}/finalize
     */
    public function finalize($hash)
    {
        $id = $this->decryptId($hash);

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid payroll link']);
        }

        // ✅ SAHIHISHA URL - Ongeza /batches/
        $url = $this->apiBaseUrl . '/payroll/batches/' . $id . '/finalize';

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(60)
                ->post($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Finalization failed'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payroll finalized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * ========================================================================
     * 7. GENERATE SALARY SLIPS
     * ========================================================================
     * POST /payroll/{id}/generate-slips
     */
    public function generateSlips($hash)
    {
        $id = $this->decryptId($hash);

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid payroll link']);
        }

        $userId = Auth::id();

        $school = Auth::user()->school;
        $schoolInfo = [
            'name' => $school->school_name ?? config('app.name'),
            'logo' => asset('storage/' . $school->logo),
            'email' => $school->school_email ?? null,
            'phone' => $school->school_phone ?? null,
            'address' => $school->postal_addres ?? null,
            'address_name' => $school->postal_name ?? null,
            'country' => $school->country ?? null,
            'registration_no' => $school->school_reg_no ?? null,
            'abbriv_code' => $school->abbriv_code ?? null
        ];

        // ✅ SAHIHISHA URL - Ongeza /batches/
        $url = $this->apiBaseUrl . '/payroll/batches/' . $id . '/generate-slips';
        // Log::info('Calling API URL:', ['url' => $url]);

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(120)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($url, [
                    'generated_by' => $userId,
                    'school_info' => $schoolInfo
                ]);

            // Log::info('API Response:', [
            //     'status' => $response->status(),
            //     'body' => $response->body()
            // ]);

            if ($response->failed()) {
                $errorMsg = $response->json()['message'] ?? 'Unknown error';
                return response()->json(['success' => false, 'message' => $errorMsg]);
            }

            $data = $response->json();

            if ($data['success']) {
                return response()->json(['success' => true, 'message' => 'Salary slips generated successfully']);
            }

            return response()->json(['success' => false, 'message' => $data['message'] ?? 'Generation failed']);
        } catch (ConnectionException $e) {
            return response()->json(['success' => false, 'message' => 'Cannot connect to Finance API: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    /**
     * ========================================================================
     * 8. DOWNLOAD PAYROLL SUMMARY PDF
     * ========================================================================
     * GET /payroll/{id}/download-summary
     */
    public function downloadSummary($hash)
    {
        $id = $this->decryptId($hash);

        if (!$id) {
            Alert()->toast('Invalid payroll link. Please check the URL.', 'error');
            return back();
        }

        // ✅ SAHIHISHA URL - Ongeza /batches/
        $url = $this->apiBaseUrl . '/payroll/batches/' . $id . '/download-summary';

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(60)
                ->get($url);

            if (!$response->successful()) {
                Alert()->toast('Summary not found', 'error');
                return back();
            }

            $date = now();
            $filename = "payroll_{$date}.xlsx";

            return response($response->body())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Alert()->toast('Connection error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * ========================================================================
     * 9. DOWNLOAD COMBINED SALARY SLIPS PDF
     * ========================================================================
     * GET /payroll/{id}/download-slips
     */
    public function downloadSlips($hash)
    {
        $id = $this->decryptId($hash);

        if (!$id) {
            Alert()->toast('Invalid payroll link. Please check the URL.', 'error');
            return back();
        }

        // ✅ SAHIHISHA URL - Ongeza /batches/
        $url = $this->apiBaseUrl . '/payroll/batches/' . $id . '/download-slips';

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(120)
                ->get($url);

            if (!$response->successful()) {
                Alert()->toast($response->json()['message'] ?? 'Unknown error', 'error');
                return back();
            }

            $date = now();
            $filename = "salary_slips_{$date}.pdf";

            return response($response->body())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Alert()->toast('Connection error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * ========================================================================
     * 10. EMPLOYEE DETAIL IN PAYROLL
     * ========================================================================
     * GET /payroll/{batchId}/employee/{employeeId}
     */
    public function employeeDetail($batchHash, $employeeHash)
    {
        $batchId = $this->decryptId($batchHash);
        $employeeId = $this->decryptId($employeeHash);

        if (!$batchId || !$employeeId) {
            return back()->with('error', 'Invalid link');
        }

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->get($this->apiBaseUrl . '/payroll/batches/' . $batchId . '/employee/' . $employeeId);

            if (!$response->successful()) {
                return back()->with('error', 'Employee details not found');
            }

            $employeeData = $response->json()['data'];
        } catch (\Exception $e) {
            return back()->with('error', 'Connection error: ' . $e->getMessage());
        }

        return view('payroll.employee-detail', compact('employeeData', 'batchId', 'batchHash'));
    }

    /**
     * ========================================================================
     * 11. EMPLOYEE STATEMENTS LIST / FORM
     * ========================================================================
     * GET /payroll/statements
     */
    public function statements(Request $request)
    {
        return view('payroll.statements');
    }

    /**
     * ========================================================================
     * 12. PAYROLL REPORTS
     * ========================================================================
     * GET /reports/payroll
     */
    public function reports(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $year = $request->year ?? date('Y');

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->get($this->apiBaseUrl . '/payroll/batches', [
                    'school_id' => $schoolId,
                    'year' => $year,
                    'status' => 'finalized',
                    'per_page' => 100
                ]);

            $batches = $response->successful() ? $response->json()['data'] : [];
        } catch (\Exception $e) {
            $batches = [];
        }

        return view('payroll.reports', compact('batches', 'year'));
    }

    /**
     * ========================================================================
     * 13. YEARLY PAYROLL REPORT
     * ========================================================================
     * GET /reports/payroll/yearly
     */
    public function yearlyReport(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $year = $request->year ?? date('Y');

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->get($this->apiBaseUrl . '/reports/yearly', [
                    'school_id' => $schoolId,
                    'year' => $year
                ]);

            $report = $response->successful() ? $response->json()['data'] : [];
        } catch (\Exception $e) {
            $report = [];
        }

        return view('payroll.yearly-report', compact('report', 'year'));
    }

    public function getScheduleDetails(Request $request)
    {
        $batchId = $request->query('batch_id');

        if (!$batchId) {
            return response()->json([
                'success' => false,
                'message' => 'Batch ID required'
            ], 400);
        }

        try {
            // Call backend API to get batch details
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(30)
                ->get($this->apiBaseUrl . '/payroll/batches/' . $batchId);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not fetch schedule details'
                ], 404);
            }

            $data = $response->json()['data'];
            $batch = $data['batch'];
            $summary = $data['summary'];

            // Format employees data for preview
            $employees = [];
            foreach ($batch['payroll_employees'] ?? [] as $employee) {
                // Find calculation for this employee
                $calculation = null;
                foreach ($batch['payroll_calculations'] ?? [] as $calc) {
                    if ($calc['payroll_employee_id'] == $employee['id']) {
                        $calculation = $calc;
                        break;
                    }
                }

                $employees[] = [
                    'staff_id' => $employee['staff_id'],
                    'employee_name' => $employee['employee_full_name'],
                    'basic_salary' => $employee['basic_salary'],
                    'allowances' => $employee['allowances'] ?? 0,
                    'gross' => $calculation['gross_salary'] ?? 0,
                    'nssf' => $calculation['nssf'] ?? 0,
                    'paye' => $calculation['paye_tax'] ?? 0,
                    'net' => $calculation['net_salary'] ?? 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $batch['id'],
                    'name' => $batch['name'],
                    'month_name' => date('F Y', strtotime($batch['payroll_month'] . '-01')),
                    'total_employees' => $summary['total_employees'] ?? 0,
                    'total_gross' => $summary['total_gross'] ?? 0,
                    'total_net' => $summary['total_net'] ?? 0,
                    'total_tax' => $summary['total_paye'] ?? 0,
                    'employees' => $employees
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Kwenye PayrollController.php (frontend)
    public function getPayrollData(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(60)
                ->get($this->apiBaseUrl . '/payroll/batches', [
                    'school_id' => $schoolId,
                    'status' => $request->status,
                    'year' => $request->year,
                    'search' => $request->search,
                    'page' => $request->page ?? 1,
                    'per_page' => 15
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Failed to fetch payroll data'
                ], $response->status());
            }

            $data = $response->json();
            $batches = $data['data'] ?? [];

            // ✅ FRONTEND GENERATES ITS OWN HASH (not using backend's hash)
            if (isset($batches['data']) && is_array($batches['data'])) {
                foreach ($batches['data'] as &$batch) {
                    // Generate hash using frontend's encryption
                    $batch['hash'] = $this->hashId($batch['id']);
                }
            } elseif (is_array($batches)) {
                foreach ($batches as &$batch) {
                    $batch['hash'] = $this->hashId($batch['id']);
                }
            }

            $statistics = [
                'total_batches' => $data['total_batches'] ?? 0,
                'finalized_count' => $data['finalized_count'] ?? 0,
                'draft_count' => $data['draft_count'] ?? 0,
                'calculated' => $data['total_calculated'] ?? 0,
            ];

            return response()->json([
                'success' => true,
                'batches' => $batches,
                'statistics' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payroll data', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }

    // app/Http/Controllers/PayrollController.php

    /**
     * Recalculate payroll batch
     * POST /payroll/{hash}/recalculate
     */
    public function recalculate($hash)
    {
        $id = $this->decryptId($hash);

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payroll link'
            ]);
        }

        try {
            $response = Http::withToken(session('finance_api_token'))
                ->timeout(60)
                ->post($this->apiBaseUrl . '/payroll/batches/' . $id . '/recalculate');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Recalculation failed'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $response->json()['data'] ?? null,
                'message' => 'Payroll recalculated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Payroll recalculation failed', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
