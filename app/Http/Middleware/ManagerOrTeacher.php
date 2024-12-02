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
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        $user = Auth::user();

        // Check if the user is authenticated
        if ($user) {
            // Check if the user has usertype = 2
            if ($user->usertype == 2) {
                return $next($request);
            }

            // Check if the user is a teacher with role_id = 2
            if ($user->teacher && $user->teacher->role_id == 2 || $user->teacher && $user->teacher->role_id == 3) {
                return $next($request);
            }
        }

        // Redirect to error page if conditions are not met
        return redirect()->route('error.page');
    }
}
