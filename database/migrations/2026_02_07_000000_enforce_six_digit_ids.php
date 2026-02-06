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
        // 1. Preparation: Disable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Tables to update (Primary Keys)
        $tables = [
            'users',
            'plans',
            'subscriptions',
            'invoices',
            'coupons',
            'audit_logs',
            'personal_access_tokens',
            // Add potentially existing tables
            'impersonation_codes',
        ];

        // 2. Table Updates (Auto-Increment) & 3. Data Migration (Existing Records)
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Determine the primary key name (usually 'id')
                $pk = 'id'; // Default
                
                // Ensure the table has the PK column before trying to update it
                if (Schema::hasColumn($table, $pk)) {
                    // Set Auto Increment to 100000
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 100000;");

                    // Shift existing IDs: UPDATE table SET id = id + 100000 WHERE id < 100000;
                    // We check < 100000 to avoid double-migration if run multiple times partially (though unlikely in one transaction)
                    DB::statement("UPDATE `{$table}` SET `{$pk}` = `{$pk}` + 100000 WHERE `{$pk}` < 100000;");
                }
            }
        }

        // 4. Foreign Key & Relationship Updates
        
        // --- subscriptions ---
        if (Schema::hasTable('subscriptions')) {
            $this->updateForeignKey('subscriptions', 'user_id');
            $this->updateForeignKey('subscriptions', 'plan_id');
        }

        // --- invoices ---
        if (Schema::hasTable('invoices')) {
            $this->updateForeignKey('invoices', 'user_id');
            // Assuming invoices might link to subscriptions? If so, check schema/logic. Usually 'subscription_id'
            $this->updateForeignKey('invoices', 'subscription_id'); 
        }

        // --- personal_access_tokens (Polymorphic) ---
        if (Schema::hasTable('personal_access_tokens')) {
            // Update tokenable_id for User model
            DB::statement("
                UPDATE personal_access_tokens 
                SET tokenable_id = tokenable_id + 100000 
                WHERE tokenable_id < 100000 
                AND tokenable_type = 'App\\\\Models\\\\User'
            ");
        }

        // --- impersonation_codes ---
        if (Schema::hasTable('impersonation_codes')) {
            $this->updateForeignKey('impersonation_codes', 'user_id');
        }

        // --- audit_logs (Polymorphic & FKs) ---
        if (Schema::hasTable('audit_logs')) {
            // Direct FKs if they exist (some implementations use 'user_id', others 'actor_id')
            $this->updateForeignKey('audit_logs', 'user_id');
            $this->updateForeignKey('audit_logs', 'actor_id'); // Just in case custom implementation

            // Spatie/Standard Polymorphic: causer_id / causer_type
            // Update causer_id where type is User
            DB::statement("
                UPDATE audit_logs 
                SET causer_id = causer_id + 100000 
                WHERE causer_id < 100000 
                AND causer_type = 'App\\\\Models\\\\User'
            ");

            // Update subject_id where subject_type matches our upgraded tables
            $polymorphicTypes = [
                'App\\\\Models\\\\User',
                'App\\\\Models\\\\Plan',
                'App\\\\Models\\\\Subscription',
                'App\\\\Models\\\\Invoice',
                'App\\\\Models\\\\Coupon',
            ];

            foreach ($polymorphicTypes as $type) {
                DB::statement("
                    UPDATE audit_logs 
                    SET subject_id = subject_id + 100000 
                    WHERE subject_id < 100000 
                    AND subject_type = '{$type}'
                ");
            }
        }

        // 5. Cleanup: Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Helper to update simple foreign keys.
     */
    protected function updateForeignKey($table, $column)
    {
        if (Schema::hasColumn($table, $column)) {
            // Only update if the value is < 100000 (standard IDs)
            // This prevents issues if some IDs were already legitimate large numbers (unlikely but safe)
            DB::statement("UPDATE `{$table}` SET `{$column}` = `{$column}` + 100000 WHERE `{$column}` < 100000;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // NOTE: Reversing this migration is risky because new records might have been 
        // created with IDs > 100000. We will attempt to restore only the shifted records 
        // that fall within the expected range, but Auto-Increment reset is tricky.
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

         $tables = [
            'users', 'plans', 'subscriptions', 'invoices', 'coupons', 
            'audit_logs', 'personal_access_tokens', 'impersonation_codes'
        ];

        // Revert IDs for checks (range 100000 to 200000 - assuming original IDs were < 100000)
        // This is a heuristic rollback.
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $pk = 'id';
                if (Schema::hasColumn($table, $pk)) {
                    DB::statement("UPDATE `{$table}` SET `{$pk}` = `{$pk}` - 100000 WHERE `{$pk}` >= 100000 AND `{$pk}` < 200000;");
                    // Reset auto-increment? Dangerous if we have data > 200000? 
                    // Typically 'ALTER TABLE table AUTO_INCREMENT = 1' will reset to MAX(id)+1
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1;"); 
                }
            }
        }

        // Revert FKs
        $this->revertForeignKey('subscriptions', 'user_id');
        $this->revertForeignKey('subscriptions', 'plan_id');
        $this->revertForeignKey('invoices', 'user_id');
        if (Schema::hasColumn('invoices', 'subscription_id')) $this->revertForeignKey('invoices', 'subscription_id');
        $this->revertForeignKey('impersonation_codes', 'user_id');
        $this->revertForeignKey('audit_logs', 'user_id');
        $this->revertForeignKey('audit_logs', 'actor_id');

        // Revert Polymorphic personal_access_tokens
        if (Schema::hasTable('personal_access_tokens')) {
            DB::statement("
                UPDATE personal_access_tokens 
                SET tokenable_id = tokenable_id - 100000 
                WHERE tokenable_id >= 100000 AND tokenable_id < 200000 
                AND tokenable_type = 'App\\\\Models\\\\User'
            ");
        }

        // Revert Polymorphic audit_logs
        if (Schema::hasTable('audit_logs')) {
             DB::statement("
                UPDATE audit_logs 
                SET causer_id = causer_id - 100000 
                WHERE causer_id >= 100000 AND causer_id < 200000 
                AND causer_type = 'App\\\\Models\\\\User'
            ");
            
            $polymorphicTypes = [
                'App\\\\Models\\\\User', 'App\\\\Models\\\\Plan', 'App\\\\Models\\\\Subscription', 
                'App\\\\Models\\\\Invoice', 'App\\\\Models\\\\Coupon'
            ];
            foreach ($polymorphicTypes as $type) {
                DB::statement("
                    UPDATE audit_logs 
                    SET subject_id = subject_id - 100000 
                    WHERE subject_id >= 100000 AND subject_id < 200000 
                    AND subject_type = '{$type}'
                ");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function revertForeignKey($table, $column)
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            DB::statement("UPDATE `{$table}` SET `{$column}` = `{$column}` - 100000 WHERE `{$column}` >= 100000 AND `{$column}` < 200000;");
        }
    }
};
