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
            // 1. Angalia kama session ya mwisho (last activity) ipo
            if (!session()->has('last_activity')) {
                session(['last_activity' => now()]);
            }

            // 2. Piga update "last_activity" kwa kila request
            session(['last_activity' => now()]);

            // 3. Angalia kama muda wa session umekwisha (120 dakika bila activity)
            $inactiveTime = now()->diffInMinutes(session('last_activity'));

            if ($inactiveTime >= 60) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->Alert()->toast('Session has expired please login again.', 'error');
            }
        }

        return $next($request);
    }

}
