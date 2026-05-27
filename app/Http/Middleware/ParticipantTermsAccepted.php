<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the participant selection flow: organization / instructor / course
 * pages all require the participant to have accepted the Privacy + Terms
 * page first (legacy parity with student_reg_terms.php).
 *
 * If the user is already authenticated (returning participant), we skip the
 * gate — they've accepted previously.
 */
class ParticipantTermsAccepted
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null) {
            return $next($request);
        }

        if (! $request->session()->has('participant.terms_accepted_at')) {
            return redirect()->route('participant.terms');
        }

        return $next($request);
    }
}
