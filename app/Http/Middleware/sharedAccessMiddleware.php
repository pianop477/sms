<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class sharedAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            if (
                $user->usertype == 2 ||
                ($user->teacher && in_array($user->teacher->role_id, [2, 3]))
            ) {
                return $next($request);
            }

            return redirect()->route('error.page');
        }

        // Redirect to login if user is not authenticated
        return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
    }
}
