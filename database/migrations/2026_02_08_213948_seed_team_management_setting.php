<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert 'team_management_enabled' = '1' (true) if it doesn't exist
        \Illuminate\Support\Facades\DB::table('system_settings')->insertOrIgnore([
            'key' => 'team_management_enabled', 
            'value' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Clear cache to ensure it's picked up immediately
        \Illuminate\Support\Facades\Cache::forget('system_settings_global');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Remove the setting on rollback, or leave it as it's user configuration
        // \Illuminate\Support\Facades\DB::table('system_settings')->where('key', 'team_management_enabled')->delete();
    }
};
