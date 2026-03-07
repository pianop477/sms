<?php

namespace App\Http\Middleware;

use App\Models\contract_otp_validation;
use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ContractOrAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        // Log::info('🛡️ MIDDLEWARE CHECK', [
        //     'url' => $request->fullUrl(),
        //     'ip' => $request->ip(),
        //     'method' => $request->method()
        // ]);

        // ===== SECURITY: Check for blocked IPs =====
        if ($this->isIpBlocked($request->ip())) {
            // Log::warning('Blocked IP attempted access', ['ip' => $request->ip()]);
            abort(403, 'Access denied');
        }

        // ===== CHECK 1: Teacher authentication =====
        if (Auth::check()) {
            $user = Auth::user();

            // Verify user is active
            if ($user->status != 1) {
                // Log::warning('Inactive user', ['user_id' => $user->id]);
                Auth::logout();

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Account inactive'], 403);
                }
                return redirect()->route('login')->with('error', 'Account inactive');
            }

            $teacher = Teacher::where('user_id', $user->id)
                ->where('status', 1)
                ->first();

            if ($user->usertype == 3 && $teacher && in_array($teacher->role_id, [1, 3, 4])) {
                // Log::info('✅ Middleware: Teacher authenticated', [
                //     'user_id' => $user->id,
                //     'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
                // ]);
                return $next($request);
            }
        }

        // ===== CHECK 2: Token authentication =====
        $token = $request->bearerToken() ??
            $request->session()->get('contract_auth_token') ??
            $request->input('auth_token') ??
            $request->query('auth_token');

        if ($token) {
            // Validate token format
            if (strlen($token) < 20 || !preg_match('/^[a-zA-Z0-9]+$/', $token)) {
                // Log::warning('Invalid token format in middleware', [
                //     'ip' => $request->ip(),
                //     'token_prefix' => substr($token, 0, 10)
                // ]);

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid token format'], 401);
                }
                return redirect()->route('welcome')->with('error', 'Invalid authentication');
            }

            // Check token in database with ALL security constraints
            $otpValidation = contract_otp_validation::where('auth_token', $token)
                ->where('is_verified', true)
                ->where('is_used', false)
                ->where('is_active', true)
                ->where('is_expired', false)
                ->where('expires_at', '>', now())
                ->where('ip_address', $request->ip()) // CRITICAL: IP must match
                ->first();

            if ($otpValidation) {
                // Log::info('✅ Middleware: Token authenticated', [
                //     'user_id' => $otpValidation->user_id,
                //     'session_id' => $otpValidation->id,
                //     'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
                // ]);

                // Store in session for this request
                $request->session()->put('contract_auth_token', $token);
                $request->session()->put('auth_time', now()->toDateTimeString());
                $request->session()->put('auth_ip', $request->ip());
                $request->merge(['otp_session' => $otpValidation]);

                return $next($request);
            } else {
                // Log why token is invalid
                $this->logInvalidMiddlewareToken($token, $request);
            }
        }

        // ===== AUTHENTICATION FAILED =====
        // Log::warning('❌ Middleware: Authentication failed', [
        //     'ip' => $request->ip(),
        //     'url' => $request->fullUrl(),
        //     'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
        // ]);

        // Track failed attempts
        $this->trackFailedAttempt($request);

        // Return appropriate response
        if ($request->expectsJson() || $request->is('api/*') || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Tafadhali hakiki Utambulisho wako kwa ID yako'
            ], 401);
        }

        // For web requests, save intended URL and redirect to gateway
        session(['url.intended' => $request->fullUrl()]);

        return redirect()->route('contract.gateway.init')
            ->with('error', 'Tafadhali hakiki Utambulisho wako kwa ID yako');
    }

    /**
     * Log invalid token in middleware
     */
    private function logInvalidMiddlewareToken($token, Request $request)
    {
        $tokenRecord = contract_otp_validation::where('auth_token', $token)->first();

        if (!$tokenRecord) {
            // Log::warning('Middleware: Token not found', ['ip' => $request->ip()]);
        } elseif (!$tokenRecord->is_verified) {
            // Log::warning('Middleware: Token not verified', ['id' => $tokenRecord->id]);
        } elseif ($tokenRecord->is_used) {
            // Log::warning('Middleware: Token already used', ['id' => $tokenRecord->id]);
        } elseif (!$tokenRecord->is_active) {
            // Log::warning('Middleware: Token inactive', ['id' => $tokenRecord->id]);
        } elseif ($tokenRecord->is_expired || $tokenRecord->expires_at <= now()) {
            // Log::warning('Middleware: Token expired', ['id' => $tokenRecord->id]);
        } elseif ($tokenRecord->ip_address !== $request->ip()) {
            // Log::warning('🔴 MIDDLEWARE SECURITY: Token IP mismatch', [
            //     'id' => $tokenRecord->id,
            //     'token_ip' => $tokenRecord->ip_address,
            //     'request_ip' => $request->ip()
            // ]);
        }
    }

    /**
     * Track failed attempts for rate limiting
     */
    private function trackFailedAttempt(Request $request)
    {
        $key = 'failed_auth_' . $request->ip();
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addHour());

        if ($attempts > 20) { // More than 20 failed attempts per hour
            $this->blockIp($request->ip());
        }
    }

    /**
     * Block an IP address
     */
    private function blockIp($ip)
    {
        Cache::put('blocked_ip_' . $ip, true, now()->addDay());
        // Log::critical('🔴 IP BLOCKED due to multiple failed attempts', ['ip' => $ip]);
    }

    /**
     * Check if IP is blocked
     */
    private function isIpBlocked($ip)
    {
        return Cache::has('blocked_ip_' . $ip);
    }
}
