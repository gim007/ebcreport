<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Instructor email-verification flow — Laravel's signed-URL approach,
 * adapted to the legacy ebc_user_master `user_email` column.
 *
 * Routes:
 *   GET  /instructor/email/verify                 → notice (shown after signup)
 *   GET  /instructor/email/verify/{id}/{hash}     → consume the signed link
 *   POST /instructor/email/verify/resend          → resend if the user lost it
 *
 * Mirrors legacy ebcdisc.com `instructor_verification.php?e=<base64>` but
 * uses HMAC-signed URLs (security improvement over the legacy base64 token).
 */
class InstructorVerificationController extends Controller
{
    public function notice(Request $request): View
    {
        return view('instructor.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('instructor.dashboard')
                ->with('status', 'Your email is already verified.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('instructor.dashboard')
            ->with('status', 'Email verified. Your account is still pending admin approval.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('instructor.dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'A new verification link has been sent.');
    }
}
