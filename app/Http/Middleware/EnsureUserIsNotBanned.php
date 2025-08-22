<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsNotBanned
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // allow admin while impersonating (optional)
        if ($request->session()->has('impersonated_by')) {
            return $next($request);
        }

        if ($user && $user->isBanned()) {
            Auth::logout();
            return redirect()->route('login')->with('status', 'Your account is banned.');
        }

        return $next($request);
    }
}
