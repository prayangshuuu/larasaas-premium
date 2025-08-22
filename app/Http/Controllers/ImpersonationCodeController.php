<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImpersonationCodeController extends Controller
{
    public function show(Request $request)
    {
        if (!config('features.impersonation')) {
            abort(404);
        }
        return view('profile.partials.impersonation-code');
    }

    public function generate(Request $request)
    {
        if (!config('features.impersonation')) {
            abort(404);
        }

        $user = $request->user();

        // 8-digit numeric code (or swap for random string)
        $code = (string) random_int(10000000, 99999999);
        $ttl  = (int) config('features.impersonation_code_ttl', 30);

        $user->forceFill([
            'impersonation_code' => $code,
            'impersonation_code_expires_at' => now()->addMinutes($ttl),
        ])->save();

        // Optional: write audit
        \App\Models\AuditLog::write($user, 'impersonation.code.regenerate', 'User generated support access code');

        return back()->with([
            'status' => __('Support access code generated.'),
            'status_type' => 'success',
        ]);
    }

    public function revoke(Request $request)
    {
        if (!config('features.impersonation')) {
            abort(404);
        }

        $user = $request->user();
        $user->forceFill([
            'impersonation_code' => null,
            'impersonation_code_expires_at' => null,
        ])->save();

        \App\Models\AuditLog::write($user, 'impersonation.code.revoke', 'User revoked support access code');

        return back()->with([
            'status' => __('Support access code revoked.'),
            'status_type' => 'success',
        ]);
    }
}
