<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class checkSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Ikiwa session_start_time haipo, weka sasa hivi (user ka-login tu)
            if (!session()->has('session_start_time')) {
                session(['session_start_time' => now()]);
            }

            // Linganisha muda wa sasa na ule aliowekewa mwanzo
            $sessionDuration = now()->diffInMinutes(session('session_start_time'));

            // Kama muda umepita zaidi ya masaa 2 (dakika 120), fanya logout
            if ($sessionDuration >= 120) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Session expired. Please login again');
            }
        }

        return $next($request);
    }


}
