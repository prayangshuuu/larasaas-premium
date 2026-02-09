<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class Feature
{
    /**
     * Cache key for all system settings.
     */
    private const CACHE_KEY = 'system_settings_global';

    /**
     * Cache TTL: 24 hours in seconds.
     */
    private const CACHE_TTL = 86400;

    /**
     * Check if a feature is enabled.
     *
     * @param string $key Feature key (e.g., 'support_enabled', 'subscription_module_enabled')
     * @return bool
     */
    public static function enabled(string $key): bool
    {
        $settings = static::getAllSettings();

        // Return the boolean value if key exists, otherwise false
        if (array_key_exists($key, $settings)) {
            return static::toBool($settings[$key]);
        }

        return false;
    }

    /**
     * Get all settings from cache or database.
     *
     * @return array<string, mixed>
     */
    public static function getAllSettings(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $settings = [];

            foreach (SystemSetting::all() as $setting) {
                $settings[$setting->key] = $setting->value;
            }

            return $settings;
        });
    }

    /**
     * Clear the settings cache.
     * Call this after updating any system settings.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get the cache key (for external use if needed).
     *
     * @return string
     */
    public static function getCacheKey(): string
    {
        return self::CACHE_KEY;
    }

    /**
     * Convert a value to boolean.
     * Handles: true/false, 1/0, "true"/"false", "1"/"0", "yes"/"no", "on"/"off"
     *
     * @param mixed $value
     * @return bool
     */
    private static function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            $lower = strtolower(trim($value));
            return in_array($lower, ['true', '1', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    /**
     * Fake the feature flags for testing.
     *
     * @param array $features
     * @return void
     */
    public static function fake(array $features): void
    {
        Cache::put(self::CACHE_KEY, $features, self::CACHE_TTL);
    }
}
