<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable FK checks to allow modifying IDs
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Tables to process (must have an auto-incrementing 'id' column)
        $tables = [
            'users',
            'plans',
            'subscriptions',
            'invoices',
            'coupons',
            'audit_logs',
            'system_settings',
            'personal_access_tokens',
            // Default Laravel tables that usually have IDs
            'jobs',
            'failed_jobs',
            // 'settings' if it exists and has an ID
        ];

        // Add 'settings' if it exists
        if (Schema::hasTable('settings')) {
            $tables[] = 'settings';
        }

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Set Auto Increment to 100000
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 100000;");
                
                // Shift existing IDs (only if they are small, e.g. < 100000)
                // We use a safe query that won't break if table is empty
                DB::statement("UPDATE `{$table}` SET id = id + 100000 WHERE id < 100000;");
            }
        }

        // 2. Update Foreign Keys (Explicit Relationships)
        
        // Users Relationships
        $this->updateForeignKey('subscriptions', 'user_id');
        $this->updateForeignKey('invoices', 'user_id');
        $this->updateForeignKey('audit_logs', 'actor_id'); // nullable
        
        // Polymorphic: personal_access_tokens (tokenable_id) for Users
        DB::statement("
            UPDATE personal_access_tokens 
            SET tokenable_id = tokenable_id + 100000 
            WHERE tokenable_id < 100000 
            AND tokenable_type = 'App\\\\Models\\\\User'
        ");

        // Polymorphic: audit_logs (target_id) for Users
        DB::statement("
            UPDATE audit_logs 
            SET target_id = target_id + 100000 
            WHERE target_id < 100000 
            AND target_type = 'App\\\\Models\\\\User'
        ");

        // Plans Relationships
        $this->updateForeignKey('subscriptions', 'plan_id');
        
        // Polymorphic: audit_logs (target_id) for Plans
        DB::statement("
            UPDATE audit_logs 
            SET target_id = target_id + 100000 
            WHERE target_id < 100000 
            AND target_type = 'App\\\\Models\\\\Plan'
        ");
        
        // Polymorphic: audit_logs (target_id) for Subscriptions
        DB::statement("
            UPDATE audit_logs 
            SET target_id = target_id + 100000 
            WHERE target_id < 100000 
            AND target_type = 'App\\\\Models\\\\Subscription'
        ");

        // Polymorphic: audit_logs (target_id) for Invoices
         DB::statement("
            UPDATE audit_logs 
            SET target_id = target_id + 100000 
            WHERE target_id < 100000 
            AND target_type = 'App\\\\Models\\\\Invoice'
        ");

        // Polymorphic: audit_logs (target_id) for Coupons
        DB::statement("
            UPDATE audit_logs 
            SET target_id = target_id + 100000 
            WHERE target_id < 100000 
            AND target_type = 'App\\\\Models\\\\Coupon'
        ");

        // Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Helper to update simple foreign keys.
     */
    protected function updateForeignKey($table, $column)
    {
        if (Schema::hasColumn($table, $column)) {
            DB::statement("UPDATE `{$table}` SET `{$column}` = `{$column}` + 100000 WHERE `{$column}` < 100000;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is complex because new data might have been created > 100000.
        // For safety, we will strictly reverse the ID shift for IDs that were likely shifted.
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'users', 'plans', 'subscriptions', 'invoices', 'coupons', 
            'audit_logs', 'system_settings', 'personal_access_tokens', 'jobs', 'failed_jobs'
        ];
        if (Schema::hasTable('settings')) $tables[] = 'settings';

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Shift back IDs that are in the 100xxx range (assuming original IDs were small)
                // This is a "best effort" rollback.
                DB::statement("UPDATE `{$table}` SET id = id - 100000 WHERE id >= 100000 AND id < 200000;");
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1;");
            }
        }

        // Revert FKs
        $this->revertForeignKey('subscriptions', 'user_id');
        $this->revertForeignKey('invoices', 'user_id');
        $this->revertForeignKey('audit_logs', 'actor_id');

        // Revert Polymorphic
        DB::statement("UPDATE personal_access_tokens SET tokenable_id = tokenable_id - 100000 WHERE tokenable_id >= 100000 AND tokenable_id < 200000 AND tokenable_type = 'App\\\\Models\\\\User'");
        
        // Revert Audit Log targets
        $types = ['App\\\\Models\\\\User', 'App\\\\Models\\\\Plan', 'App\\\\Models\\\\Subscription', 'App\\\\Models\\\\Invoice', 'App\\\\Models\\\\Coupon'];
        foreach ($types as $type) {
             DB::statement("UPDATE audit_logs SET target_id = target_id - 100000 WHERE target_id >= 100000 AND target_id < 200000 AND target_type = '{$type}'");
        }
        
        $this->revertForeignKey('subscriptions', 'plan_id');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function revertForeignKey($table, $column)
    {
        if (Schema::hasColumn($table, $column)) {
            DB::statement("UPDATE `{$table}` SET `{$column}` = `{$column}` - 100000 WHERE `{$column}` >= 100000 AND `{$column}` < 200000;");
        }
    }
};
