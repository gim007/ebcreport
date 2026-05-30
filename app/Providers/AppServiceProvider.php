<?php

namespace App\Providers;

use App\Auth\AdminHasher;
use App\Auth\AdminUserProvider;
use App\Services\DiscChartService;
use App\Services\DiscParagraphService;
use App\Services\DiscScoreCalculator;
use App\Services\ReportPdfService;
use App\Services\SmsOtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;
use Twilio\Rest\Client as TwilioClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DiscScoreCalculator::class);
        $this->app->singleton(DiscParagraphService::class);
        $this->app->singleton(DiscChartService::class);
        $this->app->singleton(ReportPdfService::class);

        $this->app->singleton(TwilioClient::class, fn () => new TwilioClient(
            config('services.twilio.sid'),
            config('services.twilio.token'),
        ));

        $this->app->singleton(SmsOtpService::class);

        $this->app->singleton(StripeClient::class, fn () => new StripeClient(
            config('services.stripe.secret'),
        ));
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            // R-12: force every generated URL to use https in production so links
            // emitted server-side (password reset emails, signed URLs, etc.) match
            // the HTTPS the request middleware enforces. Skipped in non-prod so
            // local http://localhost dev keeps working.
            URL::forceScheme('https');

            // R-08 / R-05 production-safety guards. Fail at boot rather than
            // letting a misconfigured production silently leak stack traces
            // (APP_DEBUG=true) or store plaintext session payloads
            // (SESSION_ENCRYPT=false). The .env.production.example template
            // sets these correctly, but this catches the "someone copied the
            // dev .env to prod" mistake before the first user request.
            if (config('app.debug')) {
                throw new \RuntimeException(
                    'APP_DEBUG must be false in production (see .env.production.example).'
                );
            }
            if (! config('session.encrypt')) {
                throw new \RuntimeException(
                    'SESSION_ENCRYPT must be true in production (see .env.production.example).'
                );
            }
        }

        // Accept legacy MD5 admin passwords; silently re-hash to bcrypt on login.
        Hash::extend('admin', fn () => new AdminHasher());

        // Remap Filament's generic 'email' credential to 'admin_email' column.
        Auth::provider('admin-eloquent', fn ($app, array $config) =>
            new AdminUserProvider($app['hash'], $config['model'])
        );
    }
}
