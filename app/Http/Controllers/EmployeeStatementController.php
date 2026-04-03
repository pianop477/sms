<?php
// app/Http/Controllers/EmployeeStatementController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ResolveApplicantTrait;
use Illuminate\Support\Facades\Validator;

class EmployeeStatementController extends Controller
{
    use ResolveApplicantTrait;

    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.finance_api_base_url', 'http://localhost:8000/api/v1.0');
    }

    /**
     * Search employee statement - AJAX real-time
     */
    public function search(Request $request)
    {

        // ✅ Validation - staff_type no longer required from user
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string|min:2',
            'from_month' => 'nullable|date_format:Y-m',
            'to_month' => 'nullable|date_format:Y-m',
            'year' => 'nullable|digits:4'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $schoolId = Auth::user()->school_id;
        $token = session('finance_api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to finance system first',
                'redirect' => route('finance.login')
            ], 401);
        }

        try {
            // ✅ STEP 1: Get employee details including staff_type using trait
            $employeeDetails = $this->resolveApplicantDetails($request->staff_id, $schoolId);

            if ($employeeDetails['staff_type'] === 'Unknown') {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found. Please check Staff ID.'
                ], 404);
            }

            // ✅ STEP 2: Build request parameters with staff_type from trait
            $params = [
                'staff_id' => $request->staff_id,
                'staff_type' => $employeeDetails['staff_type'], // ✅ Auto-detected staff_type
                'school_id' => $schoolId
            ];

            if ($request->filled('from_month')) {
                $params['from_month'] = $request->from_month;
            }
            if ($request->filled('to_month')) {
                $params['to_month'] = $request->to_month;
            }
            if ($request->filled('year')) {
                $params['year'] = $request->year;
            }

            $url = $this->apiBaseUrl . '/employee/statement';

            // ✅ STEP 3: Make API request
            $response = Http::withToken($token)
                ->timeout(30)
                ->get($url, $params);

            if (!$response->successful()) {
                if ($response->status() === 401) {
                    session()->forget('finance_api_token');
                    return response()->json([
                        'success' => false,
                        'message' => 'Your finance session has expired. Please login again.',
                        'redirect' => route('finance.login')
                    ], 401);
                }

                $errorMsg = $response->json()['message'] ?? 'Failed to fetch employee statement';

                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], $response->status());
            }

            $data = $response->json()['data'];

            // ✅ Store search params in session for PDF download
            session(['statement_search_params' => [
                'staff_id' => $request->staff_id,
                'staff_type' => $employeeDetails['staff_type'], // ✅ Store staff_type too
                'from_month' => $request->from_month,
                'to_month' => $request->to_month,
                'year' => $request->year
            ]]);

            return response()->json([
                'success' => true,
                'employee' => $data['employee'] ?? [],
                'summary' => $data['summary'] ?? [],
                'statement' => $data['statement'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Employee statement search exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download statement as PDF
     */
    public function downloadPdf(Request $request)
    {

        // ✅ First try to get from query params (from URL)
        $staffId = $request->query('staff_id');
        $staffType = $request->query('staff_type');
        $fromMonth = $request->query('from_month');
        $toMonth = $request->query('to_month');
        $year = $request->query('year');

        // ✅ If not in query, try session
        if (!$staffId) {
            $staffId = session('statement_search_params.staff_id');
        }
        if (!$staffType) {
            $staffType = session('statement_search_params.staff_type');
        }
        if (!$fromMonth) {
            $fromMonth = session('statement_search_params.from_month');
        }
        if (!$toMonth) {
            $toMonth = session('statement_search_params.to_month');
        }
        if (!$year) {
            $year = session('statement_search_params.year');
        }

        if (!$staffId || !$staffType) {
            Log::warning('PDF download: Missing staff_id or staff_type', [
                'staff_id' => $staffId,
                'staff_type' => $staffType
            ]);

            return redirect()->route('employee.statement.index')
                ->with('error', 'Please search for an employee first');
        }

        $schoolId = Auth::user()->school_id;
        $token = session('finance_api_token');

        if (!$token) {
            return redirect()->route('finance.login')
                ->with('error', 'Please login to finance system first');
        }

        try {
            $params = [
                'staff_id' => $staffId,
                'staff_type' => $staffType,
                'school_id' => $schoolId
            ];
            if ($fromMonth) $params['from_month'] = $fromMonth;
            if ($toMonth) $params['to_month'] = $toMonth;
            if ($year) $params['year'] = $year;

            $url = $this->apiBaseUrl . '/employee/statement/pdf';

            $response = Http::withToken($token)
                ->timeout(60)
                ->get($url, $params);

            if (!$response->successful()) {
                if ($response->status() === 401) {
                    session()->forget('finance_api_token');
                    return redirect()->route('finance.login')
                        ->with('error', 'Your finance session has expired. Please login again.');
                }

                // ✅ Log the error response body
                $errorBody = $response->body();
                Log::error('PDF API error', ['body' => $errorBody]);

                return redirect()->back()->with('error', 'Failed to generate PDF: ' . $errorBody);
            }

            $filename = "Payment Statement_". strtoupper($staffId).".pdf";

            return response($response->body())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Log::error('PDF download exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Connection error: ' . $e->getMessage());
        }
    }

    /**
     * Show employee statement form
     */
    public function index()
    {

        return view('employee.statement');
    }
}
