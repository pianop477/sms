<?php

namespace App\Http\Middleware;

use App\Services\FinanceTokenService;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ApiSessionTokenMiddleware
{
    protected $financeTokenService;

    public function __construct(FinanceTokenService $financeTokenService)
    {
        $this->financeTokenService = $financeTokenService;
    }

    public function handle($request, Closure $next)
    {
        // Ensure user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login again.');
        }

        $token = Session::get('finance_api_token');
        $expiresAt = Session::get('finance_token_expires_at');

        // Token haipo kabisa
        if (!$token || !$expiresAt) {
            // Log::warning("Finance token missing for user: " . Auth::id());
            $this->forceLogout();
            return redirect()->route('login')->with('error', 'Session has expired. Please login.');
        }

        // Ikiwa imebaki dakika chini ya 2, jaribu ku-refresh token
        if (now()->diffInSeconds($expiresAt, false) <= 120 && now()->lessThan($expiresAt)) {
            // Log::info("Finance token is about to expire, refreshing...", [
            //     'user_id' => Auth::id(),
            //     'expires_in_seconds' => now()->diffInSeconds($expiresAt, false)
            // ]);

            $newToken = $this->financeTokenService->ensureValidToken();
            if (!$newToken) {
                // Log::warning("Token refresh failed for user: " . Auth::id());
                $this->forceLogout();
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
        }

        // Ikiwa imekwisha kabisa
        if (now()->greaterThanOrEqualTo($expiresAt)) {
            // Log::warning("Finance token expired for user: " . Auth::id());
            $newToken = $this->financeTokenService->ensureValidToken();
            if (!$newToken) {
                $this->forceLogout();
                return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
            }
        }

        return $next($request);
    }

    private function forceLogout()
    {
        try {
            $userId = Auth::id();
            // Log::info("Force logging out user {$userId} due to expired finance token");

            // Clean Laravel session properly
            Auth::logout();

            // Delete actual record from 'sessions' table
            if (session()->getId()) {
                DB::table('sessions')->where('id', session()->getId())->delete();
            }

            Session::invalidate();
            Session::regenerateToken();
        } catch (\Throwable $e) {
            // Log::error("Error during forced logout: " . $e->getMessage());
        }
    }
}
