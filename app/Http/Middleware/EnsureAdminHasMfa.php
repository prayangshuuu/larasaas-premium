<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminHasMfa
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();
        if (!$u || !$u->isAdmin()) {
            abort(403, 'Admins only.');
        }
        // Fortify stores TOTP secret in two_factor_secret
        if (empty($u->two_factor_secret)) {
            // You can redirect to profile if you prefer
            abort(403, 'Admin MFA required to impersonate.');
        }
        return $next($request);
    }
}
