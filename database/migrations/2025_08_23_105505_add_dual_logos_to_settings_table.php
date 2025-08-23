<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private function isKv(): bool
    {
        return Schema::hasColumn('settings', 'key') && Schema::hasColumn('settings', 'value');
    }

    private function hasTimestamps(): bool
    {
        return Schema::hasColumn('settings', 'created_at') && Schema::hasColumn('settings', 'updated_at');
    }

    public function up(): void
    {
        if ($this->isKv()) {
            // KV schema: no columns to add; ensure keys exist / migrate legacy value.
            // Try to take legacy value from KV key 'app.logo_path' if present.
            $legacy = DB::table('settings')->where('key', 'app.logo_path')->value('value');

            $hasLight = DB::table('settings')->where('key', 'app.logo_light_path')->exists();
            $hasDark  = DB::table('settings')->where('key', 'app.logo_dark_path')->exists();

            $payloadBase = [];
            if ($this->hasTimestamps()) {
                $payloadBase['created_at'] = now();
                $payloadBase['updated_at'] = now();
            }

            if (! $hasLight) {
                $payload = array_merge($payloadBase, [
                    'key'   => 'app.logo_light_path',
                    'value' => $legacy ?? 'null', // store string "null" to match Setting::encodeValue() convention
                ]);
                DB::table('settings')->insert($payload);
            }

            if (! $hasDark) {
                $payload = array_merge($payloadBase, [
                    'key'   => 'app.logo_dark_path',
                    'value' => 'null',
                ]);
                DB::table('settings')->insert($payload);
            }

            // Done for KV schema.
            return;
        }

        // Column schema: add columns if missing and backfill from legacy column if present.
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'app_logo_light_path')) {
                // put after legacy single-logo column if it exists
                if (Schema::hasColumn('settings', 'app_logo_path')) {
                    $table->string('app_logo_light_path')->nullable()->after('app_logo_path');
                } else {
                    $table->string('app_logo_light_path')->nullable();
                }
            }
            if (! Schema::hasColumn('settings', 'app_logo_dark_path')) {
                $after = Schema::hasColumn('settings', 'app_logo_light_path') ? 'app_logo_light_path' : null;
                $col = $table->string('app_logo_dark_path')->nullable();
                if ($after) $col->after($after);
            }
        });

        // Backfill row 1 from legacy column if available.
        try {
            $row = DB::table('settings')
                ->select([
                    DB::raw('COALESCE(app_logo_light_path, "") as app_logo_light_path'),
                    DB::raw('COALESCE(app_logo_dark_path, "")  as app_logo_dark_path'),
                    DB::raw('COALESCE(app_logo_path, "")       as app_logo_path'),
                ])
                ->first();

            if ($row) {
                $updates = [];
                if ($row->app_logo_light_path === '' && $row->app_logo_path !== '') {
                    $updates['app_logo_light_path'] = $row->app_logo_path;
                }
                // Leave dark as null by default unless you want to clone the legacy too.
                if (!empty($updates)) {
                    DB::table('settings')->update($updates);
                }
            }
        } catch (\Throwable $e) {
            // Non-fatal: skip backfill if table doesn’t have those columns.
        }
    }

    public function down(): void
    {
        if ($this->isKv()) {
            // KV schema: remove the keys we created (if present).
            DB::table('settings')->whereIn('key', [
                'app.logo_light_path',
                'app.logo_dark_path',
            ])->delete();
            return;
        }

        // Column schema: drop the columns if they exist.
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'app_logo_light_path')) {
                $table->dropColumn('app_logo_light_path');
            }
            if (Schema::hasColumn('settings', 'app_logo_dark_path')) {
                $table->dropColumn('app_logo_dark_path');
            }
        });
    }
};
