<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

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
        // Cache mapping of keys to their boolean values for 60 minutes
        // We act on the assumption that settings don't change every second.
        // If they do, we should clear this cache in the SystemSetting observer/controller.
        return Cache::remember("feature_flag:{$key}", 60 * 60, function () use ($key) {
            $setting = SystemSetting::where('key', $key)->first();

            if (!$setting) {
                return false;
            }

            $value = $setting->value;

            // Handle string booleans if stored as 'true'/'false'
            if (is_string($value)) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            return (bool) $value;
        });
    }
}
