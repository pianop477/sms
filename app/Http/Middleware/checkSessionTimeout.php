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
             $warningTime = 60 * 55; // 5 minutes before expiration

             $remainingTime = $sessionLifeTime - (time() - $lastActivity);

             // **Ikiwa muda umeisha, toa mtumiaji nje**
             if ($remainingTime <= 0) {
                 Auth::logout();
                 session()->invalidate();
                 session()->regenerateToken();

                 if ($request->expectsJson() || $request->ajax()) {
                     return response()->json(['session_expired' => true], 401);
                 } else {
                     return redirect()->route('login')->with('error', 'Session expired. Please login again.');
                 }
             }

             // **Onyo la Session Expiring**
             if ($remainingTime <= 300) { // 5 minutes before expiration
                 if ($request->expectsJson() || $request->ajax()) {
                     return response()->json([
                         'session_expiring_soon' => true,
                         'remaining_time' => $remainingTime
                     ]);
                 }
             }

             // **Sasisha last activity**
             Session::put('last_activity', time());
         }

         return $next($request);
     }

}
