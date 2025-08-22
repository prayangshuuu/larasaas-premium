<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'impersonation_code')) {
                $table->string('impersonation_code', 32)->nullable()->after('banned_at');
            }
            if (!Schema::hasColumn('users', 'impersonation_code_expires_at')) {
                $table->timestamp('impersonation_code_expires_at')->nullable()->after('impersonation_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'impersonation_code_expires_at')) {
                $table->dropColumn('impersonation_code_expires_at');
            }
            if (Schema::hasColumn('users', 'impersonation_code')) {
                $table->dropColumn('impersonation_code');
            }
        });
    }
};
