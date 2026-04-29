<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users',
            'plans',
            'subscriptions',
            'invoices',
            'coupons',
            'audit_logs',
            'personal_access_tokens',
            'impersonation_codes',
        ];

        /**
         * 1. SHIFT PRIMARY KEYS
         */
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'id')) {
                DB::statement("
                    UPDATE {$table}
                    SET id = id + 100000
                    WHERE id < 100000
                ");
            }
        }

        /**
         * 2. UPDATE FOREIGN KEYS
         */
        $this->updateForeignKey('subscriptions', 'user_id');
        $this->updateForeignKey('subscriptions', 'plan_id');

        $this->updateForeignKey('invoices', 'user_id');
        if (Schema::hasColumn('invoices', 'subscription_id')) {
            $this->updateForeignKey('invoices', 'subscription_id');
        }

        $this->updateForeignKey('impersonation_codes', 'user_id');

        $this->updateForeignKey('audit_logs', 'user_id');
        $this->updateForeignKey('audit_logs', 'actor_id');

        /**
         * 3. POLYMORPHIC FIXES
         */

        if (Schema::hasTable('personal_access_tokens')) {
            DB::statement("
                UPDATE personal_access_tokens
                SET tokenable_id = tokenable_id + 100000
                WHERE tokenable_id < 100000
                AND tokenable_type = 'App\\\\Models\\\\User'
            ");
        }

        if (Schema::hasTable('audit_logs')) {
            $types = [
                'App\\\\Models\\\\User',
                'App\\\\Models\\\\Plan',
                'App\\\\Models\\\\Subscription',
                'App\\\\Models\\\\Invoice',
                'App\\\\Models\\\\Coupon',
            ];

            foreach ($types as $type) {
                DB::statement("
                    UPDATE audit_logs
                    SET target_id = target_id + 100000
                    WHERE target_id < 100000
                    AND target_type = '{$type}'
                ");
            }
        }

        /**
         * 4. RESET SEQUENCES (CRITICAL)
         */
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("
                    SELECT setval(
                        pg_get_serial_sequence('{$table}', 'id'),
                        (SELECT COALESCE(MAX(id), 1) FROM {$table})
                    )
                ");
            }
        }
    }

    /**
     * Update FK helper
     */
    protected function updateForeignKey($table, $column)
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            DB::statement("
                UPDATE {$table}
                SET {$column} = {$column} + 100000
                WHERE {$column} < 100000
            ");
        }
    }

    public function down(): void
    {
        $tables = [
            'users',
            'plans',
            'subscriptions',
            'invoices',
            'coupons',
            'audit_logs',
            'personal_access_tokens',
            'impersonation_codes',
        ];

        /**
         * REVERT PRIMARY KEYS
         */
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'id')) {
                DB::statement("
                    UPDATE {$table}
                    SET id = id - 100000
                    WHERE id >= 100000 AND id < 200000
                ");
            }
        }

        /**
         * REVERT FOREIGN KEYS
         */
        $this->revertForeignKey('subscriptions', 'user_id');
        $this->revertForeignKey('subscriptions', 'plan_id');

        $this->revertForeignKey('invoices', 'user_id');
        if (Schema::hasColumn('invoices', 'subscription_id')) {
            $this->revertForeignKey('invoices', 'subscription_id');
        }

        $this->revertForeignKey('impersonation_codes', 'user_id');

        $this->revertForeignKey('audit_logs', 'user_id');
        $this->revertForeignKey('audit_logs', 'actor_id');

        /**
         * REVERT POLYMORPHIC
         */

        if (Schema::hasTable('personal_access_tokens')) {
            DB::statement("
                UPDATE personal_access_tokens
                SET tokenable_id = tokenable_id - 100000
                WHERE tokenable_id >= 100000 AND tokenable_id < 200000
                AND tokenable_type = 'App\\\\Models\\\\User'
            ");
        }

        if (Schema::hasTable('audit_logs')) {
            $types = [
                'App\\\\Models\\\\User',
                'App\\\\Models\\\\Plan',
                'App\\\\Models\\\\Subscription',
                'App\\\\Models\\\\Invoice',
                'App\\\\Models\\\\Coupon',
            ];

            foreach ($types as $type) {
                DB::statement("
                    UPDATE audit_logs
                    SET target_id = target_id - 100000
                    WHERE target_id >= 100000 AND target_id < 200000
                    AND target_type = '{$type}'
                ");
            }
        }

        /**
         * RESET SEQUENCES AGAIN
         */
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("
                    SELECT setval(
                        pg_get_serial_sequence('{$table}', 'id'),
                        (SELECT COALESCE(MAX(id), 1) FROM {$table})
                    )
                ");
            }
        }
    }

    protected function revertForeignKey($table, $column)
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            DB::statement("
                UPDATE {$table}
                SET {$column} = {$column} - 100000
                WHERE {$column} >= 100000 AND {$column} < 200000
            ");
        }
    }
};
