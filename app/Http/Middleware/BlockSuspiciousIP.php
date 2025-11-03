<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class BlockSuspiciousIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $blockedIps = Cache::get('blocked_ips', []);

        if (in_array($ip, $blockedIps)) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Access Denied, Suspecious activity detected');
            // abort(403, 'Access Denied, Suspecious activity detected');
        }

        return $next($request);
    }
}
