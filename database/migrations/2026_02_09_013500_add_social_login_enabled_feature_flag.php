<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the master social login feature flag using updateOrInsert for idempotency
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'social_login_enabled'],
            [
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Clear the feature cache to ensure the new setting is immediately available
        \App\Helpers\Feature::clearCache();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')
            ->where('key', 'social_login_enabled')
            ->delete();

        // Clear cache after removal
        \App\Helpers\Feature::clearCache();
    }
};
