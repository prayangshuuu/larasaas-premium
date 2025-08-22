<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ImpersonationGuard
{
    /**
     * If impersonating:
     *  - Default mode: read-only (blocks POST/PUT/PATCH/DELETE)
     *  - "Full" mode allowed only when a valid access code was presented.
     *  - Still block ultra-sensitive actions by route name anyway.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('impersonated_by')) {
            return $next($request);
        }

        $mode = session('impersonation_mode', 'readonly'); // readonly|full

        // Always protect ultra-sensitive endpoints by route name
        $blockedRoutes = [
            'password.update',
            'two-factor.enable',
            'two-factor.disable',
            'two-factor.recovery-codes',
            'billing.*', // wildcard example if you have billing routes
            'theme.update', // optional
        ];
        $name = optional($request->route())->getName();
        if ($name) {
            foreach ($blockedRoutes as $pat) {
                if (str_ends_with($pat, '.*')) {
                    $prefix = substr($pat, 0, -2);
                    if (str_starts_with($name, $prefix)) {
                        abort(403, 'This action is blocked during impersonation.');
                    }
                } elseif ($name === $pat) {
                    abort(403, 'This action is blocked during impersonation.');
                }
            }
        }

        // Read-only policy unless mode is 'full'
        if ($mode !== 'full' && in_array($request->method(), ['POST','PUT','PATCH','DELETE'])) {
            abort(403, 'Read-only while impersonating.');
        }

        return $next($request);
    }
}
