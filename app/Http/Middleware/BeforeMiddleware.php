<?php

namespace App\Http\Middleware;

use Closure;

class BeforeMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->input('age') <= 200) {
            return 'false';
        }

        return $next($request);
    }
}