<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';

    // Allow both KV and column-based attributes for mass update safety
    protected $fillable = [
        'key', 'value',
        'app_name', 'app_logo_path',
        'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption',
        'smtp_from_address', 'smtp_from_name',
        'feature_impersonation', 'feature_usernames_editable',
        'security_require_admin_mfa_for_impersonation',
    ];

    /**
     * Map friendly dot-keys -> column names when using "single row / many columns" schema.
     * If you're on a key/value schema, this map is ignored.
     */
    private const MAP = [
        // App
        'app.name'       => 'app_name',
        'app.logo_path'  => 'app_logo_path',

        // SMTP
        'mail.smtp.host'       => 'smtp_host',
        'mail.smtp.port'       => 'smtp_port',
        'mail.smtp.username'   => 'smtp_username',
        'mail.smtp.password'   => 'smtp_password',
        'mail.smtp.encryption' => 'smtp_encryption',
        'mail.from.address'    => 'smtp_from_address',
        'mail.from.name'       => 'smtp_from_name',

        // Features / Security
        'features.impersonation'                        => 'feature_impersonation',
        'features.allow_username_change'                => 'feature_usernames_editable',
        'security.require_admin_mfa_for_impersonation'  => 'security_require_admin_mfa_for_impersonation',
    ];

    /** Cached schema detection (true = KV table with key/value; false = column-based table) */
    private static ?bool $kv = null;

    protected static function isKvSchema(): bool
    {
        if (static::$kv !== null) {
            return static::$kv;
        }

        $hasKey   = Schema::hasColumn('settings', 'key');
        $hasValue = Schema::hasColumn('settings', 'value');

        // KV schema requires both 'key' and 'value' columns present
        static::$kv = $hasKey && $hasValue;
        return static::$kv;
    }

    /**
     * Get a setting (cached 5 minutes).
     * Works with both schemas:
     *  - KV:    SELECT value FROM settings WHERE key=?
     *  - Cols:  SELECT {column} FROM settings LIMIT 1
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "settings.$key";

        return Cache::remember($cacheKey, 300, function () use ($key, $default) {
            if (static::isKvSchema()) {
                $row = static::query()->where('key', $key)->first();
                return $row ? static::decodeValue($row->value, $default) : $default;
            }

            // Column-schema
            $column = static::mapKeyToColumn($key);
            if (!$column || !Schema::hasColumn('settings', $column)) {
                return $default;
            }

            $row = static::query()->first();
            if (!$row) {
                return $default;
            }

            return static::decodeValue($row->{$column}, $default);
        });
    }

    /**
     * Get a boolean setting with a boolean default.
     */
    public static function bool(string $key, bool $default = false): bool
    {
        $val = static::get($key, $default);

        if (is_bool($val)) {
            return $val;
        }

        if (is_string($val)) {
            $lower = strtolower($val);
            if (in_array($lower, ['true', '1', 'yes', 'on'], true))  return true;
            if (in_array($lower, ['false', '0', 'no', 'off'], true)) return false;
        }

        if (is_numeric($val)) {
            return (int) $val === 1;
        }

        return (bool) $val;
    }

    /**
     * Put (upsert) a setting and clear its cache.
     * Arrays/objects are JSON-encoded automatically.
     * Supports both KV and column-based schemas.
     */
    public static function put(string $key, $value): void
    {
        $storeVal = static::encodeValue($value);

        if (static::isKvSchema()) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $storeVal]);
        } else {
            $column = static::mapKeyToColumn($key);

            if (!$column) {
                // Column not mapped: if KV columns also exist, fallback to KV row
                if (Schema::hasColumn('settings', 'key') && Schema::hasColumn('settings', 'value')) {
                    static::query()->updateOrCreate(['key' => $key], ['value' => $storeVal]);
                }
            } else {
                $row = static::query()->first();
                if (!$row) {
                    $row = new static();
                }
                $row->{$column} = $storeVal;
                $row->save();
            }
        }

        static::forget($key);
        Cache::forget('settings.__all');
    }

    /**
     * Forget a single key from cache.
     */
    public static function forget(string $key): void
    {
        Cache::forget("settings.$key");
    }

    /**
     * Return ALL settings as key => decoded value (cached 5 minutes).
     * - KV schema: dump all rows
     * - Col schema: map the single row into dot-keys via MAP
     */
    public static function allPairs(): array
    {
        return Cache::remember('settings.__all', 300, function () {
            $pairs = [];

            if (static::isKvSchema()) {
                foreach (static::query()->get(['key', 'value']) as $row) {
                    $pairs[$row->key] = static::decodeValue($row->value, null);
                }
                return $pairs;
            }

            $row = static::query()->first();
            if (!$row) {
                return $pairs;
            }

            foreach (self::MAP as $dot => $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $pairs[$dot] = static::decodeValue($row->{$col}, null);
                }
            }

            return $pairs;
        });
    }

    /**
     * Convenience DTO exposing frequently-used flags/properties as attributes.
     * Maps dot-keys to simple snake_case properties.
     */
    public static function instance(): object
    {
        return (object) [
            // App
            'app_name'  => static::get('app.name', config('app.name')),
            'app_logo_path' => static::get('app.logo_path'),

            // Features
            'feature_impersonation'                        => static::bool('features.impersonation', false),
            'feature_usernames_editable'                   => static::bool('features.allow_username_change', true),
            'security_require_admin_mfa_for_impersonation' => static::bool('security.require_admin_mfa_for_impersonation', true),

            // SMTP
            'smtp_host'       => static::get('mail.smtp.host'),
            'smtp_port'       => static::get('mail.smtp.port'),
            'smtp_username'   => static::get('mail.smtp.username'),
            'smtp_password'   => static::get('mail.smtp.password'),
            'smtp_encryption' => static::get('mail.smtp.encryption'),
            'smtp_from_name'  => static::get('mail.from.name'),
            'smtp_from_addr'  => static::get('mail.from.address'),
        ];
    }

    /** Map dot-key => column (null if unmapped) */
    private static function mapKeyToColumn(string $key): ?string
    {
        return self::MAP[$key] ?? null;
    }

    /** Decode stored scalar/JSON strings back into PHP values */
    private static function decodeValue($val, $default = null)
    {
        if ($val === null)   return $default;
        if ($val === 'null') return null;

        if (is_string($val) && strlen($val) > 0 && in_array($val[0], ['{', '['], true)) {
            try {
                return json_decode($val, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                // keep as raw string
            }
        }

        if ($val === 'true')  return true;
        if ($val === 'false') return false;

        return $val;
    }

    /** Encode arrays/objects to JSON; booleans/null to strings; leave scalars as-is */
    private static function encodeValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($value === null)  return 'null';
        if ($value === true)  return 'true';
        if ($value === false) return 'false';
        return $value;
    }
}
