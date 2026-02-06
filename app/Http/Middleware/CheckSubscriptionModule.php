<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = Cache::rememberForever('system_setting.subscription_module_enabled', function () {
            // Retrieve string/bool value and normalize checks
            $val = SystemSetting::where('key', 'subscription_module_enabled')->value('value');
            // Check for 'true', true, or 1
            return $val === 'true' || $val === true || $val === 1;
        });

        if (!$enabled) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Maintenance Mode: Subscriptions are currently disabled.'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Subscriptions are currently disabled.');
        }

        return $next($request);
    }
}
