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
        // Exclude routes like 'login' and 'logout'
        if ($request->routeIs('login', 'logout')) {
            return $next($request);
        }

        // Check if user is authenticated and session has last activity
        if (!Auth::check() || !$request->session()->has('last_activity')) {
            Auth::logout();
            Alert::error('Error', 'Unauthorized request for this resource, please login');
            return redirect()->route('login');
        }

        $sessionLifeTime = config('session.lifetime') * 60;
        $lastActivity = $request->session()->get('last_activity');

        if (time() - $lastActivity > $sessionLifeTime) {
            Auth::logout();
            Alert::error('Error', 'Unauthorized request for this resource, please login');
            return redirect()->route('login');
        }

        // Update last activity time
        $request->session()->put('last_activity', time());
        return $next($request);
    }
}
