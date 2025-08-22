<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If the settings table already exists (created by an earlier migration),
        // do nothing to avoid "Base table already exists" errors.
        if (Schema::hasTable('settings')) {
            return;
        }

        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // App settings
            $table->string('app_name')->default('IELTSBandBooster');
            $table->string('app_logo_path')->nullable();

            // SMTP settings
            $table->string('smtp_host')->nullable();
            $table->unsignedInteger('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();    // store encrypted if you like
            $table->string('smtp_encryption')->nullable();  // null|ssl|tls
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();

            // Feature flags
            $table->boolean('feature_impersonation')->default(false);
            $table->boolean('feature_usernames_editable')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Intentionally left blank.
        // This migration may be a no-op if the table already existed; dropping it here
        // could remove a table created by the earlier migration (2025_08_22_190941_*).
    }
};
