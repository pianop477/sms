<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Retrieve the user's last login time from the session
        $lastLoginTime = Session::get('last_login_time');

        // If there's no last login time in the session, it means the user hasn't logged in yet
        if (!$lastLoginTime) {
            return $next($request); // Let the request pass, if not logged in
        }

        // Define session timeout duration (1 hour)
        $timeoutDuration = 60; // 60 minutes (1 hour)

        // Check if the session has expired
        if (Carbon::parse($lastLoginTime)->addMinutes($timeoutDuration)->isPast()) {
            // If the session has expired, clear the session and redirect to login
            session()->flush();
            // Redirect to login page
            return redirect()->route('login')->with('error', 'Your session has expired. Please login.');
        }

        // Continue to the next request (middleware stack)
        return $next($request);
    }

}
