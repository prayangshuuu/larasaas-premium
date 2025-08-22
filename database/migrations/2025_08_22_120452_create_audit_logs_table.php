<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a nullable banned_at timestamp to users.
     * When set, your auth middleware can block access.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'banned_at')) {
            Schema::table('users', function (Blueprint $table) {
                // Place it near other auth-related columns
                $table->timestamp('banned_at')
                    ->nullable()
                    ->after('email_verified_at')
                    ->index(); // quick lookups: "is this user banned?"
            });
        }
    }

    /**
     * Remove the banned_at column.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'banned_at')) {
            Schema::table('users', function (Blueprint $table) {
                // On MySQL, dropping the column will drop its index too.
                $table->dropColumn('banned_at');
            });
        }
    }
};
