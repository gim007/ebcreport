<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParticipant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isParticipant()) {
            abort(403, 'Participant access required.');
        }

        return $next($request);
    }
}
