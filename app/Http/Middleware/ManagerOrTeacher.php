<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerOrTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            // Redirect to login if user is not authenticated
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        if (
            $user->usertype == 2 ||
            ($user->teacher && $user->teacher->role_id == 2)
        ) {
            return $next($request);
        }

        // Redirect to error page if conditions are not met
        return redirect()->route('error.page');
    }
}
