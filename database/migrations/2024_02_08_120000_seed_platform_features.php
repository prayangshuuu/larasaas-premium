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
        // Define the platform feature flags and modules to be enabled/configured
        // These keys correspond to the requirements for Social Auth, Subscriptions, etc.
        $features = [
            [
                'key' => 'social_login_enabled',
                'value' => 'true', // Master switch for all social authentication providers
            ],
            [
                'key' => 'subscription_module_enabled',
                'value' => 'true', // Enable or disable the entire subscription & billing system
            ],
            [
                'key' => 'stripe_payments_enabled',
                'value' => 'true', // Enable real payment processing via Stripe
            ],
            [
                'key' => 'impersonation_enabled',
                'value' => 'true', // Allow admins to log in as other users
            ],
            [
                'key' => 'editable_usernames_enabled',
                'value' => 'true', // Allow admins to change user handles
            ],
            [
                'key' => 'admin_mfa_required',
                'value' => 'false', // Admins must have 2FA enabled (defaulting to false to prevent lockout)
            ],
            [
                'key' => 'support_desk_enabled',
                'value' => 'true', // Allow users to view and create support tickets
            ],
            [
                'key' => 'ticket_auto_reply_enabled',
                'value' => 'true', // Automatically post a system message when a user creates a ticket
            ],
        ];

        foreach ($features as $feature) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $feature['key']],
                ['value' => $feature['value']]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'social_login_enabled',
            'subscription_module_enabled',
            'stripe_payments_enabled',
            'impersonation_enabled',
            'editable_usernames_enabled',
            'admin_mfa_required',
            'support_desk_enabled',
            'ticket_auto_reply_enabled',
        ];

        DB::table('system_settings')->whereIn('key', $keys)->delete();
    }
};