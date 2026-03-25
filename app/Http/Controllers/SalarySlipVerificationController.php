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
     * Verify salary slip - Called from QR scan or manual token entry
     * GET /verify-slip?token=abc123xyz
     */
    public function verifyWeb(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            // Redirect to scanner page if no token
            return redirect()->route('scan.qr');
        }

        try {
            $response = Http::timeout(30)
                ->get($this->apiBaseUrl . '/salary-slip/verify/' . $token);

            if (!$response->successful()) {
                return view('payroll.verification-failed', [
                    'message' => $response->json()['message'] ?? 'Invalid verification token',
                    'token' => $token
                ]);
            }

            $data = $response->json()['data'];

            return view('payroll.verification-success', [
                'data' => $data,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return view('payroll.verification-failed', [
                'message' => 'Could not connect to verification service. Please try again later.',
                'token' => $token
            ]);
        }
    }
}
