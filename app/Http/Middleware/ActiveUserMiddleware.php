<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Exclude the login and logout routes
        if ($request->routeIs('login', 'logout')) {
            return $next($request);
        }

        if(! $user) {
            // Alert::error('Error!', 'Unauthorized user access');
            return redirect()->route('logout')->with('error', 'Unauthorized user access');
        }

        if($user->status == 1) {
            return $next($request);
        }

        Auth::logout();
        // Alert::warning('Warning!', 'Account suspended, please contact system administrator');
        return redirect()->route('login')->with('error', 'Account suspended, please contact system administrator');

    }
}
