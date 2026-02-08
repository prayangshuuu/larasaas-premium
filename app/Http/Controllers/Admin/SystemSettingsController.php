<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAppSettingsRequest;
use App\Http\Requests\Admin\UpdateFeatureFlagsRequest;
use App\Http\Requests\Admin\UpdateSmtpSettingsRequest;
use App\Helpers\Feature;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

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
     * Provide data for App / SMTP / Feature Flags cards + API tokens card.
     */
    public function edit()
    {
        $s = Setting::instance();

        // Back-compat single logo fallback for old templates
        $singleLogo = $s->app_logo_dark_path
            ?? $s->app_logo_light_path
            ?? $s->app_logo_path;

        // Base columns to select for tokens
        $select = [
            'id',
            'name',
            'abilities',
            'last_used_at',
            'expires_at',
            'created_at',
        ];

        // Only add reveal columns if they exist (prevents "Unknown column" SQL error)
        if (Schema::hasColumns('personal_access_tokens', [
            'token_plain_encrypted',
            'token_plain_show_count',
            'token_plain_last_shown_at',
        ])) {
            $select = array_merge($select, [
                'token_plain_encrypted',
                'token_plain_show_count',
                'token_plain_last_shown_at',
            ]);
        }

        // Current admin's tokens (each admin manages only their own)
        $apiTokens = PersonalAccessToken::query()
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', Auth::id())
            ->orderByDesc('id')
            ->get($select);

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
                'password'   => $s->smtp_password,    // not shown; present for completeness
                'encryption' => $s->smtp_encryption,  // '', 'tls', 'ssl'
                'from_name'  => $s->smtp_from_name,
                'from_addr'  => $s->smtp_from_addr,
            ],

            // Features - read from system_settings via Feature helper
            'features' => [
                'impersonation'                       => Feature::enabled('impersonation'),
                'allow_username_change'               => Feature::enabled('allow_username_change'),
                'require_admin_mfa_for_impersonation' => Feature::enabled('require_admin_mfa_for_impersonation'),
                'subscription_module_enabled'         => Feature::enabled('subscription_module_enabled'),
                'stripe_payment_enabled'              => Feature::enabled('stripe_payment_enabled'),
                'stripe_key'                          => SystemSetting::where('key', 'stripe_key')->first()?->value,
                'stripe_secret'                       => SystemSetting::where('key', 'stripe_secret')->first()?->value,
                'stripe_webhook_secret'               => SystemSetting::where('key', 'stripe_webhook_secret')->first()?->value,
                'announcement_enabled'                => Feature::enabled('announcement_enabled'),
            ],

            // Support Settings - read from system_settings via Feature helper
            'support' => [
                'enabled'            => Feature::enabled('support_enabled'),
                'auto_reply_enabled' => Feature::enabled('support_auto_reply_enabled'),
                'auto_reply_text'    => SystemSetting::where('key', 'support_auto_reply_text')->first()?->value ?? "Thank you for contacting us. We have received your ticket and will get back to you shortly.",
            ],

            // Social Authentication Settings
            'social' => [
                'enabled'                => Feature::enabled('social_login_enabled'),
                'google_enabled'         => Feature::enabled('google_login_enabled'),
                'google_client_id'       => SystemSetting::where('key', 'google_client_id')->first()?->value,
                'google_client_secret'   => SystemSetting::where('key', 'google_client_secret')->first()?->value,
                'facebook_enabled'       => Feature::enabled('facebook_login_enabled'),
                'facebook_client_id'     => SystemSetting::where('key', 'facebook_client_id')->first()?->value,
                'facebook_client_secret' => SystemSetting::where('key', 'facebook_client_secret')->first()?->value,
                'twitter_enabled'        => Feature::enabled('twitter_login_enabled'),
                'twitter_client_id'      => SystemSetting::where('key', 'twitter_client_id')->first()?->value,
                'twitter_client_secret'  => SystemSetting::where('key', 'twitter_client_secret')->first()?->value,
            ],

            // API keys (Sanctum)
            'api_tokens' => $apiTokens,

            // Back-compat for older Blade that expects $tokens
            'tokens' => $apiTokens,
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
     * Persists feature flags to the system_settings table.
     */
    public function updateFeatures(UpdateFeatureFlagsRequest $request)
    {
        // Define all feature flags with their keys
        // Use $request->boolean() for reliable boolean casting from checkboxes
        $features = [
            'social_login_enabled'                => $request->boolean('social_login_enabled'),
            'subscription_module_enabled'         => $request->boolean('subscription_module_enabled'),
            'stripe_payment_enabled'              => $request->boolean('stripe_payment_enabled'),
            'impersonation'                       => $request->boolean('impersonation'),
            'allow_username_change'               => $request->boolean('allow_username_change'),
            'require_admin_mfa_for_impersonation' => $request->boolean('require_admin_mfa_for_impersonation', true),
            'support_enabled'                     => $request->boolean('support_enabled'),
            'support_auto_reply_enabled'          => $request->boolean('support_auto_reply_enabled'),
            'announcement_enabled'                => $request->boolean('announcement_enabled'),
        ];

        // Persist each feature flag to system_settings table
        foreach ($features as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Save Stripe keys if present (nullable strings)
        $v = $request->validated();
        if (array_key_exists('stripe_key', $v)) {
            SystemSetting::updateOrCreate(['key' => 'stripe_key'], ['value' => $v['stripe_key']]);
        }
        if (array_key_exists('stripe_secret', $v)) {
            SystemSetting::updateOrCreate(['key' => 'stripe_secret'], ['value' => $v['stripe_secret']]);
        }
        if (array_key_exists('stripe_webhook_secret', $v)) {
            SystemSetting::updateOrCreate(['key' => 'stripe_webhook_secret'], ['value' => $v['stripe_webhook_secret']]);
        }

        // Clear the Feature helper cache to ensure settings take effect immediately
        Feature::clearCache();

        $this->logAudit('settings.update', 'Updated feature flags and modules', $v);

        return redirect()->route('admin.settings.index')->with('status', 'settings-features-updated');
    }

    /* =======================
     |  API keys (Sanctum)
     | =======================*/

    /**
     * POST /admin/settings/api-tokens  (admin.settings.api.create)
     * Create a personal access token for the current admin.
     * Accepts either "token_name" or legacy "name".
     */
    public function createApiToken(Request $request)
    {
        Gate::authorize('access-admin'); // defense in depth

        // Accept both token_name and legacy name
        if (!$request->filled('token_name') && $request->filled('name')) {
            $request->merge(['token_name' => $request->input('name')]);
        }

        $data = $request->validate([
            'token_name' => ['required', 'string', 'max:100'],
            'abilities'  => ['nullable', 'string'], // CSV or space e.g. "*", "read,write", "read write"
            'expires'    => ['nullable', 'string'], // "7 days" or "2026-01-01 23:59"
        ]);

        // Parse abilities: allow comma or whitespace separated; default "*"
        $abilities = ['*'];
        if (isset($data['abilities']) && trim($data['abilities']) !== '') {
            $abilities = array_values(array_filter(preg_split('/[\s,]+/', trim($data['abilities'])) ?: []));
            if (empty($abilities)) {
                $abilities = ['*'];
            }
        }

        $expiresAt = null;
        if (!empty($data['expires'])) {
            try {
                $expiresAt = Carbon::parse($data['expires']);
            } catch (\Throwable $e) {
                $expiresAt = null; // fall back to non-expiring if parse fails
            }
        }

        /** @var \App\Models\User $user */
        $user   = Auth::user();
        $issued = $user->createToken($data['token_name'], $abilities, $expiresAt);

        $plain = $issued->plainTextToken;     //  "{id}|{random}"
        $model = $issued->accessToken;        //  PersonalAccessToken Eloquent model

        // Store encrypted plaintext for always-on reveal (requires migration columns)
        try {
            $model->forceFill([
                'token_plain_encrypted'     => Crypt::encryptString($plain),
                'token_plain_show_count'    => (int) ($model->token_plain_show_count ?? 0),
                'token_plain_last_shown_at' => null,
            ])->save();
        } catch (\Throwable $e) {
            // If columns don't exist yet, do not fail token creation
        }

        $this->logAudit('settings.api-token.create', 'Created API token', [
            'token_id'   => $model->id,
            'name'       => $data['token_name'],
            'abilities'  => $abilities,
            'expires_at' => $model->expires_at?->toDateTimeString(),
        ]);

        // Flash both modern and legacy keys so any Blade variant works
        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'settings-api-token-created')
            ->with('new_token_plain', $plain)
            ->with('new_token', $plain)
            ->with('new_token_id', $model->id);
    }

    /**
     * POST /admin/settings/api-tokens/{token}/reveal  (admin.settings.api.reveal)
     * Reveal the plaintext token (we decrypt what we stored at creation).
     * Returns JSON when requested, otherwise flashes to session.
     */
    public function revealApiToken(Request $request, PersonalAccessToken $token)
    {
        Gate::authorize('access-admin');

        // Ensure it belongs to the current admin
        if ($token->tokenable_type !== User::class || (int) $token->tokenable_id !== (int) Auth::id()) {
            abort(403, 'You can only reveal your own tokens.');
        }

        // Decrypt stored value
        $plain = null;
        try {
            $enc = $token->token_plain_encrypted ?? null;
            if ($enc) {
                $plain = Crypt::decryptString($enc);
            }
        } catch (\Throwable $e) {
            $plain = null;
        }

        if (!$plain) {
            // Either older token (created before column existed) or missing storage
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Plain token is not stored for this key.',
                ], 404);
            }

            return back()->with('error', 'Plain token is not stored for this key.');
        }

        // Audit + bump counters (best effort)
        try {
            $token->forceFill([
                'token_plain_show_count'     => (int) ($token->token_plain_show_count ?? 0) + 1,
                'token_plain_last_shown_at'  => now(),
            ])->save();
        } catch (\Throwable $e) {
            // ignore
        }

        $this->logAudit('settings.api-token.reveal', 'Revealed API token plaintext', [
            'token_id' => $token->id,
            'name'     => $token->name,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'    => true,
                'token' => $plain,
            ]);
        }

        // Flash for the page to show in a banner/modal
        return back()
            ->with('status', 'settings-api-token-revealed')
            ->with('reveal_token_plain', $plain)
            ->with('reveal_token_id', $token->id);
    }

    /**
     * DELETE /admin/settings/api-tokens/{token} (admin.settings.api.revoke)
     * Revoke a personal access token that belongs to the current admin.
     */
    public function revokeApiToken(PersonalAccessToken $token, Request $request)
    {
        Gate::authorize('access-admin'); // defense in depth

        // Ensure it belongs to this admin
        if ($token->tokenable_type !== User::class || (int) $token->tokenable_id !== (int) Auth::id()) {
            abort(403, 'You can only revoke your own tokens.');
        }

        $id   = $token->id;
        $name = $token->name;

        $token->delete();

        $this->logAudit('settings.api-token.revoke', 'Revoked API token', [
            'token_id' => $id,
            'name'     => $name,
        ]);

        // Flash both modern and legacy keys so older Blade shows a nice message
        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'settings-api-token-revoked');
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
    /**
     * POST /admin/settings/support
     * Persists support ticket system settings to system_settings table.
     */
    public function updateSupport(Request $request)
    {
        $v = $request->validate([
            'support_enabled'             => 'nullable',
            'support_auto_reply_enabled'  => 'nullable',
            'support_auto_reply_text'     => 'nullable|string',
        ]);

        // Use boolean() for checkbox handling, persist to system_settings
        SystemSetting::updateOrCreate(
            ['key' => 'support_enabled'],
            ['value' => $request->boolean('support_enabled')]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'support_auto_reply_enabled'],
            ['value' => $request->boolean('support_auto_reply_enabled')]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'support_auto_reply_text'],
            ['value' => $request->input('support_auto_reply_text', '')]
        );

        // Clear the Feature helper cache
        Feature::clearCache();

        $this->logAudit('settings.update', 'Updated support settings', $v);

        return redirect()->route('admin.settings.index')->with('status', 'settings-support-updated');
    }

    /**
     * POST /admin/settings/social
     * Persists social authentication settings to system_settings table.
     */
    public function updateSocial(Request $request)
    {
        $v = $request->validate([
            'social_login_enabled'    => 'nullable',
            'google_login_enabled'    => 'nullable',
            'google_client_id'        => 'nullable|string|max:255',
            'google_client_secret'    => 'nullable|string|max:255',
            'facebook_login_enabled'  => 'nullable',
            'facebook_client_id'      => 'nullable|string|max:255',
            'facebook_client_secret'  => 'nullable|string|max:255',
            'twitter_login_enabled'   => 'nullable',
            'twitter_client_id'       => 'nullable|string|max:255',
            'twitter_client_secret'   => 'nullable|string|max:255',
        ]);

        // Boolean toggles
        SystemSetting::updateOrCreate(
            ['key' => 'social_login_enabled'],
            ['value' => $request->boolean('social_login_enabled')]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'google_login_enabled'],
            ['value' => $request->boolean('google_login_enabled')]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'facebook_login_enabled'],
            ['value' => $request->boolean('facebook_login_enabled')]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'twitter_login_enabled'],
            ['value' => $request->boolean('twitter_login_enabled')]
        );

        // String credentials (only update if provided, preserve existing if empty)
        if ($request->filled('google_client_id')) {
            SystemSetting::updateOrCreate(['key' => 'google_client_id'], ['value' => $v['google_client_id']]);
        }
        if ($request->filled('google_client_secret')) {
            SystemSetting::updateOrCreate(['key' => 'google_client_secret'], ['value' => $v['google_client_secret']]);
        }
        if ($request->filled('facebook_client_id')) {
            SystemSetting::updateOrCreate(['key' => 'facebook_client_id'], ['value' => $v['facebook_client_id']]);
        }
        if ($request->filled('facebook_client_secret')) {
            SystemSetting::updateOrCreate(['key' => 'facebook_client_secret'], ['value' => $v['facebook_client_secret']]);
        }
        if ($request->filled('twitter_client_id')) {
            SystemSetting::updateOrCreate(['key' => 'twitter_client_id'], ['value' => $v['twitter_client_id']]);
        }
        if ($request->filled('twitter_client_secret')) {
            SystemSetting::updateOrCreate(['key' => 'twitter_client_secret'], ['value' => $v['twitter_client_secret']]);
        }

        // Clear the Feature helper cache
        Feature::clearCache();

        $this->logAudit('settings.update', 'Updated social authentication settings', [
            'social_login_enabled' => $request->boolean('social_login_enabled'),
            'google_enabled' => $request->boolean('google_login_enabled'),
            'facebook_enabled' => $request->boolean('facebook_login_enabled'),
            'twitter_enabled' => $request->boolean('twitter_login_enabled'),
        ]);

        return redirect()->route('admin.settings.index')->with('status', 'settings-social-updated');
    }
}
