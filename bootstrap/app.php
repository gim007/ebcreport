<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'              => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'instructor'        => \App\Http\Middleware\EnsureInstructor::class,
            'participant'       => \App\Http\Middleware\EnsureParticipant::class,
            'participant.terms' => \App\Http\Middleware\ParticipantTermsAccepted::class,
            'guest.redirect'    => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);

        // R-12: trust the upstream proxy / load balancer so $request->isSecure()
        // correctly reflects the original scheme and forced HTTPS redirects work.
        // Use TRUSTED_PROXIES env (CIDR or single IP) in prod; '*' is fine for
        // platforms where you cannot enumerate proxy IPs (Heroku, App Runner, etc).
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '*'));

        // R-12: emit HSTS / CSP / X-Frame-Options / X-Content-Type-Options on
        // every response (web + Filament + Livewire).
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // R-08: production must not leak detail to users.
        //
        // - PII fields (passwords, credit cards, tokens) are excluded from
        //   the validation-error redirect payload so they never end up in
        //   browser session / flash data
        // - Custom branded error pages live in resources/views/errors/
        //   and are picked up automatically by Laravel
        // - Full exception logging happens to storage/logs (daily channel
        //   in prod) regardless of what the user sees
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
            'payment_method_id',
            '_token',
        ]);
    })->create();
