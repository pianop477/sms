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
        // Hakikisha mtumiaji ame-authenticate
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Pata session ya mtumiaji kutoka kwenye database
        $sessionId = Session::getId();
        $session = DB::table('sessions')->where('id', $sessionId)->first();

        // Ikiwa hakuna session kwenye database, acha user aendelee
        if (!$session) {
            return $next($request);
        }

        // Pata muda wa mwisho wa activity ya session
        $lastActivity = Carbon::parse($session->last_activity);

        // Angalia kama muda wa session umepita
        $timeoutDuration = 60; // Dakika 60
        if ($lastActivity->addMinutes($timeoutDuration)->isPast()) {
            // Futa session na force user kuingia tena
            Auth::logout();
            Session::flush();
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        return $next($request);
    }

}
