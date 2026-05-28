<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Participant + instructor login. Accepts either a user_login_id or an
 * email address as the username field. Verifies the legacy MD5 password
 * format silently and upgrades the stored hash to bcrypt on first success
 * (mirrors AdminHasher behavior for the admin guard).
 */
class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('user_login_id', $credentials['username'])
            ->orWhere('user_email', $credentials['username'])
            ->first();

        if ($user === null || ! $this->verifyPassword($user, $credentials['password'])) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate(); // R-05 session ID rotation

        return redirect()->intended($this->defaultRedirect($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Verify password against bcrypt; if the stored hash is legacy MD5, accept
     * matching plaintext and upgrade the hash to bcrypt in-place.
     */
    private function verifyPassword(User $user, string $plain): bool
    {
        $stored = (string) $user->getAuthPassword();

        if ($this->isMd5($stored)) {
            if (md5($plain) !== $stored) {
                return false;
            }
            $user->forceFill(['user_password' => Hash::make($plain)])->saveQuietly();
            return true;
        }

        return Hash::check($plain, $stored);
    }

    private function isMd5(string $hash): bool
    {
        return strlen($hash) === 32 && ctype_xdigit($hash);
    }

    private function defaultRedirect(User $user): string
    {
        // Land both user types on their account page after sign-in. Account
        // is the natural starting point: it shows credentials/profile and
        // links to everything else (dashboard, courses, reports).
        if ($user->isInstructor()) {
            return route('instructor.account');
        }
        return route('participant.account');
    }
}
