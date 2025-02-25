<?php

namespace App\Http\Middleware;

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
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity', time()); // Hii itahakikisha haipo null
            $sessionLifeTime = 60 * 60; // 1 hour

            if (time() - $lastActivity > $sessionLifeTime) {
                Auth::logout();
                Session::flush();
                return redirect()->route('login')->with('error', 'Session Expired, Please login again');
            }

            // Update session activity time
            Session::put('last_activity', time());
        }

        return $next($request);

    }

}
