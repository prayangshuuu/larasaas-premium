<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Send the user to Google for authentication.
     */
    public function redirect(): RedirectResponse
    {
        // Ask for basic scopes; 'openid' helps providers confirm email_verified.
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    /**
     * Handle Google callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            // Use stateless() if you have session issues locally with the callback.
            $google = Socialite::driver('google')->user();
            // $google = Socialite::driver('google')->stateless()->user();

            $googleId     = (string) $google->getId();       // Google sub
            $googleEmail  = (string) $google->getEmail();    // may be null in rare scopes/consent cases
            $googleName   = (string) ($google->getName() ?: ($google->getNickname() ?? 'Google User'));
            $googleAvatar = $google->getAvatar();            // url or null
            $raw          = $google->user ?? [];             // raw array from provider

            $isEmailVerified = false;
            if (is_array($raw)) {
                // Common flag from Google: "email_verified" true/false
                $isEmailVerified = (bool) ($raw['email_verified'] ?? false);
            }

            $user = null;

            DB::transaction(function () use (
                &$user,
                $googleId,
                $googleEmail,
                $googleName,
                $googleAvatar,
                $isEmailVerified,
                $raw
            ) {
                // 1) If this provider account is already linked, load that user
                $user = User::query()
                    ->where('provider', 'google')
                    ->where('provider_id', $googleId)
                    ->first();

                // 2) If not linked, try to find by email and link accounts
                if (!$user && $googleEmail) {
                    $user = User::query()->where('email', $googleEmail)->first();

                    if ($user) {
                        // Link Google to existing local account
                        $user->provider  = 'google';
                        $user->provider_id = $googleId;
                        $user->provider_avatar = $googleAvatar ?: $user->provider_avatar;
                        // If Google confirms the email is verified, set it if not already verified
                        if ($isEmailVerified && is_null($user->email_verified_at)) {
                            $user->email_verified_at = now();
                        }
                        $user->save();
                    }
                }

                // 3) Create a brand new user if still not found
                if (!$user) {
                    // Build a unique username from name or email local-part
                    $base = $googleEmail ? Str::before($googleEmail, '@') : Str::slug($googleName);
                    $username = $this->uniqueUsername($base ?: 'user');

                    $user = new User();
                    $user->name = $googleName;
                    $user->username = $username;
                    $user->email = $googleEmail ?: "google+{$googleId}@example.invalid";
                    $user->password = Str::password(32); // random; user logs in via Google

                    // Trust Google verified flag (optional)
                    if ($isEmailVerified && $googleEmail) {
                        $user->email_verified_at = now();
                    }

                    // Link provider
                    $user->provider = 'google';
                    $user->provider_id = $googleId;
                    $user->provider_avatar = $googleAvatar ?: null;

                    // You can store tokens if you plan to call Google APIs later
                    // (Socialite Google doesn't always include refresh_token on every login)
                    if (isset($raw['access_token'])) {
                        $user->provider_token = $raw['access_token'];
                    }
                    if (isset($raw['refresh_token'])) {
                        $user->provider_refresh_token = $raw['refresh_token'];
                    }
                    if (isset($raw['expires_in'])) {
                        $user->provider_token_expires_at = now()->addSeconds((int) $raw['expires_in']);
                    }

                    $user->save();
                }

                // Update avatar / token info on each login if present
                $changed = false;
                if ($googleAvatar && $user->provider_avatar !== $googleAvatar) {
                    $user->provider_avatar = $googleAvatar; $changed = true;
                }
                if (isset($raw['access_token'])) {
                    $user->provider_token = $raw['access_token']; $changed = true;
                }
                if (isset($raw['refresh_token'])) {
                    $user->provider_refresh_token = $raw['refresh_token']; $changed = true;
                }
                if (isset($raw['expires_in'])) {
                    $user->provider_token_expires_at = now()->addSeconds((int) $raw['expires_in']); $changed = true;
                }
                if ($changed) {
                    $user->save();
                }
            });

            // Ban check before login
            if (method_exists($user, 'isBanned') && $user->isBanned()) {
                return redirect()
                    ->route('login')
                    ->withErrors(['email' => 'Your account is banned.']);
            }

            // Log them in & regenerate the session
            Auth::login($user, true);
            $request->session()->regenerate();

            // Audit log (best-effort)
            $this->audit('auth.social.login', 'Google login', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            // Send to admin or normal dashboard
            return redirect()->intended(
                $user->isAdmin() ? route('admin.dashboard') : route('dashboard')
            )->with('success', 'Signed in with Google!');
        } catch (Throwable $e) {
            // Optionally log $e->getMessage()
            $this->audit('auth.social.failed', 'Google login failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google login failed. Please try again.']);
        }
    }

    /**
     * Make a unique username from a base string.
     */
    protected function uniqueUsername(string $base): string
    {
        $slug = Str::slug($base);
        $slug = $slug !== '' ? $slug : 'user';

        if (!User::where('username', $slug)->exists()) {
            return $slug;
        }

        // Try slug-1234 variants
        for ($i = 1; $i <= 50; $i++) {
            $candidate = $slug.'-'.$i;
            if (!User::where('username', $candidate)->exists()) {
                return $candidate;
            }
        }

        // Last resort: random suffix
        do {
            $candidate = $slug.'-'.Str::lower(Str::random(5));
        } while (User::where('username', $candidate)->exists());

        return $candidate;
    }

    /**
     * Best-effort audit helper (works with your AuditLog model if present).
     */
    protected function audit(string $action, string $description, array $metadata = []): void
    {
        try {
            if (class_exists(AuditLog::class)) {
                if (method_exists(AuditLog::class, 'log')) {
                    AuditLog::log($action, Auth::id(), null, $description, $metadata);
                } else {
                    AuditLog::create([
                        'actor_id'   => Auth::id(),
                        'action'     => $action,
                        'description'=> $description,
                        'metadata'   => $metadata ?: null,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
        } catch (Throwable) {
            // never block login flow on audit issues
        }
    }
}
