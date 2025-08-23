<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class ImpersonationGuard
{
    /**
     * Policy
     * -------
     * - Feature OFF (DB flag):
     *     • Allow hitting admin.impersonate.stop so an active session can exit.
     *     • Otherwise 404 any impersonation endpoints.
     *     • If session still has impersonation, clear it.
     * - Feature ON:
     *     • If not impersonating → allow.
     *     • If impersonating:
     *         • Always allow admin.impersonate.stop.
     *         • Block the entire admin area while impersonating (defense in depth; AdminOnly also enforces this).
     *         • Always block ultra-sensitive routes (2FA/password/etc.).
     *         • Enforce read-only by default (block POST/PUT/PATCH/DELETE) unless session('impersonation_mode') === 'full'.
     *
     * NOTE: If you need protection across ALL web pages (not just admin),
     *       also add this middleware to the "web" group in bootstrap/app.php:
     *       $middleware->appendToGroup('web', \App\Http\Middleware\ImpersonationGuard::class);
     */
    public function handle(Request $request, Closure $next)
    {
        $enabled       = Setting::bool('features.impersonation', false);
        $route         = $request->route();
        $name          = $route ? $route->getName() : null;
        $path          = ltrim($request->path(), '/');
        $impersonating = $request->session()->has('impersonated_by');

        $isImpersonationAction = $name && str_starts_with($name, 'admin.impersonate.');
        $isStopActionByName    = $name === 'admin.impersonate.stop';
        $isStopActionByPath    = (bool) preg_match('#^admin/impersonate/stop$#i', $path);
        $isStopAction          = $isStopActionByName || $isStopActionByPath;

        /* ------------------------ Feature OFF ------------------------ */
        if (! $enabled) {
            // Still allow STOP so an active session can exit even if the feature is turned off.
            if ($isStopAction) {
                return $next($request);
            }

            // End any active impersonation if the flag was turned off mid-session.
            if ($impersonating) {
                $request->session()->forget(['impersonated_by', 'impersonation_mode']);
            }

            // Hide/deny impersonation endpoints entirely when disabled.
            if ($isImpersonationAction) {
                abort(404);
            }

            return $next($request);
        }

        /* ------------------------ Feature ON; not impersonating ------------------------ */
        if (! $impersonating) {
            return $next($request);
        }

        /* ------------------------ Feature ON; impersonating ------------------------ */

        // Always allow the STOP endpoint.
        if ($isStopAction) {
            return $next($request);
        }

        // Hard block the entire admin area while impersonating (AdminOnly also enforces this).
        if (($name && str_starts_with($name, 'admin.')) || str_starts_with($path, 'admin/')) {
            abort(403, 'Stop impersonation to access the admin area.');
        }

        // Always protect ultra-sensitive endpoints by route name (exact or simple wildcard).
        $blockedRouteNames = [
            'password.update',
            'user-password.update',
            'two-factor.enable',
            'two-factor.disable',
            'two-factor.confirm',
            'two-factor.qr-code',
            'two-factor.recovery-codes',
            'two-factor.codes.show',
            '2fa.codes.regenerate',
            '2fa.codes.download',
            'billing.*', // if you have billing routes
        ];
        if ($name && $this->nameMatchesAny($name, $blockedRouteNames)) {
            abort(403, 'This action is blocked during impersonation.');
        }

        // Path-based fallbacks (when routes aren’t named).
        if (preg_match(
            '#^user/(confirmed-two-factor-authentication|two-factor-authentication|two-factor-recovery-codes)#i',
            $path
        )) {
            abort(403, 'This action is blocked during impersonation.');
        }

        // Read-only unless explicit "full" mode.
        $mode = (string) $request->session()->get('impersonation_mode', 'readonly'); // 'readonly' | 'full'
        $isWriteMethod = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);

        if ($mode !== 'full' && $isWriteMethod) {
            abort(403, 'Read-only while impersonating.');
        }

        return $next($request);
    }

    /** Simple route-name matcher with support for a trailing wildcard ".*". */
    private function nameMatchesAny(string $name, array $patterns): bool
    {
        foreach ($patterns as $pat) {
            // wildcard prefix (e.g., "billing.*")
            if (str_ends_with($pat, '.*')) {
                $prefix = substr($pat, 0, -2);
                if (str_starts_with($name, $prefix)) {
                    return true;
                }
                continue;
            }
            // exact match
            if ($name === $pat) {
                return true;
            }
        }
        return false;
    }
}
