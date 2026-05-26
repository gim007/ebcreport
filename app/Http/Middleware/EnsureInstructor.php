<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstructor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isInstructor()) {
            abort(403, 'Instructor access required.');
        }

        return $next($request);
    }
}
