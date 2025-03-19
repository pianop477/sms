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
            $lastActivity = Session::get('last_activity');

            if (!$lastActivity) {
                Session::put('last_activity', time());
                $lastActivity = time();
            }

            $sessionLifeTime = 60 * 60; // 1 hour
            $warningTime = 60 * 55; // 5 minutes before session expires

            if (time() - $lastActivity > $sessionLifeTime) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                return response()->json(['session_expired' => true], 401);
            }

            if (time() - $lastActivity > $warningTime && !Session::has('session_warning_shown')) {
                Session::put('session_warning_shown', true);
                Session::put('session_remaining_time', $sessionLifeTime - (time() - $lastActivity));

                return response()->json([
                    'session_expiring_soon' => true,
                    'remaining_time' => Session::get('session_remaining_time')
                ]);
            }

            Session::put('last_activity', time());
        }

        return $next($request);
    }

}
