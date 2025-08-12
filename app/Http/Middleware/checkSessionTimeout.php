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
            // 1. Angalia kama session ya 'last_activity' ipo, kama haipo, weka sasa
            if (!session()->has('last_activity')) {
                session(['last_activity' => now()]);
            }

            // 2. Angalia muda wa inactivity (TUUMA UPDATE last_activity IKIWA USER AMEFANYA KITENDO HALISI)
            $inactiveTime = now()->diffInMinutes(session('last_activity'));

            if ($inactiveTime >= 60) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Session imekwisha. Tafadhali ingia tena.');
            }

            // 3. UPDATE last_activity IKIWA REQUEST NI KITENDO HALISI (SIYO BACKGROUND REQUEST)
            // Mfano: AJAX, favicon.ico, livewire, asset requests hazina budi ZISIUPDATE last_activity
            if (!$this->isBackgroundRequest($request)) {
                session(['last_activity' => now()]);
            }
        }

        return $next($request);
    }

    // Kuangalia kama request ni ya 'background' (AJAX, favicon, assets, n.k)
    protected function isBackgroundRequest(Request $request)
    {
        return $request->isXmlHttpRequest() ||
            $request->is('*.js') ||
            $request->is('*.css') ||
            $request->is('*.png') ||
            $request->is('livewire/*');
            $request->is('favicon.ico') ||
            $request->is('robots.txt') ||
            $request->is('sitemap.xml');
            $request->is('api/*');
            $request->is('storage/*');
    }

}
