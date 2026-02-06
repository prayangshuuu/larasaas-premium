<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePlanLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitKey): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Load active subscription with plan
        $subscription = $user->subscriptions()->where('status', 'active')->with('plan')->first();

        if (!$subscription || !$subscription->plan) {
            // No active subscription means 0 limit for most paid features
            // In a real app, you might have a "Free Tier" logic here.
            // For now, we strictly enforce "Active Subscription Required for this feature".
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Active subscription required.'], 403);
            }
            abort(403, 'Active subscription required.');
        }

        // Get limit from Plan (default to 0 if not set)
        $limit = $subscription->plan->getFeatureLimit($limitKey, 0);

        // Get current usage from User
        $usage = $user->getUsage($limitKey);

        if ($usage >= $limit) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Plan limit reached for this feature.'], 429);
            }
            abort(429, 'Plan limit reached for this feature.');
        }

        return $next($request);
    }
}
