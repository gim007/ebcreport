<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Forgot Password — legacy parity with ebcdisc.com `/forgot_password.php`.
 *
 * Sends a Laravel signed-token reset link. Tokens live in
 * `password_reset_tokens` and expire per `config('auth.passwords.users.expire')`.
 *
 * Email lookup uses the legacy `user_email` column (Laravel's default broker
 * keys on `email`; we pass the credential explicitly).
 */
class ForgotPasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // PasswordBroker default expects an `email` column. Our User model
        // exposes `user_email`, so pass the credential explicitly to avoid
        // a "column not found" query error.
        $status = Password::broker('users')->sendResetLink(
            ['user_email' => $request->input('email')],
        );

        // Always respond with the generic success message — leaking whether
        // an account exists is a known auth-enumeration risk.
        return back()->with('status', __('If that email is registered, a password reset link has been sent.'));
    }
}
