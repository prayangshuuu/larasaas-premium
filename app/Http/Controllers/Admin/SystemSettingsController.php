<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAppSettingsRequest;
use App\Http\Requests\Admin\UpdateSmtpSettingsRequest;
use App\Http\Requests\Admin\UpdateFeatureFlagsRequest;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    /**
     * Back-compat: old route may still hit index(). Delegate to edit().
     */
    public function index()
    {
        return $this->edit();
    }

    /**
     * GET /admin/settings  (admin.settings.edit)
     * Load settings for the three sections: App, SMTP, Feature Flags.
     */
    public function edit()
    {
        $s = Setting::instance();

        return view('admin.settings.index', [
            // For the app card
            'app_name' => $s->app_name ?? config('app.name'),
            'app_logo' => $s->app_logo_path ?? null,

            // For the SMTP card
            'smtp' => [
                'host'       => $s->smtp_host,
                'port'       => $s->smtp_port,
                'username'   => $s->smtp_username,
                'password'   => $s->smtp_password,
                'encryption' => $s->smtp_encryption,   // null|tls|ssl
                'from_name'  => $s->smtp_from_name,
                'from_addr'  => $s->smtp_from_addr,    // ← correct property name
            ],

            // For the Features card
            'features' => [
                'impersonation'                       => (bool) $s->feature_impersonation,
                'allow_username_change'               => (bool) $s->feature_usernames_editable,
                'require_admin_mfa_for_impersonation' => Setting::bool('security.require_admin_mfa_for_impersonation', true),
            ],
        ]);
    }

    /**
     * POST /admin/settings/app  (admin.settings.app.update)
     * Saves app name, and (optionally) logo if included in the same form.
     */
    public function updateApp(UpdateAppSettingsRequest $request)
    {
        $data = $request->validated();

        // Save app name
        Setting::put('app.name', $data['app_name']);

        // Optional inline logo upload if provided in this form
        if ($request->hasFile('app_logo')) {
            $this->storeLogoFile($request);
        }

        $this->logAudit('settings.update', 'Updated app settings', [
            'keys' => array_keys($data),
        ]);

        return back()->with('status', 'settings-app-updated');
    }

    /**
     * POST /admin/settings/app/logo  (admin.settings.app.logo)
     * Dedicated endpoint for the "Change" button over the avatar preview.
     */
    public function uploadLogo(Request $request)
    {
        // NOTE: don't use 'image' rule if you want to allow SVG
        $request->validate([
            'app_logo' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ]);

        $path = $this->storeLogoFile($request);

        $this->logAudit('settings.update', 'Updated app logo', [
            'path' => $path,
        ]);

        return back()->with('status', 'settings-app-logo-updated');
    }

    /**
     * POST /admin/settings/smtp  (admin.settings.smtp.update)
     * Persists transport + from settings into the settings KV store.
     */
    public function updateSmtp(UpdateSmtpSettingsRequest $request)
    {
        $v = $request->validated();

        Setting::put('mail.smtp.host',       $v['host']);
        Setting::put('mail.smtp.port',       $v['port']);
        Setting::put('mail.smtp.username',   $v['username'] ?? null);
        Setting::put('mail.smtp.password',   $v['password'] ?? null);
        Setting::put('mail.smtp.encryption', $v['encryption'] ?? null);
        Setting::put('mail.from.name',       $v['from_name'] ?? null);
        Setting::put('mail.from.address',    $v['from_addr'] ?? null);

        $this->logAudit('settings.update', 'Updated SMTP settings', [
            'host' => $v['host'],
            'port' => $v['port'],
        ]);

        return back()->with('status', 'settings-smtp-updated');
    }

    /**
     * POST /admin/settings/features  (admin.settings.features.update)
     * Persists feature flags (booleans) to the settings KV store.
     */
    public function updateFeatures(UpdateFeatureFlagsRequest $request)
    {
        $v = $request->validated();

        Setting::put('features.impersonation',                       (bool) ($v['impersonation'] ?? false));
        Setting::put('features.allow_username_change',               (bool) ($v['allow_username_change'] ?? false));
        Setting::put('security.require_admin_mfa_for_impersonation', (bool) ($v['require_admin_mfa_for_impersonation'] ?? true));

        $this->logAudit('settings.update', 'Updated feature flags', $v);

        return back()->with('status', 'settings-features-updated');
    }

    /**
     * Store/replace the logo file under the public disk and persist "storage/..." path in settings.
     * Returns the public path saved into 'app.logo_path'.
     */
    private function storeLogoFile(Request $request): string
    {
        $file = $request->file('app_logo');
        $path = $file->store('logos', 'public'); // storage/app/public/logos/...

        // Remove old managed logo if any
        $old = Setting::get('app.logo_path');
        if (is_string($old) && str_starts_with($old, 'storage/')) {
            $oldRel = substr($old, strlen('storage/'));
            if ($oldRel && Storage::disk('public')->exists($oldRel)) {
                Storage::disk('public')->delete($oldRel);
            }
        }

        $publicPath = "storage/$path";
        Setting::put('app.logo_path', $publicPath);

        return $publicPath;
    }

    /**
     * Safe audit helper. Uses AuditLog::log() when present, or falls back to create().
     */
    private function logAudit(string $action, string $description, array $metadata = []): void
    {
        $actorId   = Auth::id();
        $ip        = request()->ip();
        $userAgent = request()->userAgent();

        try {
            if (method_exists(\App\Models\AuditLog::class, 'log')) {
                \App\Models\AuditLog::log($action, $actorId, null, $description, $metadata + [
                        'ip' => $ip,
                        'ua' => $userAgent,
                    ]);
                return;
            }

            \App\Models\AuditLog::query()->create([
                'actor_id'    => $actorId,
                'target_type' => null,
                'target_id'   => null,
                'action'      => $action,
                'description' => $description,
                'ip_address'  => $ip,
                'user_agent'  => $userAgent,
                'metadata'    => $metadata ?: null,
            ]);
        } catch (\Throwable $e) {
            // Do not block UX if logging fails.
        }
    }
}
