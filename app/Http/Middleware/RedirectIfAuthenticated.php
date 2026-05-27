<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects authenticated users away from "guest only" pages (landing,
 * login, register, forgot/reset password, forgot username, instructor
 * signup) to their appropriate dashboard.
 *
 * Resolution order for the destination:
 *   - instructor (`user_type=ins`)  → instructor.dashboard
 *   - participant (`user_type=stud`)→ participant.account
 *   - admin guard authenticated     → bypass (admin lives at /admin and
 *     uses a separate guard; the `web` guard is what matters here)
 *
 * Apply via `->middleware('guest.redirect')` on individual routes or
 * groups. Does not interfere with users who are not logged in.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('web')->check()) {
            return $next($request);
        }

        $user = Auth::guard('web')->user();

        if (method_exists($user, 'isInstructor') && $user->isInstructor()) {
            return redirect()->route('instructor.dashboard');
        }

        if (method_exists($user, 'isParticipant') && $user->isParticipant()) {
            return redirect()->route('participant.account');
        }

        // Unknown user_type — fall back to the participant account page.
        return redirect()->route('participant.account');
    }
}
