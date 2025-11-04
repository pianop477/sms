<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiSessionTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()) {

            //if no api token in session, logout user
            if (!session('finance_api_token')) {
                session()->forget('finance_api_token'); // safe
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Unauthorized client request');
            }

            return $next($request);
        }

        //if not authenticated, redirect to login
        return redirect()->route('login')->with('error', 'Unauthorized user access');
    }
}
