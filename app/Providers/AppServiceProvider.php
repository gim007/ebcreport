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
        // Accept legacy MD5 admin passwords; silently re-hash to bcrypt on login.
        Hash::extend('admin', fn () => new AdminHasher());

        // Remap Filament's generic 'email' credential to 'admin_email' column.
        Auth::provider('admin-eloquent', fn ($app, array $config) =>
            new AdminUserProvider($app['hash'], $config['model'])
        );
    }
}
