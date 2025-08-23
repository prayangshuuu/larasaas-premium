<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';

    // Keep mass-assignable minimal & schema-agnostic
    protected $fillable = ['key', 'value'];

    /**
     * Dot-key → column mapping (only used if you actually have a column-based schema).
     * Includes dual-logo keys; keeps legacy single-logo for backward compatibility.
     */
    private const MAP = [
        // App
        'app.name'             => 'app_name',
        'app.logo_path'        => 'app_logo_path',        // legacy single-logo (BC)
        'app.logo_light_path'  => 'app_logo_light_path',  // light theme logo
        'app.logo_dark_path'   => 'app_logo_dark_path',   // dark theme logo

        // SMTP
        'mail.smtp.host'       => 'smtp_host',
        'mail.smtp.port'       => 'smtp_port',
        'mail.smtp.username'   => 'smtp_username',
        'mail.smtp.password'   => 'smtp_password',
        'mail.smtp.encryption' => 'smtp_encryption',
        'mail.from.address'    => 'smtp_from_address',
        'mail.from.name'       => 'smtp_from_name',

        // Features / Security
        'features.impersonation'                       => 'feature_impersonation',
        'features.allow_username_change'               => 'feature_usernames_editable',
        'security.require_admin_mfa_for_impersonation' => 'security_require_admin_mfa_for_impersonation',
    ];

    /** Cache the detection result (true = KV table) */
    private static ?bool $isKv = null;

    /** Detect whether the table is KV (has key + value). */
    protected static function isKvSchema(): bool
    {
        if (static::$isKv !== null) {
            return static::$isKv;
        }

        $hasKey   = Schema::hasColumn('settings', 'key');
        $hasValue = Schema::hasColumn('settings', 'value');

        static::$isKv = $hasKey && $hasValue;
        return static::$isKv;
    }

    /** Map dot-key → column name (null if unmapped). */
    private static function mapKeyToColumn(string $key): ?string
    {
        return self::MAP[$key] ?? null;
    }

    /**
     * Get a setting (cached 5 minutes).
     * - KV schema: fetch by {key}
     * - Column schema: fetch from first row's mapped column (if it exists)
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "settings.$key";

        return Cache::remember($cacheKey, 300, function () use ($key, $default) {
            if (static::isKvSchema()) {
                $row = static::query()->where('key', $key)->first();
                return $row ? static::decodeValue($row->value, $default) : $default;
            }

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

    /** Get a boolean setting with a sane default. */
    public static function bool(string $key, bool $default = false): bool
    {
        $val = static::get($key, $default);

        if (is_bool($val)) return $val;

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
     * Put (upsert) a setting and clear caches.
     * - KV schema: upsert row {key,value}
     * - Column schema: if mapped column exists, update first row; else fallback to KV row if available
     *   (safe no-op if neither mapped column nor KV columns exist).
     */
    public static function put(string $key, $value): void
    {
        if (static::isKvSchema()) {
            $storeVal = static::encodeValue($value);
            static::query()->updateOrCreate(['key' => $key], ['value' => $storeVal]);
        } else {
            $column = static::mapKeyToColumn($key);

            if ($column && Schema::hasColumn('settings', $column)) {
                $row = static::query()->first() ?: new static();
                // IMPORTANT: for column schema, coerce to the right storage type per column
                $row->{$column} = static::encodeForColumn($column, $value);
                $row->save();
            } elseif (Schema::hasColumn('settings', 'key') && Schema::hasColumn('settings', 'value')) {
                // Fallback to KV row even if we initially detected column schema
                $storeVal = static::encodeValue($value);
                static::query()->updateOrCreate(['key' => $key], ['value' => $storeVal]);
            } else {
                // Nowhere to store (neither mapped col nor KV). Do nothing gracefully.
                return;
            }
        }

        static::forget($key);
        Cache::forget('settings.__all');
    }

    /** Forget a single key from cache. */
    public static function forget(string $key): void
    {
        Cache::forget("settings.$key");
    }

    /**
     * Return ALL settings as key => decoded value (cached 5 minutes).
     * - KV schema: dump all rows
     * - Column schema: map first row to dot-keys via MAP (only existing columns)
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
            if (!$row) return $pairs;

            foreach (self::MAP as $dot => $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $pairs[$dot] = static::decodeValue($row->{$col}, null);
                }
            }
            return $pairs;
        });
    }

    /**
     * Convenience DTO exposing common settings on simple properties.
     */
    public static function instance(): object
    {
        return (object) [
            // App (legacy + new dual-logo)
            'app_name'            => static::get('app.name', config('app.name')),
            'app_logo_path'       => static::get('app.logo_path'),        // legacy single
            'app_logo_light_path' => static::get('app.logo_light_path'),  // new
            'app_logo_dark_path'  => static::get('app.logo_dark_path'),   // new

            // Features / security
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

    /* ---------------------------- Value coders ---------------------------- */

    /** Decode stored scalar/JSON strings back into PHP values. */
    private static function decodeValue($val, $default = null)
    {
        if ($val === null)   return $default;
        if ($val === 'null') return null;

        if (is_string($val) && $val !== '' && in_array($val[0], ['{', '['], true)) {
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

    /** Encode arrays/objects to JSON; booleans/null to strings; leave scalars as-is (KV schema). */
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

    /**
     * Encode with the right storage type for a known COLUMN.
     * - Booleans → 1/0 (also accepts "true"/"false","1"/"0","yes"/"no","on"/"off")
     * - smtp_port → int|null
     * - arrays/objects → json
     * - scalars → as-is
     */
    private static function encodeForColumn(string $column, $value)
    {
        // Known boolean columns
        static $boolCols = [
            'feature_impersonation',
            'feature_usernames_editable',
            'security_require_admin_mfa_for_impersonation',
        ];
        if (in_array($column, $boolCols, true)) {
            $b = static::parseBoolLike($value);
            return $b ? 1 : 0; // default false if unparsable
        }

        // Known integer columns
        if ($column === 'smtp_port') {
            if ($value === null || $value === '') return null;
            return (int) $value;
        }

        // String-ish columns: host/user/pass/encryption/names/paths etc.
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        // If a raw boolean sneaks in here
        if ($value === true)  return 1;
        if ($value === false) return 0;

        return $value; // string|int|null as-is
    }

    /** Parse many boolean-like inputs to true/false. */
    private static function parseBoolLike($val): bool
    {
        if (is_bool($val)) return $val;
        if (is_int($val))  return $val === 1;
        if (is_numeric($val)) return ((int)$val) === 1;

        if (is_string($val)) {
            $lower = trim(strtolower($val));
            if (in_array($lower, ['1','true','yes','on'], true))  return true;
            if (in_array($lower, ['0','false','no','off',''], true)) return false;
        }

        return (bool)$val;
    }
}
