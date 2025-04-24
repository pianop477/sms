<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAgent
{

    // Allowed common user agents (simplified for example)
    protected $allowedAgents = [
        'Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Mozilla',
    ];


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->header('User-Agent');

        // Check if any allowed keyword exists in the User-Agent string
        $isAllowed = false;
        foreach ($this->allowedAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                $isAllowed = true;
                break;
            }
        }

        // If not allowed, log it and block or redirect
        if (! $isAllowed) {
            Log::warning("Blocked suspicious user agent: {$userAgent}", [
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);

            // Option 1: block completely
            return abort(403, 'Access denied due to suspicious browser.');

            // Option 2: just log and continue
            // return $next($request);
        }

        return $next($request);
    }
}
