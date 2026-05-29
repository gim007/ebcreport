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
