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
            // 1. Initialize last_activity if missing
            if (!session()->has('last_activity')) {
                session(['last_activity' => now()]);
            }

            // 2. Parse last_activity (handle both DateTime and timestamp)
            $lastActivity = session('last_activity');
            $lastActivityTime = is_int($lastActivity)
                ? Carbon::createFromTimestamp($lastActivity)
                : Carbon::parse($lastActivity);

            $inactiveTime = now()->diffInMinutes($lastActivityTime);

            // 3. Logout if inactive for 60+ minutes
            if ($inactiveTime >= 60) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Session imekwisha. Tafadhali ingia tena.');
            }

            // 4. Update last_activity for non-background requests
            if (!$this->isBackgroundRequest($request)) {
                session(['last_activity' => now()]); // Stores as DateTime
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
