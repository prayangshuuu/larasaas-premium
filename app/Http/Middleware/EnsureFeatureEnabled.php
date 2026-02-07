<?php

namespace App\Http\Middleware;

use App\Helpers\Feature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature, string $status = '404'): Response
    {
        if (!Feature::enabled($feature)) {
            abort((int) $status);
        }

        return $next($request);
    }
}
