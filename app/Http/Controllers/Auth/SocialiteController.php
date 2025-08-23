<?php

namespace App\Http\Controllers\Auth;

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
     * Redirect the user to the Google authentication page.
     */
    public function redirect(Request $request): RedirectResponse
    {
        // Basic safety: ensure provider is configured
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->with('error', 'Google login is not configured.');
        }

        // You can pass optional parameters here (like prompt=select_account)
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google and log them in (or create).
     */
    public function callback(Request $request): RedirectResponse
    {
        // Guard against missing config
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->with('error', 'Google login is not configured.');
        }

        try {
            // Normal flow (with state). If state mismatch (common in some local setups), retry stateless.
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Could not authenticate with Google.');
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));

        if (empty($email)) {
            // We require a verified email from Google to map accounts safely.
            return redirect()->route('login')->with('error', 'Google did not return an email. Please use password login.');
        }

        // Try to find existing user by email
        /** @var \App\Models\User|null $user */
        $user = User::query()->where('email', $email)->first();

        if ($user) {
            // Respect bans
            if ($user->isBanned()) {
                return redirect()->route('login')->with('error', 'Your account is banned. Contact support.');
            }
        } else {
            // Create a new user record
            $name = $googleUser->getName() ?: (string) $googleUser->user['given_name'] ?? '';
            if (! $name) {
                $name = strstr($email, '@', true) ?: 'User';
            }

            $username = $this->generateUniqueUsernameFromEmail($email);

            $user = new User();
            $user->name              = $name;
            $user->username          = $username;
            $user->email             = $email;
            $user->password          = Str::password(40); // hashed via cast
            $user->email_verified_at = now();             // Google returned the email; treat as verified
            // DO NOT set is_admin automatically. Leave it false.
            $user->save();
        }

        // Final safety check
        if ($user->isBanned()) {
            return redirect()->route('login')->with('error', 'Your account is banned. Contact support.');
        }

        // Log in and remember
        Auth::login($user, remember: true);

        // Redirect to intended (e.g., /admin/dashboard for admins via your DashboardController)
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Generate a unique username from the email's local part.
     * - sanitize to [a-z0-9_]
     * - ensure uniqueness by suffixing numbers if needed
     */
    protected function generateUniqueUsernameFromEmail(string $email): string
    {
        $base = strtolower(trim(strstr($email, '@', true) ?: 'user'));
        $base = preg_replace('/[^a-z0-9_]/', '', $base) ?: 'user';

        $username = $base;

        // If already taken, append numeric suffixes
        $i = 1;
        while (
            User::query()->where('username', $username)->exists()
            && $i < 1000 // Just a reasonable cap
        ) {
            $username = $base . $i;
            $i++;
        }

        // As a last resort, append a short random string
        if (User::query()->where('username', $username)->exists()) {
            $username = $base . '_' . Str::lower(Str::random(6));
        }

        return $username;
    }
}
