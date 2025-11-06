<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
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
        $token = Session::get('finance_api_token');
        $expiresAt = Session::get('finance_token_expires_at');

        if ($token && $expiresAt) {
            // Ikiwa token iko karibu ku-expire (<5 min)
            if (now()->diffInMinutes($expiresAt, false) < 5) {
                try {
                    $response = Http::withToken($token)
                        ->post(config('app.finance_api_base_url') . '/auth/refresh');

                    if ($response->successful()) {
                        $data = $response->json();

                        Session::put('finance_api_token', $data['token']);
                        Session::put('finance_token_expires_at', now()->addSeconds($data['expires_in']));
                    } else {
                        // Token invalid/expired, remove session
                        Session::forget(['finance_api_token', 'finance_token_expires_at']);
                    }
                } catch (\Throwable $e) {
                    // Connection error, ignore or log
                }
            }
        }

        return $next($request);
    }
}
