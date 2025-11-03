<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkApiTokenSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()) {
            if(! session('finance_api_token')) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Unauthorized client request');
            }

            return $next($request);
        }
        return redirect()->route('login')->with('error', 'Unauthorized user access');
    }
}
