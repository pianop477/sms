<?php
// app/Http/Controllers/SalarySlipVerificationController.php (Frontend)

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SalarySlipVerificationController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.finance_api_base_url', 'http://localhost:8000/api/v1.0');
    }

    /**
     * ========================================================================
     * VERIFY SALARY SLIP - Frontend handles UI, Backend handles data
     * ========================================================================
     * GET /verify-slip/{token}
     */

    // app/Http/Controllers/SalarySlipVerificationController.php (Frontend)

    public function verifyWeb($token)
    {
        // Log::info('Frontend verification started', [
        //     'token' => $token,
        //     'backend_url' => $this->apiBaseUrl . '/salary-slip/verify/' . $token
        // ]);

        if (!$token) {
            return view('payroll.verification-failed', [
                'message' => 'No verification token provided.',
                'token' => null
            ]);
        }

        try {
            // ✅ Remove the token requirement for public verification
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($this->apiBaseUrl . '/salary-slip/verify/' . $token);

            // Log::info('Backend response', [
            //     'status' => $response->status(),
            //     'body' => $response->body()
            // ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                return view('payroll.verification-failed', [
                    'message' => $errorData['message'] ?? 'Invalid verification token',
                    'token' => $token
                ]);
            }

            $result = $response->json();

            if (!$result['success']) {
                return view('payroll.verification-failed', [
                    'message' => $result['message'] ?? 'Verification failed',
                    'token' => $token
                ]);
            }

            $data = $result['data'];

            return view('payroll.verification-success', [
                'data' => [
                    'slip_number' => $data['slip_number'] ?? 'N/A',
                    'employee_name' => $data['employee_name'] ?? 'N/A',
                    'staff_id' => $data['staff_id'] ?? 'N/A',
                    'staff_type' => $data['staff_type'] ?? 'N/A',
                    'department' => $data['department'] ?? 'N/A',
                    'month' => $data['month'] ?? 'N/A',
                    'basic_salary' => $data['basic_salary'] ?? 0,
                    'total_allowances' => $data['total_allowances'] ?? 0,
                    'gross_salary' => $data['gross_salary'] ?? 0,
                    'nssf' => $data['nssf'] ?? 0,
                    'paye' => $data['paye'] ?? 0,
                    'heslb' => $data['heslb'] ?? 0,
                    'other_deductions' => $data['other_deductions'] ?? 0,
                    'net_salary' => $data['net_salary'] ?? 0,
                    'verification_time' => $data['verification_time'] ?? now()->format('d/m/Y H:i:s'),
                    'verification_count' => $data['verification_count'] ?? 1
                ],
                'token' => $token
            ]);
        } catch (\Exception $e) {
            Log::error('Verification error', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return view('payroll.verification-failed', [
                'message' => 'An error occurred during verification. Please try again later.',
                'token' => $token
            ]);
        }
    }
}
