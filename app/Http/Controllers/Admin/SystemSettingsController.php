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
     * Back-compat: some routes may still hit index(); delegate to edit().
     */
    public function index()
    {
        return $this->edit();
    }

    /**
     * GET /admin/settings  (admin.settings.index)
     * Provide data for App / SMTP / Feature Flags cards.
     */
    public function edit()
    {
        $s = Setting::instance();

        // Back-compat single logo fallback for old templates
        $singleLogo = $s->app_logo_dark_path
            ?? $s->app_logo_light_path
            ?? $s->app_logo_path;

        return view('admin.settings.index', [
            // App identity
            'app_name'       => $s->app_name ?? config('app.name'),
            'app_logo'       => $singleLogo ?: null,             // legacy single
            'app_logo_light' => $s->app_logo_light_path ?: null, // new
            'app_logo_dark'  => $s->app_logo_dark_path ?: null,  // new

            // SMTP (for the form)
            'smtp' => [
                'host'       => $s->smtp_host,
                'port'       => $s->smtp_port,
                'username'   => $s->smtp_username,
                'password'   => $s->smtp_password,    // not shown; just present for completeness
                'encryption' => $s->smtp_encryption,  // '', 'tls', 'ssl'
                'from_name'  => $s->smtp_from_name,
                'from_addr'  => $s->smtp_from_addr,
            ],

            // Features
            'features' => [
                'impersonation'                       => Setting::bool('features.impersonation', false),
                'allow_username_change'               => Setting::bool('features.allow_username_change', true),
                'require_admin_mfa_for_impersonation' => Setting::bool('security.require_admin_mfa_for_impersonation', true),
            ],
        ]);
    }

    /**
     * POST /admin/settings/app  (admin.settings.app.update)
     * Saves app name; also handles inline logo uploads if provided.
     * Accepts (optional): app_logo_light, app_logo_dark, app_logo (legacy single).
     */
    public function updateApp(UpdateAppSettingsRequest $request)
    {
        $data = $request->validated();

        // Save name
        Setting::put('app.name', $data['app_name']);

        // Optional inline uploads on the same form
        $logosChanged = $this->handleLogoUploads($request, false);

        $this->logAudit('settings.update', 'Updated app settings', [
            'name_changed'  => true,
            'logos_changed' => $logosChanged,
        ]);

        return redirect()->route('admin.settings.index')->with('status', 'settings-app-updated');
    }

    /**
     * POST /admin/settings/app/logo  (admin.settings.app.logo)
     * Accepts one or both of: app_logo_light, app_logo_dark (and legacy app_logo).
     */
    public function uploadLogo(Request $request)
    {
        // Build rules only for the fields actually sent
        $rules = [];
        foreach (['app_logo', 'app_logo_light', 'app_logo_dark'] as $f) {
            if ($request->hasFile($f)) {
                $rules[$f] = ['file', 'mimes:png,jpg,jpeg,webp,svg', 'max:4096'];
            }
        }

        if (empty($rules)) {
            return redirect()->route('admin.settings.index')->with('error', 'Please choose a logo file to upload.');
        }

        $request->validate($rules);

        $changed = $this->handleLogoUploads($request, true);

        $this->logAudit('settings.update', 'Updated app logo(s)', [
            'light_uploaded' => $request->hasFile('app_logo_light') || $request->hasFile('app_logo'),
            'dark_uploaded'  => $request->hasFile('app_logo_dark')  || $request->hasFile('app_logo'),
        ]);

        return redirect()->route('admin.settings.index')
            ->with('status', $changed ? 'settings-app-logo-updated' : 'settings-app-updated');
    }

    /**
     * POST /admin/settings/smtp  (admin.settings.smtp.update)
     * Persists transport + from settings; preserves password if field left blank.
     */
    public function updateSmtp(UpdateSmtpSettingsRequest $request)
    {
        $v = $request->validated();

        Setting::put('mail.smtp.host',       $v['host']);
        Setting::put('mail.smtp.port',       $v['port']);
        Setting::put('mail.smtp.username',   $v['username'] ?? null);
        Setting::put('mail.smtp.encryption', $v['encryption'] ?? null);
        Setting::put('mail.from.name',       $v['from_name'] ?? null);
        Setting::put('mail.from.address',    $v['from_addr'] ?? null);

        // Only overwrite password if provided
        if (array_key_exists('password', $v) && $v['password'] !== '') {
            Setting::put('mail.smtp.password', $v['password']);
        }

        $this->logAudit('settings.update', 'Updated SMTP settings', [
            'host' => $v['host'],
            'port' => $v['port'],
        ]);

        return redirect()->route('admin.settings.index')->with('status', 'settings-smtp-updated');
    }

    /**
     * POST /admin/settings/features  (admin.settings.features.update)
     * Persists feature flags (booleans).
     */
    public function updateFeatures(UpdateFeatureFlagsRequest $request)
    {
        $v = $request->validated();

        Setting::put('features.impersonation',                       (bool) ($v['impersonation'] ?? false));
        Setting::put('features.allow_username_change',               (bool) ($v['allow_username_change'] ?? false));
        Setting::put('security.require_admin_mfa_for_impersonation', (bool) ($v['require_admin_mfa_for_impersonation'] ?? true));

        $this->logAudit('settings.update', 'Updated feature flags', $v);

        return redirect()->route('admin.settings.index')->with('status', 'settings-features-updated');
    }

    /* ---------------------------------------------------------------------
     | Internal helpers
     | ------------------------------------------------------------------ */

    /**
     * Process any provided logo fields and update settings atomically.
     * - app_logo_light  → app.logo_light_path
     * - app_logo_dark   → app.logo_dark_path
     * - app_logo (legacy) → sets BOTH above and keeps app.logo_path for BC
     *
     * Also keeps a sensible legacy fallback:
     *   app.logo_path = light (if present) else dark (if present) else unchanged
     *
     * @return bool True if at least one file was processed.
     */
    private function handleLogoUploads(Request $request, bool $throwIfNone = false): bool
    {
        $processed = false;

        // Legacy single file: use for both & keep legacy key in sync
        if ($request->hasFile('app_logo')) {
            $lightOld = Setting::get('app.logo_light_path');
            $darkOld  = Setting::get('app.logo_dark_path');

            $publicLight = $this->storeManagedAndReturnUrl($request->file('app_logo'), $lightOld);
            $publicDark  = $this->storeManagedAndReturnUrl($request->file('app_logo'), $darkOld);

            Setting::put('app.logo_light_path', $publicLight);
            Setting::put('app.logo_dark_path',  $publicDark);
            Setting::put('app.logo_path',       $publicLight); // legacy single uses "light" by convention

            $processed = true;
        }

        if ($request->hasFile('app_logo_light')) {
            $old    = Setting::get('app.logo_light_path');
            $public = $this->storeManagedAndReturnUrl($request->file('app_logo_light'), $old);
            Setting::put('app.logo_light_path', $public);

            // keep legacy single if it was previously empty
            if (!Setting::get('app.logo_path')) {
                Setting::put('app.logo_path', $public);
            }
            $processed = true;
        }

        if ($request->hasFile('app_logo_dark')) {
            $old    = Setting::get('app.logo_dark_path');
            $public = $this->storeManagedAndReturnUrl($request->file('app_logo_dark'), $old);
            Setting::put('app.logo_dark_path', $public);

            // if legacy single still empty and light isn't set, use dark as fallback
            if (!Setting::get('app.logo_path') && !Setting::get('app.logo_light_path')) {
                Setting::put('app.logo_path', $public);
            }
            $processed = true;
        }

        if (!$processed && $throwIfNone) {
            abort(422, 'No logo file uploaded.');
        }

        return $processed;
    }

    /**
     * Store a file on the public disk, delete previous managed file, and return the PUBLIC URL.
     * - Saves to storage/app/public/logos/...
     * - Returns something like "/storage/logos/xyz.png" (or full URL depending on disk config).
     */
    private function storeManagedAndReturnUrl($file, ?string $oldPublicUrl): string
    {
        // Store new file
        $storedPath = $file->store('logos', 'public'); // e.g. logos/xyz.png

        // Delete previously-managed file if it existed
        $oldRel = $this->publicDiskRelativeFromUrl($oldPublicUrl);
        if ($oldRel && Storage::disk('public')->exists($oldRel)) {
            Storage::disk('public')->delete($oldRel);
        }

        // Return a public URL (portable across environments)
        return Storage::disk('public')->url($storedPath); // typically "/storage/logos/xyz.png"
    }

    /**
     * Convert a public URL (absolute or relative) into a path relative to the "public" disk.
     * Handles:
     *   - "https://example.com/storage/logos/abc.png"
     *   - "/storage/logos/abc.png"
     *   - "storage/logos/abc.png"
     */
    private function publicDiskRelativeFromUrl(?string $url): ?string
    {
        if (!is_string($url) || $url === '') {
            return null;
        }

        // If it's an absolute URL, get just the path part.
        $path = parse_url($url, PHP_URL_PATH) ?: $url;

        // Normalize leading slash
        if (str_starts_with($path, '/')) {
            $path = ltrim($path, '/');
        }

        // Strip the "storage/" prefix to get the disk-relative path
        if (str_starts_with($path, 'storage/')) {
            return substr($path, strlen('storage/')); // e.g. "logos/abc.png"
        }

        // Already disk-relative (unlikely, but safe)
        return $path;
    }

    /**
     * Safe audit helper (uses AuditLog::log() if available; otherwise create()).
     */
    private function logAudit(string $action, string $description, array $metadata = []): void
    {
        $actorId   = Auth::id();
        $ip        = request()->ip();
        $userAgent = request()->userAgent();

        try {
            if (method_exists(AuditLog::class, 'log')) {
                AuditLog::log($action, $actorId, null, $description, $metadata + [
                        'ip' => $ip,
                        'ua' => $userAgent,
                    ]);
                return;
            }

            AuditLog::query()->create([
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
            // Never block UX on logging failure
        }
    }
}
