<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Column-mode columns expected by App\Models\Setting::MAP
            if (! Schema::hasColumn('settings', 'feature_impersonation')) {
                $table->boolean('feature_impersonation')->default(false);
            }
            if (! Schema::hasColumn('settings', 'feature_usernames_editable')) {
                $table->boolean('feature_usernames_editable')->default(true);
            }
            if (! Schema::hasColumn('settings', 'security_require_admin_mfa_for_impersonation')) {
                $table->boolean('security_require_admin_mfa_for_impersonation')->default(true);
            }
        });

        // Ensure there is at least one row in column-mode tables so updates have a target
        if (! DB::table('settings')->exists()) {
            DB::table('settings')->insert([
                'feature_impersonation'                          => false,
                'feature_usernames_editable'                     => true,
                'security_require_admin_mfa_for_impersonation'   => true,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'feature_impersonation')) {
                $table->dropColumn('feature_impersonation');
            }
            if (Schema::hasColumn('settings', 'feature_usernames_editable')) {
                $table->dropColumn('feature_usernames_editable');
            }
            if (Schema::hasColumn('settings', 'security_require_admin_mfa_for_impersonation')) {
                $table->dropColumn('security_require_admin_mfa_for_impersonation');
            }
        });
    }
};
