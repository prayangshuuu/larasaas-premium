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

    <div class="max-w-7xl mx-auto space-y-8 animate-fade-in-up">

        {{-- Page header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    Dashboard
                </h1>
                <p class="text-base-content/70 mt-2 text-lg">Welcome back, <span class="font-semibold text-primary">{{ $user->name }}</span>. Ready to boost your band score?</p>
            </div>
            <div class="flex gap-2">
                 <button class="btn btn-primary gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Resume Learning
                </button>
            </div>
        </div>

        {{-- IELTS Progress Stats (DaisyUI Stats) --}}
        <div class="stats stats-vertical lg:stats-horizontal shadow-lg w-full bg-base-100 border border-base-200">
            
            {{-- Reading --}}
            <div class="stat">
                <div class="stat-figure text-primary">
                    <div class="radial-progress bg-primary/10 text-primary border-4 border-transparent" style="--value:75; --size:3rem;">75%</div>
                </div>
                <div class="stat-title font-medium">Reading</div>
                <div class="stat-value text-primary">7.5</div>
                <div class="stat-desc">12 tests completed</div>
            </div>
            
            {{-- Writing --}}
            <div class="stat">
                <div class="stat-figure text-secondary">
                     <div class="radial-progress bg-secondary/10 text-secondary border-4 border-transparent" style="--value:65; --size:3rem;">65%</div>
                </div>
                <div class="stat-title font-medium">Writing</div>
                <div class="stat-value text-secondary">6.5</div>
                <div class="stat-desc">8 essays submitted</div>
            </div>
            
            {{-- Listening --}}
            <div class="stat">
                <div class="stat-figure text-accent">
                     <div class="radial-progress bg-accent/10 text-accent border-4 border-transparent" style="--value:80; --size:3rem;">80%</div>
                </div>
                <div class="stat-title font-medium">Listening</div>
                <div class="stat-value text-accent">8.0</div>
                <div class="stat-desc">15 tests completed</div>
            </div>

             {{-- Speaking --}}
             <div class="stat">
                <div class="stat-figure text-info">
                     <div class="radial-progress bg-info/10 text-info border-4 border-transparent" style="--value:70; --size:3rem;">70%</div>
                </div>
                <div class="stat-title font-medium">Speaking</div>
                <div class="stat-value text-info">7.0</div>
                <div class="stat-desc">6 mock interviews</div>
            </div>
            
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Account Overview Card --}}
            <div class="card bg-base-100 shadow-md border border-base-200 lg:col-span-2">
                <div class="card-body">
                    <h2 class="card-title flex items-center gap-2 mb-4">
                        <div class="p-2 bg-base-200 rounded-lg text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        Account Status
                    </h2>

                    <div class="flex flex-col sm:flex-row items-center gap-8">
                         {{-- Completion Circle --}}
                        <div class="relative">
                            <div class="radial-progress text-primary" style="--value:{{ $progress }}; --size:8rem; --thickness: 0.8rem;" role="progressbar">
                                <span class="text-2xl font-bold text-base-content">{{ $progress }}%</span>
                            </div>
                            <div class="text-center mt-2 text-sm font-medium text-base-content/70">Profile Complete</div>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 space-y-4 w-full">
                            <div class="flex items-center justify-between p-3 bg-base-200/50 rounded-xl">
                                <span class="font-medium">Email Verification</span>
                                <span class="badge {{ $emailVerified ? 'badge-success' : 'badge-warning' }} gap-1">
                                    {{ $emailVerified ? 'Verified' : 'Pending' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-base-200/50 rounded-xl">
                                <span class="font-medium">Two-Factor Auth</span>
                                <span class="badge {{ $twofaEnabled ? 'badge-primary' : 'badge-ghost' }} gap-1">
                                    {{ $twofaEnabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>

                            @if($progress < 100)
                                <div class="alert alert-warning shadow-sm py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <span class="text-sm">Complete your profile to unlock all features!</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title flex items-center gap-2 mb-4">
                        <div class="p-2 bg-base-200 rounded-lg text-secondary">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        Quick Actions
                    </h2>
                    
                    <ul class="menu bg-base-200/50 w-full rounded-box gap-2">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="flex gap-3 active:bg-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Profile
                            </a>
                        </li>
                        <li>
                            <a class="flex gap-3 hover:text-error">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Change Password
                            </a>
                        </li>
                    </ul>

                     <div class="divider my-2">Security</div>
                     
                     @if ($twofaEnabled)
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error btn-outline w-full gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Disable 2FA
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-full gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Enable 2FA
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
