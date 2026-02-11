<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add the missing app_name, app_logo_path, and SMTP columns
     * to the settings table so Setting::put() can persist them.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'app_name')) {
                $table->string('app_name')->default('IELTSBandBooster')->after('id');
            }
            if (! Schema::hasColumn('settings', 'app_logo_path')) {
                $table->string('app_logo_path')->nullable()->after('app_name');
            }
            if (! Schema::hasColumn('settings', 'smtp_host')) {
                $table->string('smtp_host')->nullable()->after('app_logo_dark_path');
            }
            if (! Schema::hasColumn('settings', 'smtp_port')) {
                $table->unsignedInteger('smtp_port')->nullable()->after('smtp_host');
            }
            if (! Schema::hasColumn('settings', 'smtp_username')) {
                $table->string('smtp_username')->nullable()->after('smtp_port');
            }
            if (! Schema::hasColumn('settings', 'smtp_password')) {
                $table->string('smtp_password')->nullable()->after('smtp_username');
            }
            if (! Schema::hasColumn('settings', 'smtp_encryption')) {
                $table->string('smtp_encryption')->nullable()->after('smtp_password');
            }
            if (! Schema::hasColumn('settings', 'smtp_from_address')) {
                $table->string('smtp_from_address')->nullable()->after('smtp_encryption');
            }
            if (! Schema::hasColumn('settings', 'smtp_from_name')) {
                $table->string('smtp_from_name')->nullable()->after('smtp_from_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = [
                'app_name', 'app_logo_path',
                'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
                'smtp_encryption', 'smtp_from_address', 'smtp_from_name',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('settings', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
