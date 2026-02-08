<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Feature;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Supported OAuth providers.
     */
    protected array $supportedProviders = ['google', 'facebook'];

    /**
     * Redirect the user to the OAuth provider authentication page.
     */
    public function redirect(Request $request, string $provider): RedirectResponse
    {
        // Validate provider
        if (!in_array($provider, $this->supportedProviders)) {
            return redirect()->route('login')->with('error', 'Unsupported login provider.');
        }

        // Check if social login is globally enabled
        if (!Feature::enabled('social_login_enabled')) {
            return redirect()->route('login')->with('error', 'Social login is currently disabled.');
        }

        // Check if this specific provider is enabled
        if (!Feature::enabled("{$provider}_login_enabled")) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is not enabled.');
        }

        // Check if provider is configured
        if (!config("services.{$provider}.client_id") || !config("services.{$provider}.client_secret")) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is not configured.');
        }

        try {
            $driver = Socialite::driver($provider);

            // Provider-specific configuration
            if ($provider === 'google') {
                $driver->scopes(['openid', 'email', 'profile'])
                       ->with(['prompt' => 'select_account']);
            } elseif ($provider === 'facebook') {
                $driver->scopes(['email', 'public_profile']);
            }

            return $driver->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Could not connect to ' . ucfirst($provider) . '.');
        }
    }

    /**
     * Obtain the user information from the provider and log them in (or create).
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        // Validate provider
        if (!in_array($provider, $this->supportedProviders)) {
            return redirect()->route('login')->with('error', 'Unsupported login provider.');
        }

        // Check if social login is globally enabled
        if (!Feature::enabled('social_login_enabled')) {
            return redirect()->route('login')->with('error', 'Social login is currently disabled.');
        }

        // Check if this specific provider is enabled
        if (!Feature::enabled("{$provider}_login_enabled")) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is not enabled.');
        }

        // Check if provider is configured
        if (!config("services.{$provider}.client_id") || !config("services.{$provider}.client_secret")) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is not configured.');
        }

        try {
            // Normal flow (with state). If state mismatch, retry stateless.
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            try {
                $socialUser = Socialite::driver($provider)->stateless()->user();
            } catch (\Throwable $e) {
                return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
            }
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Could not authenticate with ' . ucfirst($provider) . '. Please try again.');
        }

        $email = strtolower(trim((string) $socialUser->getEmail()));
        $providerId = $socialUser->getId();
        $avatar = $socialUser->getAvatar();

        if (empty($email)) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' did not return an email. Please use password login.');
        }

        // Provider ID column name
        $providerIdColumn = "{$provider}_id";

        // Try to find existing user by provider ID first, then by email
        /** @var User|null $user */
        $user = User::where($providerIdColumn, $providerId)->first()
                ?? User::where('email', $email)->first();

        if ($user) {
            // Existing user - update provider ID and avatar if needed
            if ($user->isBanned()) {
                return redirect()->route('login')->with('error', 'Your account is banned. Contact support.');
            }

            // Update provider ID if not set
            if (!$user->{$providerIdColumn}) {
                $user->{$providerIdColumn} = $providerId;
            }

            // Update avatar if we have one
            if ($avatar && !$user->avatar) {
                $user->avatar = $avatar;
            }

            $user->save();
        } else {
            // Create a new user record
            $name = $socialUser->getName();
            if (!$name) {
                // Try to get name from user data
                $userData = $socialUser->user ?? [];
                $name = $userData['given_name'] ?? $userData['first_name'] ?? '';
                if (!$name) {
                    $name = strstr($email, '@', true) ?: 'User';
                }
            }

            $username = $this->generateUniqueUsernameFromEmail($email);

            $user = new User();
            $user->name = $name;
            $user->username = $username;
            $user->email = $email;
            $user->password = Str::password(40); // Random strong password (hashed via cast)
            $user->email_verified_at = now(); // Provider verified the email
            $user->{$providerIdColumn} = $providerId;
            $user->avatar = $avatar;
            $user->save();
        }

        // Final safety check
        if ($user->isBanned()) {
            return redirect()->route('login')->with('error', 'Your account is banned. Contact support.');
        }

        // Log in and remember
        Auth::login($user, remember: true);

        // Redirect to intended destination
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Generate a unique username from the email's local part.
     */
    protected function generateUniqueUsernameFromEmail(string $email): string
    {
        $base = strtolower(trim(strstr($email, '@', true) ?: 'user'));
        $base = preg_replace('/[^a-z0-9_]/', '', $base) ?: 'user';

        $username = $base;

        // If already taken, append numeric suffixes
        $i = 1;
        while (User::where('username', $username)->exists() && $i < 1000) {
            $username = $base . $i;
            $i++;
        }

        // As a last resort, append a short random string
        if (User::where('username', $username)->exists()) {
            $username = $base . '_' . Str::lower(Str::random(6));
        }

        return $username;
    }
}
