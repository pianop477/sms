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
             $sessionStartTime = session()->get('session_start_time', now()); // Pata muda wa kuanza wa session
             $sessionDuration = now()->diffInMinutes($sessionStartTime);

             // Angalia kama muda wa session umefikia saa 1 (60 dakika)
             if ($sessionDuration >= 60) {
                 Auth::logout(); // Logout mtumiaji
                 $request->session()->invalidate(); // Futa session
                 $request->session()->regenerateToken(); // Zuia CSRF attacks

                 return redirect()->route('login')->with('error', 'Session expired. Please login again.');
             }
         } else {
             // Weka muda wa kuanza session mara ya kwanza
             session()->put('session_start_time', now());
         }

         return $next($request);
     }

}
