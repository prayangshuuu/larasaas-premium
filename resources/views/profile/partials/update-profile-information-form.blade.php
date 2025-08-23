{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
<section>
    @php
        /** @var \App\Models\User $user */
        $emailVerified = !($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) || $user->hasVerifiedEmail();
        $twofaEnabled  = (bool) auth()->user()->two_factor_secret;

        // Detect "confirmed" state (Fortify supports a confirmed timestamp)
        $twofaConfirmed = false;
        if (method_exists($user, 'hasConfirmedTwoFactor')) {
            $twofaConfirmed = $user->hasConfirmedTwoFactor();
        } else {
            $twofaConfirmed = !empty($user->two_factor_confirmed_at);
        }

        $progress = 0;
        if (!empty($user->name))           $progress += 33;
        if ($emailVerified)                 $progress += 33;
        if (!empty($user->profile_picture)) $progress += 34;

        // Username edit policy via settings (feature flag)
        $settings        = \App\Models\Setting::instance();
        $canEditUsername = (bool) $settings->feature_usernames_editable;

        // Fortify sets this when 2FA just got enabled; use it to auto-open the modal
        $showTwoFactorSetup = session('status') === 'two-factor-authentication-enabled';
    @endphp

    {{-- Auto-open the 2FA setup modal after enabling --}}
    @includeIf('profile.partials.two-factor-setup-modal', ['show' => $showTwoFactorSetup])

    {{-- Success/Info toasts for key 2FA events --}}
    @if (session('status') === 'two-factor-authentication-confirmed')
        <div class="alert alert-success rounded-2xl mb-4">
            <span>{{ __('Two-factor authentication confirmed.') }}</span>
        </div>
    @endif
    @if (session('status') === 'recovery-codes-generated')
        <div class="alert alert-success rounded-2xl mb-4">
            <span>{{ __('New recovery codes generated.') }}</span>
        </div>
    @endif

    <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
        <div class="card-body p-6 sm:p-8">
            {{-- HEADER: left (title+desc+status), right (radial + All set) --}}
            <header class="grid grid-cols-1 sm:grid-cols-[1fr_auto] items-center gap-4">
                <div>
                    <h2 class="card-title text-base-content text-lg">
                        {{ __('Profile Information') }}
                    </h2>
                    <p class="mt-1 text-sm text-base-content/70">
                        {{ __("Update your account's profile information, picture, email address, and 2FA settings.") }}
                    </p>
                    <p class="mt-1 text-sm text-base-content/80">
                        {{ $emailVerified ? __('Email verified') : __('Email unverified') }},
                        @if ($twofaEnabled)
                            {{ $twofaConfirmed ? __('2FA confirmed') : __('2FA pending confirmation') }}
                        @else
                            {{ __('2FA disabled') }}
                        @endif
                    </p>
                </div>

                {{-- Right side: centered column, larger circle, All set below --}}
                <div class="hidden sm:flex flex-col items-center justify-center gap-2 self-stretch">
                    <div class="tooltip tooltip-left" data-tip="{{ __('Profile completeness') }}">
                        <div class="radial-progress text-primary"
                             style="--value: {{ $progress }}; --size: 8rem; --thickness: 8px"
                             role="progressbar">
                            <span class="text-sm font-semibold">{{ $progress }}%</span>
                        </div>
                    </div>
                    @if($progress === 100)
                        <span class="badge badge-success badge-lg gap-2 px-4">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('All set') }}
                        </span>
                    @endif
                </div>
            </header>

            <div class="divider my-4"></div>

            {{-- Profile Picture (right aligned, click to change → auto-submit) --}}
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                  class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                @csrf
                @method('patch')

                <label class="text-sm font-medium text-base-content">{{ __('Profile Picture') }}</label>

                <div class="hidden sm:block"></div>

                <div class="flex justify-end">
                    <div class="relative group">
                        <input id="profile_picture" name="profile_picture" type="file"
                               class="sr-only" accept="image/png,image/jpeg,image/webp"
                               onchange="this.form.submit()">

                        <div class="avatar">
                            <div class="w-24 h-24 mask mask-circle ring ring-primary ring-offset-base-100 ring-offset-2">
                                <img class="object-cover"
                                     src="{{ $user->profile_picture
                                            ? \Illuminate\Support\Facades\Storage::url($user->profile_picture)
                                            : asset('images/default-avatar.png') }}"
                                     alt="{{ $user->name }}">
                            </div>
                        </div>

                        <label for="profile_picture"
                               class="absolute inset-0 grid place-items-center rounded-full
                                      bg-base-100/70 opacity-0 group-hover:opacity-100
                                      transition cursor-pointer text-sm font-medium">
                            {{ __('Change') }}
                        </label>
                        @error('profile_picture') <p class="text-error text-sm mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>
            </form>

            <div class="divider my-6"></div>

            {{-- Icon – Label – Field (single-line rows, uniform sizes) --}}
            <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('patch')

                {{-- Name --}}
                <div class="flex items-center gap-3">
                    <span class="btn btn-ghost btn-square pointer-events-none" aria-hidden="true">
                        {{-- user icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 19.5a7.5 7.5 0 0115 0v.75H4.5v-.75z"/>
                        </svg>
                    </span>
                    <label for="name" class="w-36 shrink-0 text-sm font-medium text-base-content">
                        {{ __('Name') }}
                    </label>
                    <input id="name" name="name" type="text"
                           class="input input-bordered h-12 w-full"
                           value="{{ old('name', $user->name) }}" required autocomplete="name" />
                </div>
                @error('name') <p class="text-error text-sm">{{ $message }}</p> @enderror

                {{-- Email --}}
                <div class="flex items-center gap-3">
                    <span class="btn btn-ghost btn-square pointer-events-none" aria-hidden="true">
                        {{-- envelope icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5A2.25 2.25 0 002.25 6.75m19.5 0L12 12.75 2.25 6.75" />
                        </svg>
                    </span>
                    <label for="email" class="w-36 shrink-0 text-sm font-medium text-base-content">
                        {{ __('Email') }}
                    </label>
                    <input id="email" name="email" type="email"
                           class="input input-bordered h-12 w-full"
                           value="{{ old('email', $user->email) }}" required autocomplete="email" />
                </div>
                @error('email') <p class="text-error text-sm">{{ $message }}</p> @enderror

                {{-- Username (editable only when feature flag is ON) --}}
                <div class="flex items-center gap-3">
                    <span class="btn btn-ghost btn-square pointer-events-none" aria-hidden="true">
                        {{-- at-symbol icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16.5 12a4.5 4.5 0 11-2.64-4.11m2.64 4.11v1.5a1.5 1.5 0 003 0V12a7.5 7.5 0 10-2.2 5.3" />
                        </svg>
                    </span>
                    <label for="username" class="w-36 shrink-0 text-sm font-medium text-base-content">
                        {{ __('Username') }}
                    </label>

                    @if($canEditUsername)
                        <input id="username" name="username" type="text"
                               class="input input-bordered h-12 w-full"
                               value="{{ old('username', $user->username) }}" autocomplete="username" />
                        @error('username') <p class="text-error text-sm">{{ $message }}</p> @enderror
                    @else
                        {{-- keep layout consistent; disabled and no name attribute --}}
                        <input id="username" type="text"
                               class="input input-bordered h-12 w-full"
                               value="{{ $user->username }}" disabled />
                    @endif
                </div>

                <div class="card-actions justify-end pt-2">
                    <button type="submit" class="btn btn-primary h-12 min-w-40">
                        {{ __('Save changes') }}
                    </button>
                    @if (session('status') === 'profile-updated')
                        <span class="badge badge-success badge-outline">{{ __('Saved') }}</span>
                    @endif
                </div>
            </form>

            {{-- Email verification notices --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-6">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M4.93 19.07A10 10 0 1119.07 4.93 10 10 0 014.93 19.07z"/>
                        </svg>
                        <span>{{ __('Your email address is unverified.') }}</span>
                    </div>
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline-block ml-2">
                        @csrf
                        <button class="btn btn-sm btn-outline btn-primary">
                            {{ __('Resend verification email') }}
                        </button>
                    </form>
                </div>
                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-3">
                        <span>{{ __('A new verification link has been sent to your email address.') }}</span>
                    </div>
                @endif
            @endif

            <div class="divider my-6"></div>

            {{-- Two-Factor Authentication --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 items-start gap-4">
                <div class="sm:col-span-2">
                    <h3 class="font-medium text-base-content flex items-center gap-2">
                        {{-- shield icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253l7.5 4.327v5.62A2.75 2.75 0 0116.75 19H7.25A2.75 2.75 0 014.5 16.2v-5.62L12 6.253z"/>
                        </svg>
                        {{ __('Two-Factor Authentication (2FA)') }}
                        <span class="badge {{ $twofaEnabled ? ($twofaConfirmed ? 'badge-primary' : 'badge-warning') : 'badge-ghost' }} ml-2">
                            {{ $twofaEnabled ? ($twofaConfirmed ? __('Confirmed') : __('Pending')) : __('Disabled') }}
                        </span>
                    </h3>
                    <p class="mt-1 text-xs text-base-content/70">
                        {{ __('Use an authenticator app (TOTP) for stronger security.') }}
                    </p>
                </div>

                <div class="sm:text-right space-x-2 space-y-2 sm:space-y-0">
                    @if ($twofaEnabled)
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="inline-flex">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-error rounded-xl">
                                {{ __('Disable 2FA') }}
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="inline-flex">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-primary rounded-xl">
                                {{ __('Enable 2FA') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- If enabled but not confirmed, show inline confirm form as a backup --}}
            @if ($twofaEnabled && ! $twofaConfirmed)
                <div class="alert alert-info mt-4">
                    <span>{{ __('Enter the 6-digit code from your authenticator app to finish setup.') }}</span>
                </div>
                <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="mt-3">
                    @csrf
                    <div class="flex items-center gap-3">
                        <label for="code" class="w-36 shrink-0 text-sm font-medium text-base-content">
                            {{ __('Confirmation code') }}
                        </label>
                        <input id="code" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                               class="input input-bordered h-12 w-full sm:max-w-xs"
                               placeholder="123456" required />
                        <button type="submit" class="btn btn-primary h-12">
                            {{ __('Confirm') }}
                        </button>
                    </div>
                    @error('code') <p class="text-error text-sm mt-2">{{ $message }}</p> @enderror
                </form>
            @endif

            {{-- Recovery code actions (available after enabling) --}}
            @if ($twofaEnabled)
                <div class="divider my-6"></div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <div class="font-medium text-base-content">{{ __('Recovery Codes') }}</div>
                        <div class="text-sm text-base-content/70">
                            {{ __('Keep these safe. You can view them (after password confirmation) or regenerate new ones.') }}
                        </div>
                    </div>

                    <div class="space-x-2">
                        <a href="{{ route('two-factor.codes.show') }}" class="btn btn-ghost rounded-xl border border-base-300">
                            {{ __('Show Codes') }}
                        </a>

                        <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}"
                              class="inline-flex"
                              onsubmit="return confirm('{{ __('Regenerate recovery codes? Old codes will stop working.') }}');">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-primary rounded-xl">
                                {{ __('Regenerate') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</section>
