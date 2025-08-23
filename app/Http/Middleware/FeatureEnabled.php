<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class FeatureEnabled
{
    /**
     * Gate routes behind DB-driven feature flags.
     *
     * Basic usage (most common):
     *   ->middleware('feature:features.impersonation')
     *
     * Advanced usage (multiple keys):
     *   ->middleware('feature:features.a,features.b')        // ALL must be true (default)
     *   ->middleware('feature:features.a,features.b,any')    // ANY may be true
     *   ->middleware('feature:features.a|features.b,any')    // '|' also supported inside a param
     *   ->middleware('feature:!features.impersonation')      // require the flag to be OFF
     *
     * Behavior:
     * - When the check fails, the route is hidden (404) for all methods.
     */
    public function handle(Request $request, Closure $next, ...$params)
    {
        // No key provided → hide route
        if (empty($params)) {
            abort(404);
        }

        // Determine mode ('all' by default; allow passing as last param)
        $last = strtolower(trim(end($params)));
        $mode = in_array($last, ['any', 'all'], true) ? $last : 'all';
        if ($mode !== $last) {
            // last wasn't mode; leave params as-is
        } else {
            array_pop($params); // remove the mode token
        }

        // Collect keys (support comma-separated middleware params and '|' inside a token)
        $keys = [];
        foreach ($params as $p) {
            foreach (preg_split('/[|]/', (string) $p, -1, PREG_SPLIT_NO_EMPTY) as $piece) {
                $k = trim($piece);
                if ($k !== '') {
                    $keys[] = $k;
                }
            }
        }

        if (empty($keys)) {
            abort(404);
        }

        // Evaluate each key; support negation via leading '!' (meaning the feature must be OFF)
        $results = [];
        foreach ($keys as $k) {
            $negate = false;
            if (str_starts_with($k, '!')) {
                $negate = true;
                $k = ltrim($k, '!');
            }

            // Read boolean (defaults to false if missing)
            $on = Setting::bool($k, false);

            $results[] = $negate ? ! $on : $on;
        }

        // Decide pass/fail
        $pass = $mode === 'any'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true); // 'all' mode

        if (! $pass) {
            abort(404);
        }

        return $next($request);
    }
}
