<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware ALIASES (route-scoped; not global)
        $middleware->alias([
            'admin'         => \App\Http\Middleware\AdminOnly::class,
            'not-banned'    => \App\Http\Middleware\EnsureUserIsNotBanned::class,
            'admin.mfa'     => \App\Http\Middleware\EnsureAdminHasMfa::class,   // ← used only on impersonation routes
            'impersonation' => \App\Http\Middleware\ImpersonationGuard::class,
        ]);

        // IMPORTANT:
        // Do NOT add 'admin.mfa' to any global stack. Keep it route-scoped:
        // $middleware->appendToGroup('web', [\App\Http\Middleware\ImpersonationGuard::class]);
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

        // Model not found -> 404
        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            abort(404);
        });
    })
    ->withProviders([
        \App\Providers\SettingsServiceProvider::class,
    ])
    ->create();
