<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add columns to store/reveal plaintext tokens (encrypted),
     * plus basic reveal telemetry.
     */
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Add only if not already present (idempotent)
            if (!Schema::hasColumn('personal_access_tokens', 'token_plain_encrypted')) {
                $table->text('token_plain_encrypted')->nullable();
            }

            if (!Schema::hasColumn('personal_access_tokens', 'token_plain_show_count')) {
                $table->unsignedInteger('token_plain_show_count')->default(0);
            }

            if (!Schema::hasColumn('personal_access_tokens', 'token_plain_last_shown_at')) {
                $table->timestamp('token_plain_last_shown_at')->nullable();
            }
        });
    }

    /**
     * Roll back the added columns (if they exist).
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('personal_access_tokens', 'token_plain_last_shown_at')) {
                $table->dropColumn('token_plain_last_shown_at');
            }
            if (Schema::hasColumn('personal_access_tokens', 'token_plain_show_count')) {
                $table->dropColumn('token_plain_show_count');
            }
            if (Schema::hasColumn('personal_access_tokens', 'token_plain_encrypted')) {
                $table->dropColumn('token_plain_encrypted');
            }
        });
    }
};
