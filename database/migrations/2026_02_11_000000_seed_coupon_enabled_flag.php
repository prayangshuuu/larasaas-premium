<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed the coupon_enabled feature flag so the coupon routes are accessible.
     */
    public function up(): void
    {
        DB::table('system_settings')->insertOrIgnore([
            'key'        => 'coupon_enabled',
            'value'      => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->where('key', 'coupon_enabled')->delete();
    }
};
