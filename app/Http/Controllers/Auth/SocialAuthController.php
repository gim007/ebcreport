<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'facebook', 'apple'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS), 404);
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS), 404);

        $socialUser = Socialite::driver($provider)->user();

        $user = User::updateOrCreate(
            ['social_provider' => $provider, 'social_id' => $socialUser->getId()],
            [
                'user_login_id' => $socialUser->getEmail(),
                'user_email'    => $socialUser->getEmail(),
                'user_status'   => 'Active',
                'user_type'     => 'stud',
                'user_password' => bcrypt(str()->random(32)), // unusable password — login via SSO only
            ]
        );

        Auth::login($user);
        return redirect()->intended('/dashboard');
    }
}
