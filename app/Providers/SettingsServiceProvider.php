<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            // App
            if ($name = Setting::get('app.name')) {
                config(['app.name' => $name]);
            }

            // Features
            $impersonation = Setting::get('features.impersonation');
            if (!is_null($impersonation)) {
                config(['features.impersonation' => (bool) $impersonation]);
            }
            $allowUsername = Setting::get('features.allow_username_change');
            if (!is_null($allowUsername)) {
                config(['features.allow_username_change' => (bool) $allowUsername]);
            }
            $mfaForImpersonate = Setting::get('security.require_admin_mfa_for_impersonation');
            if (!is_null($mfaForImpersonate)) {
                config(['security.require_admin_mfa_for_impersonation' => (bool) $mfaForImpersonate]);
            }

            // SMTP (mail)
            if ($host = Setting::get('mail.smtp.host')) {
                config(['mail.default' => 'smtp']);
                config(['mail.mailers.smtp.host' => $host]);
            }
            if ($port = Setting::get('mail.smtp.port')) {
                config(['mail.mailers.smtp.port' => (int) $port]);
            }
            if (!is_null(Setting::get('mail.smtp.encryption'))) {
                config(['mail.mailers.smtp.encryption' => Setting::get('mail.smtp.encryption') ?: null]);
            }
            if ($username = Setting::get('mail.smtp.username')) {
                config(['mail.mailers.smtp.username' => $username]);
            }
            if ($password = Setting::get('mail.smtp.password')) {
                config(['mail.mailers.smtp.password' => $password]);
            }
            if ($fromAddr = Setting::get('mail.from.address')) {
                config(['mail.from.address' => $fromAddr]);
            }
            if ($fromName = Setting::get('mail.from.name')) {
                config(['mail.from.name' => $fromName]);
            }
        } catch (\Throwable $e) {
            Log::warning('SettingsServiceProvider failed to load settings: '.$e->getMessage());
        }
    }
}
