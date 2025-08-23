<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    /**
     * Ensures only real admins can access /admin/*.
     *
     * While impersonating:
     *  - Block ALL admin routes.
     *  - EXCEPT allow the stop endpoint so the admin can exit impersonation.
     *
     * When not impersonating:
     *  - Require the user to actually be an admin (via User::isAdmin() or is_admin column).
     */
    public function handle(Request $request, Closure $next)
    {
        $route     = $request->route();
        $routeName = $route ? $route->getName() : null;
        // Fallback to URI check in case route names aren’t present
        $uri       = $route ? ltrim($route->uri(), '/') : ltrim($request->path(), '/');

        /* ---------------- Impersonation guard for admin area ---------------- */
        if ($request->session()->has('impersonated_by')) {
            // Allow ONLY the stop endpoint inside /admin while impersonating.
            $isStop = ($routeName === 'admin.impersonate.stop')
                || preg_match('#^admin/impersonate/stop$#i', $uri);

            if ($isStop) {
                return $next($request);
            }

            // Block all other admin routes during impersonation.
            abort(403, 'Stop impersonation to access the admin area.');
        }

        /* ------------------------ Regular admin check ----------------------- */
        $user    = $request->user();
        $isAdmin = false;

        if ($user) {
            // Prefer a model method if it exists; otherwise fall back to a boolean column.
            $isAdmin = method_exists($user, 'isAdmin')
                ? (bool) $user->isAdmin()
                : (bool) ($user->is_admin ?? false);
        }

        if (! $isAdmin) {
            abort(403, 'Admins only.');
        }

        return $next($request);
    }
}
