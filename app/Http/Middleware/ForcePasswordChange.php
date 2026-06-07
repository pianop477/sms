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

        $user = Auth::user();
        $userType = $user->usertype; // assuming column name is 'usertype'

        // Acha aendelee kama yupo kwenye page ya kubadilisha password au logout
        if ($request->routeIs('change.password') || $request->routeIs('change.new.password') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Check kama anatumia default password (mfano: shule2025)
        $isUsingDefaultPassword = Hash::check('shule2025', $user->password);

        // Kama ni parent (usertype 4), acha aendelee hata kama anatumia default password
        if ($userType == 4) {
            return $next($request);
        }

        // Kwa usertype wengine (1,2,3), watalazimika kubadilisha password
        if ($isUsingDefaultPassword && in_array($userType, [1, 2, 3])) {
            // Unaweza kutumia session au alert kuwataarifu
            session()->flash('warning', 'Default password is no longer allowed. Please change your password now!');
            return to_route('change.password');
        }

        return $next($request);
    }
}
