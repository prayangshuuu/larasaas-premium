<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     * If feature flag is off, 404 to keep surface minimal.
     */
    public function start(Request $request, User $user)
    {
        if (!config('features.impersonation')) {
            abort(404);
        }

        // Only admins with MFA can start; your route also has 'admin' + 'admin-mfa'
        $admin = $request->user();

        // Determine mode:
        // - full: code matches & not expired, AND feature says code is required for full
        // - otherwise: readonly
        $mode = 'readonly';
        $codeRequired = (bool) config('features.impersonation_code_required_for_full');
        $code = trim((string) $request->input('code', ''));

        if ($codeRequired) {
            if ($user->impersonation_code && $user->impersonation_code_expires_at &&
                now()->lte($user->impersonation_code_expires_at) &&
                hash_equals($user->impersonation_code, $code)
            ) {
                $mode = 'full';
            }
        } else {
            // If you ever turn off the requirement, allow full by default
            $mode = 'full';
        }

        // Stash the admin id; then login as target user
        session()->put('impersonated_by', $admin->id);
        session()->put('impersonation_mode', $mode);

        Auth::login($user); // web guard

        AuditLog::write($user, 'impersonation.start', 'Admin started impersonation', [
            'by' => $admin->id,
            'mode' => $mode,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('dashboard')->with([
            'status' => $mode === 'full' ? __('Impersonation started (full access).') : __('Impersonation started (read-only).'),
            'status_type' => 'warning',
        ]);
    }

    /**
     * Stop impersonation: return to the admin account.
     */
    public function stop(Request $request)
    {
        if (!config('features.impersonation')) {
            abort(404);
        }

        $adminId = session('impersonated_by');
        if (!$adminId) {
            return redirect()->route('dashboard');
        }

        $impersonatedUser = Auth::user();
        AuditLog::write($impersonatedUser, 'impersonation.stop', 'Admin stopped impersonation', [
            'by' => $adminId,
            'ip' => $request->ip(),
        ]);

        session()->forget(['impersonated_by', 'impersonation_mode']);
        Auth::loginUsingId($adminId);

        return redirect()->route('admin.dashboard')->with([
            'status' => __('Impersonation ended.'),
            'status_type' => 'success',
        ]);
    }
}
