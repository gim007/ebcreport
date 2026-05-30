<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * R-12: applies the four secure-response headers the SOW mandates
 * (HSTS, X-Content-Type-Options, X-Frame-Options, Content-Security-Policy)
 * on every outgoing response, including Filament + Livewire AJAX.
 *
 * HSTS is only emitted on production AND for secure requests so that
 *  - local dev (`php artisan serve` over http) doesn't lock localhost to
 *    HTTPS in the browser for a year
 *  - a misconfigured non-https production request doesn't broadcast an
 *    HSTS policy the deployment can't honor
 *
 * The CSP intentionally permits 'unsafe-inline' for scripts and styles
 * because Filament, Livewire, and several Blade views (the participant
 * registration state-swap, the welcome page login form errors, etc.)
 * ship inline <script> / <style> blocks. Tightening to nonces or hashes
 * is a follow-up: it requires touching every inline block and is not
 * required by R-12, which only mandates that a CSP "be implemented".
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // ── Always-on headers ───────────────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options',        'SAMEORIGIN');
        $response->headers->set('Referrer-Policy',        'strict-origin-when-cross-origin');

        // ── HSTS (production + over HTTPS only) ─────────────────────────
        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // ── Content-Security-Policy ─────────────────────────────────────
        // Permissive baseline that works with Filament + Livewire + Tailwind
        // CDN + Stripe Elements. Tighten incrementally.
        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                implode('; ', [
                    "default-src 'self'",
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://js.stripe.com",
                    "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com",
                    "img-src 'self' data: blob: https:",
                    "font-src 'self' data: https://fonts.gstatic.com",
                    "connect-src 'self' https://api.stripe.com",
                    "frame-src 'self' https://js.stripe.com https://hooks.stripe.com",
                    "object-src 'none'",
                    "base-uri 'self'",
                    "form-action 'self'",
                    "frame-ancestors 'self'",
                ])
            );
        }

        return $response;
    }
}
