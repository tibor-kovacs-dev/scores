<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'ratelimit:api:'.$request->ip();
        $maxAttempts = 120;      
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw new ThrottleRequestsException(
                'Too many requests. Please try again later.',
                null,
                ['Retry-After' => $seconds]
            );
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }
}
