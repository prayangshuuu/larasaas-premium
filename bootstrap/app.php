<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',         // load API routes
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Route middleware aliases
        $middleware->alias([
            'admin'         => \App\Http\Middleware\AdminOnly::class,
            'not-banned'    => \App\Http\Middleware\EnsureUserIsNotBanned::class,
            'admin.mfa'     => \App\Http\Middleware\EnsureAdminHasMfa::class,   // used only on impersonation routes
            'impersonation' => \App\Http\Middleware\ImpersonationGuard::class, // protects actions while impersonating
            'feature'       => \App\Http\Middleware\FeatureEnabled::class,     // feature gate: feature:<setting.key>
            'subscription.enabled' => \App\Http\Middleware\CheckSubscriptionModule::class,
            'plan.limit'           => \App\Http\Middleware\EnforcePlanLimits::class,
        ]);

        // Apply impersonation protections across ALL web routes (so non-admin pages are read-only too).
        // Admin area is additionally protected by AdminOnly.
        $middleware->appendToGroup('web', \App\Http\Middleware\ImpersonationGuard::class);

        // Exclude Stripe Webhook from CSRF
        $middleware->validateCsrfTokens(except: [
            'api/v1/stripe/webhook', // API prefix is v1 as seen in routes/api.php
            'api/stripe/webhook', // Just in case
            'stripe/webhook', // Web route webhook
        ]);

        // (Keep other sensitive middleware route-scoped; don't globally add admin.mfa, etc.)
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Unauthenticated -> redirect to login for web, JSON for APIs
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });

        // Forbidden -> 403 page or JSON
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage() ?: 'Forbidden.'], 403);
            }
            abort(403, $e->getMessage() ?: 'This action is unauthorized.');
        });

        // Gracefully handle accidental GETs to POST-only settings endpoints
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('admin/settings/features') ||
                $request->is('admin/settings/app') ||
                $request->is('admin/settings/app/logo') ||
                $request->is('admin/settings/smtp')) {
                return redirect()->route('admin.settings.index');
            }
            return null; // let the default handler run
        });

        // Model not found -> 404
        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            abort(404);
        });
    })
    ->withProviders([
        \App\Providers\AuthServiceProvider::class,      // <-- registers 'access-admin' gate
        \App\Providers\SettingsServiceProvider::class,
    ])
    ->withCommands([
        \App\Console\Commands\SanctumTokenCreate::class,
        \App\Console\Commands\SanctumTokenList::class,
        \App\Console\Commands\SanctumTokenRevoke::class,
    ])
    ->create();
