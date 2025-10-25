<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class RefreshFinanceToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $token = session('finance_api_token');
        $expiresAt = session('finance_token_expires_at');

        if ($token && now()->diffInMinutes($expiresAt, false) < 5) {
            // Refresh token if less than 5 minutes remaining
            $response = Http::post(config('app.finance_api_base_url') . '/auth/token', [
                'client_key' => config('app.finance_api_client_key'),
                'client_secret' => config('app.finance_api_client_secret'),
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                session([
                    'finance_api_token' => $tokenData['token'],
                    'finance_token_expires_at' => now()->addSeconds($tokenData['expires_in']),
                ]);
            }
        }

        return $next($request);
    }
}
