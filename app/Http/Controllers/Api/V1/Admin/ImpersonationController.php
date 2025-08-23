<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ImpersonationController extends Controller
{
    public function __construct()
    {
        // Start requires Sanctum auth + Admin + feature flag (MFA should be on the route if desired)
        $this->middleware(['auth:sanctum', 'admin', 'feature:features.impersonation'])->only('start');

        // Stop should always be possible so the admin can exit, even if the feature was later turned off.
        $this->middleware(['auth:sanctum'])->only('stop');
    }

    /**
     * Start impersonating a user.
     * Body JSON:
     *   { "code": "string", "mode": "readonly"|"full" }
     */
    public function start(Request $request, User $user)
    {
        // Feature flag double-check (defense in depth; the middleware also enforces this)
        if (! Setting::bool('features.impersonation', false)) {
            return response()->json(['message' => 'Impersonation is disabled.'], 404);
        }

        // Validate input
        $data = $request->validate([
            'code' => ['required', 'string', 'max:64'],
            'mode' => ['nullable', 'in:readonly,full'],
        ]);

        // Cannot impersonate yourself
        if ((int) $user->id === (int) Auth::id()) {
            return response()->json(['message' => 'You cannot impersonate yourself.'], 422);
        }

        // If an impersonation session already exists, block (explicit exit first)
        if ($request->session()->has('impersonated_by')) {
            return response()->json(['message' => 'Already impersonating. Stop current session first.'], 409);
        }

        // Optionally block banned targets
        if (method_exists($user, 'isBanned') && $user->isBanned()) {
            return response()->json(['message' => 'Cannot impersonate a banned user.'], 422);
        }

        // Check one-time consent code for this user
        if (! $this->checkCode($user->id, $data['code'])) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        // Mark the session as impersonating
        $request->session()->put('impersonated_by', Auth::id());
        $mode = ($data['mode'] ?? 'readonly') === 'full' ? 'full' : 'readonly';
        $request->session()->put('impersonation_mode', $mode);

        // Swap the authenticated user to the target user (SPA Sanctum uses session)
        Auth::login($user);

        $this->logAudit('impersonation.start', [
            'as_user_id' => $user->id,
            'mode'       => $mode,
        ]);

        return response()->json([
            'message'    => 'Impersonation started',
            'as_user_id' => $user->id,
            'mode'       => $mode,
        ]);
    }

    /**
     * Stop impersonating and restore the original admin.
     */
    public function stop(Request $request)
    {
        $originalAdminId = $request->session()->pull('impersonated_by');
        $request->session()->forget('impersonation_mode');

        if ($originalAdminId) {
            Auth::loginUsingId($originalAdminId);

            $this->logAudit('impersonation.stop', [
                'restored_admin_id' => $originalAdminId,
            ]);
        }

        return response()->json(['message' => 'Impersonation stopped']);
    }

    /**
     * Check a one-time consent code for the target user.
     * Uses cache key: "impersonation:code:user:{id}"
     */
    private function checkCode(int $userId, string $code): bool
    {
        $key = "impersonation:code:user:{$userId}";
        $cached = Cache::get($key);
        if ($cached && hash_equals((string) $cached, (string) $code)) {
            Cache::forget($key); // one-time use
            return true;
        }
        return false;
    }

    /**
     * Safe audit helper.
     */
    private function logAudit(string $action, array $meta = []): void
    {
        try {
            if (class_exists(AuditLog::class)) {
                if (method_exists(AuditLog::class, 'log')) {
                    AuditLog::log($action, Auth::id(), null, $action, $meta);
                    return;
                }

                AuditLog::query()->create([
                    'actor_id'    => Auth::id(),
                    'target_type' => null,
                    'target_id'   => null,
                    'action'      => $action,
                    'description' => $action,
                    'ip_address'  => request()->ip(),
                    'user_agent'  => request()->userAgent(),
                    'metadata'    => $meta ?: null,
                ]);
            }
        } catch (\Throwable $e) {
            // Never block API on audit failures
        }
    }
}
