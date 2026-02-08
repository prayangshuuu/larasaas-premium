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
        DB::table('system_settings')->insert([
            'key' => 'announcement_enabled',
            'value' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        \Illuminate\Support\Facades\Cache::forget('laravel-cache-system_settings_global');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->where('key', 'announcement_enabled')->delete();
        \Illuminate\Support\Facades\Cache::forget('laravel-cache-system_settings_global');
    }
};
