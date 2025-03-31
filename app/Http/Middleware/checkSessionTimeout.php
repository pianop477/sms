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
            // Check if the session start time is set, if not, set it to now
            if (!session()->has('session_start_time')) {
                session()->put('session_start_time', now());
            }

            // Get the session start time
            $sessionStartTime = session()->get('session_start_time');

            // Calculate session duration in minutes
            $sessionDuration = now()->diffInMinutes($sessionStartTime);

            // If session duration exceeds 2 minutes, log out user and invalidate session
            if ($sessionDuration >= 60) {
                Auth::logout(); // Logout user
                $request->session()->invalidate(); // Invalidate session
                $request->session()->regenerateToken(); // Prevent CSRF attacks

                Alert()->toast('Session expired. Please login again 🚫', 'error');
                return redirect()->route('login');
            }
        }

        return $next($request);
    }

}
