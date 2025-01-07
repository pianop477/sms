<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class checkSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     public function handle(Request $request, Closure $next): Response
    {
        // Exclude routes like login and logout
        if ($request->routeIs('login', 'logout')) {
            return $next($request);
        }

        // Check if the user is authenticated
        if (!Auth::check()) {
            Auth::logout();
            Alert::error('Error', 'Unauthorized request for this resource, please login');
            return redirect()->route('login');
        }

        // Check for the last_activity session key
        if (!$request->session()->has('last_activity')) {
            $request->session()->put('last_activity', time());
        }

        $sessionLifeTime = config('session.lifetime') * 60; // Convert minutes to seconds
        $lastActivity = $request->session()->get('last_activity');

        // Check if the session has expired
        if (time() - $lastActivity > $sessionLifeTime) {
            Auth::logout();
            Alert::error('Error', 'Session expired. Please log in again.');
            return redirect()->route('login');
        }

        // Update last activity
        $request->session()->put('last_activity', time());

        return $next($request);
    }

}
