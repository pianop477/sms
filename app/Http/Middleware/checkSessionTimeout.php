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
	        $lastActivity = Session::get('last_activity', time());
	        $sessionLifeTime = 60 * 60; // 1 hour

	       /* dd([
	            'last_activity' => $lastActivity,
	            'time_now' => time(),
	            'time_difference' => time() - $lastActivity,
	            'sessionLifeTime' => $sessionLifeTime
	        ]); */

	        if (time() - $lastActivity > $sessionLifeTime) {
	            Auth::logout();
	            session()->invalidate();
	            session()->regenerateToken();
	            return redirect()->route('login')->with('error', 'Session Expired, Please login');
	        }

	        if (!Session::has('last_activity')) {
		    Session::put('last_activity', time());
		}
	    }

	    return $next($request);
	}

}
