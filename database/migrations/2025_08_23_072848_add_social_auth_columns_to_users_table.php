<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add columns used for Socialite logins (Google, etc.).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Generic provider info (so you can support other providers later)
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider', 50)->nullable()->after('remember_token')->index();
            }

            if (!Schema::hasColumn('users', 'provider_id')) {
                // Nullable UNIQUE: allows many NULLs, but any non-null must be unique
                $table->string('provider_id', 191)->nullable()->unique()->after('provider');
            }

            // Optional extras (handy for UX)
            if (!Schema::hasColumn('users', 'provider_avatar')) {
                $table->string('provider_avatar', 512)->nullable()->after('provider_id');
            }

            // Token details (if you ever need to call provider APIs)
            if (!Schema::hasColumn('users', 'provider_token')) {
                $table->text('provider_token')->nullable()->after('provider_avatar');
            }

            if (!Schema::hasColumn('users', 'provider_refresh_token')) {
                $table->text('provider_refresh_token')->nullable()->after('provider_token');
            }

            if (!Schema::hasColumn('users', 'provider_token_expires_at')) {
                $table->timestamp('provider_token_expires_at')->nullable()->after('provider_refresh_token');
            }
        });
    }

    /**
     * Roll back the social auth additions.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop in reverse order (guards each drop so it won't error if missing)
            if (Schema::hasColumn('users', 'provider_token_expires_at')) {
                $table->dropColumn('provider_token_expires_at');
            }
            if (Schema::hasColumn('users', 'provider_refresh_token')) {
                $table->dropColumn('provider_refresh_token');
            }
            if (Schema::hasColumn('users', 'provider_token')) {
                $table->dropColumn('provider_token');
            }
            if (Schema::hasColumn('users', 'provider_avatar')) {
                $table->dropColumn('provider_avatar');
            }
            if (Schema::hasColumn('users', 'provider_id')) {
                $table->dropUnique('users_provider_id_unique'); // named by Laravel for unique('provider_id')
                $table->dropColumn('provider_id');
            }
            if (Schema::hasColumn('users', 'provider')) {
                $table->dropIndex(['provider']); // drops index on provider
                $table->dropColumn('provider');
            }
        });
    }
};
