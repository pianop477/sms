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
            $sessionId = $request->session()->getId(); // Pata session ID ya mtumiaji
            $session = DB::table('sessions')->where('id', $sessionId)->first();

            if ($session) {
                $lastActivity = Carbon::parse($session->last_activity);
                $now = Carbon::now();

                // Angalia kama muda umepita zaidi ya saa 1 (3600 sekunde)
                if ($lastActivity->diffInMinutes($now) >= 60) {
                    Auth::logout(); // Logout mtumiaji
                    $request->session()->invalidate(); // Futa session
                    return redirect()->route('login')->with('error', 'Session expired. Please login again.');
                }
            }
        }

        return $next($request);
    }

}
