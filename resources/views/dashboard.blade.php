{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Dynamic states
        $emailVerified = !($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) || $user->hasVerifiedEmail();
        $twofaEnabled  = (bool) $user->two_factor_secret;

        // Simple completeness calc (name + verified email + profile picture)
        $progress = 0;
        if (!empty($user->name)) $progress += 33;
        if ($emailVerified)       $progress += 33;
        if (!empty($user->profile_picture)) $progress += 34;
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-base-content">Dashboard</h1>
                <p class="text-sm text-base-content/70">Welcome back, {{ $user->name }}.</p>
            </div>
        </div>

        {{-- Account overview (title + completeness + quick state) --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-6 items-center">
                    <div>
                        <h2 class="card-title text-base-content text-lg">Account Overview</h2>
                        <p class="mt-1 text-sm text-base-content/70">
                            Manage your profile, security, and preferences.
                        </p>

                        {{-- Live theme label (reads current localStorage theme periodically) --}}
                        <div class="mt-2 text-sm text-base-content/80"
                             x-data="{ theme: (localStorage.getItem('theme') || 'nord') }"
                             x-init="setInterval(()=>{theme = localStorage.getItem('theme') || 'nord'}, 400)">
                            Theme: <span class="font-medium" x-text="theme === 'dim' ? 'Dim' : 'Nord'">Nord</span>
                            <span class="text-base-content/50">(use toggle in navbar)</span>
                        </div>

                        {{-- Quick state badges (email & 2FA) --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="badge {{ $emailVerified ? 'badge-success' : 'badge-warning' }}">
                            {{ $emailVerified ? __('Email verified') : __('Email unverified') }}
                        </span>
                            <span class="badge {{ $twofaEnabled ? 'badge-primary' : 'badge-ghost' }}">
                            {{ $twofaEnabled ? __('2FA enabled') : __('2FA disabled') }}
                        </span>
                        </div>
                    </div>

                    {{-- Completeness meter (bigger, on the right) --}}
                    <div class="flex flex-col items-center justify-center gap-2 self-stretch">
                        <div class="tooltip tooltip-left" data-tip="{{ __('Profile completeness') }}">
                            <div class="radial-progress text-primary"
                                 style="--value: {{ $progress }}; --size: 7.5rem; --thickness: 8px"
                                 role="progressbar">
                                <span class="text-sm font-semibold">{{ $progress }}%</span>
                            </div>
                        </div>
                        @if($progress === 100)
                            <span class="badge badge-success badge-lg px-4">{{ __('All set') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Key status + actions --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                {{-- Stats row (DaisyUI) --}}
                <div class="stats stats-vertical sm:stats-horizontal shadow-none bg-base-200/50 rounded-xl">
                    <div class="stat">
                        <div class="stat-title">Email</div>
                        <div class="stat-value {{ $emailVerified ? 'text-success' : 'text-warning' }}">
                            {{ $emailVerified ? 'Verified' : 'Unverified' }}
                        </div>
                        <div class="stat-desc text-base-content/70 truncate">{{ $user->email }}</div>
                    </div>

                    <div class="stat">
                        <div class="stat-title">2FA</div>
                        <div class="stat-value {{ $twofaEnabled ? 'text-primary' : 'text-base-content' }}">
                            {{ $twofaEnabled ? 'Enabled' : 'Disabled' }}
                        </div>
                        <div class="stat-desc text-base-content/70">Authenticator app (TOTP)</div>
                    </div>

                    <div class="stat" x-data="{ theme: (localStorage.getItem('theme') || 'nord') }"
                         x-init="setInterval(()=>{theme = localStorage.getItem('theme') || 'nord'}, 400)">
                        <div class="stat-title">Theme</div>
                        <div class="stat-value" x-text="theme === 'dim' ? 'Dim' : 'Nord'">Nord</div>
                        <div class="stat-desc text-base-content/70">Change from navbar</div>
                    </div>
                </div>

                {{-- Actions (responsive) --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <a href="{{ route('profile.edit') }}" class="btn btn-ghost rounded-xl border border-base-300 h-11">
                        Edit Profile
                    </a>

                    {{-- Email verification action (only if unverified & uses Laravel verification routes) --}}
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-warning rounded-xl h-11 w-full">
                                Resend verification email
                            </button>
                        </form>
                    @else
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline rounded-xl h-11">
                            Manage Email
                        </a>
                    @endif

                    {{-- 2FA toggle --}}
                    @if ($twofaEnabled)
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-error rounded-xl h-11 w-full">
                                Disable 2FA
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-xl h-11 w-full">
                                Enable 2FA
                            </button>
                        </form>
                    @endif

                    {{-- Change password just directs to profile page (where your password form lives) --}}
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline rounded-xl h-11">
                        Change Password
                    </a>

                    {{-- View profile (for completeness) --}}
                    <a href="{{ route('profile.edit') }}" class="btn btn-ghost rounded-xl border border-base-300 h-11">
                        Account Settings
                    </a>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="btn rounded-xl h-11 w-full
                                   bg-neutral text-neutral-content hover:opacity-95
                                   dark:bg-neutral-content dark:text-neutral dark:border-base-300">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
