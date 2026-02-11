<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Boot: load database-stored settings into config() so the app
     * reflects admin panel changes without touching .env.
     */
    public function boot(): void
    {
        try {
            // Bail if the settings table doesn't exist yet (fresh install / before migrations)
            if (! Schema::hasTable('settings')) {
                return;
            }

            // Read settings directly from DB (bypass Setting model cache)
            // to guarantee every request gets the latest values.
            $row = DB::table('settings')->first();

            if (! $row) {
                // No row yet — seed a default so future writes have something to update
                DB::table('settings')->insert([
                    'app_name'   => config('app.name', 'Laravel'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return;
            }

            // ── App Identity ──────────────────────────────────────────
            if (! empty($row->app_name)) {
                config(['app.name' => $row->app_name]);
            }

            // ── SMTP / Mail ───────────────────────────────────────────
            if (! empty($row->smtp_host)) {
                config([
                    'mail.default'              => 'smtp',
                    'mail.mailers.smtp.host'     => $row->smtp_host,
                ]);
            }

            if (! empty($row->smtp_port)) {
                config(['mail.mailers.smtp.port' => (int) $row->smtp_port]);
            }

            // encryption can be empty string (= none), tls, or ssl
            if (property_exists($row, 'smtp_encryption')) {
                config(['mail.mailers.smtp.encryption' => $row->smtp_encryption ?: null]);
            }

            if (! empty($row->smtp_username)) {
                config(['mail.mailers.smtp.username' => $row->smtp_username]);
            }

            if (! empty($row->smtp_password)) {
                config(['mail.mailers.smtp.password' => $row->smtp_password]);
            }

            if (! empty($row->smtp_from_address)) {
                config(['mail.from.address' => $row->smtp_from_address]);
            }

            if (! empty($row->smtp_from_name)) {
                config(['mail.from.name' => $row->smtp_from_name]);
            }

        } catch (\Throwable $e) {
            // Never break boot; log and move on (e.g. DB not reachable, missing columns)
            Log::warning('SettingsServiceProvider: ' . $e->getMessage());
        }
    }
}
