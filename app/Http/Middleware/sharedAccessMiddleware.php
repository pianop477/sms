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

        if($user)
        {
            if($user->usertype == 2)
            {
                return $next($request);
            }

            if($user->teacher && $user->teacher->role_id == 2 || $user->teacher && $user->teacher->role_id == 3)
            {
                return $next($request);
            }
        }

    }
}
