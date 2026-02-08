<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SocialiteConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * 
     * Dynamically configure Socialite providers from database settings.
     * This allows admins to manage OAuth credentials via the admin panel
     * instead of requiring .env file edits.
     */
    public function boot(): void
    {
        // Only attempt to load from DB if the table exists
        // This prevents errors during migrations or fresh installs
        if (!$this->canAccessDatabase()) {
            return;
        }

        $this->configureSocialiteProviders();
    }

    /**
     * Check if we can safely access the database.
     */
    protected function canAccessDatabase(): bool
    {
        try {
            // Check if the system_settings table exists
            if (!Schema::hasTable('system_settings')) {
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            // Database connection might not be available yet
            return false;
        }
    }

    /**
     * Configure Socialite providers from database settings.
     */
    protected function configureSocialiteProviders(): void
    {
        try {
            // Fetch all social login related settings at once
            $settings = SystemSetting::whereIn('key', [
                'social_login_enabled',
                'google_login_enabled',
                'google_client_id',
                'google_client_secret',
                'facebook_login_enabled',
                'facebook_client_id',
                'facebook_client_secret',
                'twitter_login_enabled',
                'twitter_client_id',
                'twitter_client_secret',
            ])->pluck('value', 'key');

            // Configure Google
            $googleClientId = $settings->get('google_client_id');
            $googleClientSecret = $settings->get('google_client_secret');

            if ($googleClientId && $googleClientSecret) {
                config([
                    'services.google.client_id' => $googleClientId,
                    'services.google.client_secret' => $googleClientSecret,
                    'services.google.redirect' => url('/auth/google/callback'),
                ]);
            }

            // Configure Facebook
            $facebookClientId = $settings->get('facebook_client_id');
            $facebookClientSecret = $settings->get('facebook_client_secret');

            if ($facebookClientId && $facebookClientSecret) {
                config([
                    'services.facebook.client_id' => $facebookClientId,
                    'services.facebook.client_secret' => $facebookClientSecret,
                    'services.facebook.redirect' => url('/auth/facebook/callback'),
                ]);
            }

            // Configure Twitter (X)
            $twitterClientId = $settings->get('twitter_client_id');
            $twitterClientSecret = $settings->get('twitter_client_secret');

            if ($twitterClientId && $twitterClientSecret) {
                config([
                    'services.twitter.client_id' => $twitterClientId,
                    'services.twitter.client_secret' => $twitterClientSecret,
                    'services.twitter.redirect' => url('/auth/twitter/callback'),
                ]);
            }

        } catch (\Throwable $e) {
            // Silently fail - OAuth just won't work until configured
            // This prevents app from crashing during setup
        }
    }
}
