<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TwoFactorRecoveryCodesController extends Controller
{
    /**
     * Show decrypted recovery codes (route applies: auth, verified, not-banned, password.confirm).
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $this->twoFactorEnabled($user)) {
            return redirect()->route('profile.edit')
                ->with('error', __('Two-factor authentication is not enabled.'));
        }

        $codes = $this->recoveryCodes($user);

        return view('profile.two-factor-recovery-codes', compact('codes'));
    }

    /**
     * Regenerate recovery codes (protect with password.confirm on the route).
     */
    public function regenerate(Request $request)
    {
        $user = $request->user();

        if (! $this->twoFactorEnabled($user)) {
            return redirect()->route('profile.edit')
                ->with('error', __('Two-factor authentication is not enabled.'));
        }

        $codes = $this->generateCodes($user);

        // Fortify stores encrypted JSON in two_factor_recovery_codes
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ])->save();

        $this->logAudit('2fa.recovery.regenerate', [
            'user_id' => $user->id,
            'count'   => count($codes),
        ]);

        return back()->with('status', 'two-factor-recovery-codes-regenerated');
    }

    /**
     * Download recovery codes as a plaintext file (protect with password.confirm on the route).
     */
    public function download(Request $request): Response
    {
        $user = $request->user();

        if (! $this->twoFactorEnabled($user)) {
            return redirect()->route('profile.edit')
                ->with('error', __('Two-factor authentication is not enabled.'));
        }

        $codes    = $this->recoveryCodes($user);
        $filename = 'recovery-codes-' . now()->format('Ymd-His') . '.txt';
        $contents = implode(PHP_EOL, $codes) . PHP_EOL;

        $this->logAudit('2fa.recovery.download', [
            'user_id' => $user->id,
            'count'   => count($codes),
        ]);

        return response($contents, 200, [
            'Content-Type'            => 'text/plain; charset=UTF-8',
            'Content-Disposition'     => "attachment; filename=\"{$filename}\"",
            'X-Content-Type-Options'  => 'nosniff',
        ]);
    }

    /* ------------------------------- Helpers ------------------------------- */

    /**
     * Is 2FA enabled for the user?
     */
    private function twoFactorEnabled($user): bool
    {
        return (bool) ($user->two_factor_secret ?? false);
    }

    /**
     * Decrypt and return recovery codes as an array.
     * Uses Fortify's recoveryCodes() if available; safe fallback otherwise.
     */
    private function recoveryCodes($user): array
    {
        if (method_exists($user, 'recoveryCodes')) {
            // From Laravel\Fortify\TwoFactorAuthenticatable
            return (array) $user->recoveryCodes();
        }

        if (! $user->two_factor_recovery_codes) {
            return [];
        }

        try {
            $decoded = json_decode(decrypt($user->two_factor_recovery_codes), true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Generate a fresh set of recovery codes.
     * Uses Fortify's generateRecoveryCodes() if available; fallback creates 8 codes.
     */
    private function generateCodes($user): array
    {
        if (method_exists($user, 'generateRecoveryCodes')) {
            // From Laravel\Fortify\TwoFactorAuthenticatable
            return (array) $user->generateRecoveryCodes();
        }

        // Fallback: 8 codes, 20 chars with a dash in the middle (human-friendly)
        return collect(range(1, 8))
            ->map(fn () => Str::upper(Str::random(10)) . '-' . Str::upper(Str::random(10)))
            ->all();
    }

    /**
     * Best-effort audit logging (works if your AuditLog model exists).
     */
    private function logAudit(string $action, array $metadata = []): void
    {
        $actorId   = Auth::id();
        $ip        = request()->ip();
        $userAgent = request()->userAgent();

        if (class_exists(AuditLog::class) && method_exists(AuditLog::class, 'log')) {
            AuditLog::log($action, $actorId, null, $action, $metadata + ['ip' => $ip, 'ua' => $userAgent]);
            return;
        }

        try {
            if (class_exists(AuditLog::class)) {
                AuditLog::query()->create([
                    'actor_id'    => $actorId,
                    'target_type' => null,
                    'target_id'   => null,
                    'action'      => $action,
                    'description' => $action,
                    'ip_address'  => $ip,
                    'user_agent'  => $userAgent,
                    'metadata'    => $metadata ?: null,
                ]);
            }
        } catch (\Throwable $e) {
            // Never block UX on logging failure
        }
    }
}
