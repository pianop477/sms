<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiSessionTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for users with usertype 5
        if (auth()->check() && auth()->user()->usertype == 5) {
            $token = session('finance_api_token');
            $expiresAt = session('finance_token_expires_at');

            // Check if token exists and is not expired
            if (!$token || !$expiresAt || now()->greaterThan($expiresAt)) {
                // Token missing or expired, try to refresh
                if (!$this->refreshFinanceToken()) {
                    // Refresh failed, logout user
                    auth()->logout();
                    session()->flush();

                    return redirect()->route('login')
                        ->with('error', 'Session expired. Please login again.');
                }
            }
        }

        return $next($request);
    }

    private function refreshFinanceToken(): bool
    {
        try {
            $response = Http::timeout(30)->post(config('app.finance_api_base_url') . '/auth/token', [
                'client_key' => config('app.finance_api_client_key'),
                'client_secret' => config('app.finance_api_client_secret'),
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                if (isset($tokenData['status']) && $tokenData['status'] === true && isset($tokenData['token'])) {
                    session([
                        'finance_api_token' => $tokenData['token'],
                        'finance_token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
                    ]);

                    Log::info("Finance token refreshed for user: " . auth()->id());
                    return true;
                }
            }

            Log::error("Finance token refresh failed", ['response' => $response->body()]);
            return false;

        } catch (Throwable $e) {
            // Log::error("Finance token refresh exception: {$e->getMessage()}");
            return false;
        }
    }

}
