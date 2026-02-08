<?php

namespace App\Helpers;

use App\Models\Setting;

class Feature
{
    /**
     * Check if a feature is enabled.
     *
     * @param string $key
     * @return bool
     */
    public static function enabled(string $key): bool
    {
        // 1. Try exact key match (cached by Setting class)
        // We use get() checks to distinguish between "doesn't exist" (null) and "exists but false" (false/0)
        // although bool() implementation in Setting handles defaults nicely.
        if (Setting::get($key) !== null) {
            return Setting::bool($key);
        }

        // 2. If no exact match and no dot notation, try 'features.' prefix
        // This handles cases like 'support_enabled' mapping to 'features.support_enabled'
        if (!str_contains($key, '.')) {
            return Setting::bool("features.{$key}");
        }

        return false;
    }
}
