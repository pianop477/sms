<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kama user hajalogin, mwache aendelee
        if (!Auth::check()) {
            return $next($request);
        }

        // Acha aendelee kama yupo kwenye page ya kubadilisha password
        if ($request->routeIs('change.password') || $request->routeIs('change.new.password') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Check kama anatumia default password (mfano: shule2025)
        if (Hash::check('shule2025', Auth::user()->password)) {
            // Alert::warning('Warning', 'Default password is no longer allowed, Please change it now!');
            return to_route('change.password');
        }

        return $next($request);
    }
}
