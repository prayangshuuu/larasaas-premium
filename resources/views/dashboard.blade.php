{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Dynamic states
        $emailVerified = !($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) || $user->hasVerifiedEmail();
        $twofaEnabled  = (bool) $user->two_factor_secret;
        
        // ORM Accessors
        $stats = $user->ielts_stats;
        $progress = $user->profile_completeness;
    @endphp

    <div class="space-y-8 animate-fade-in">

        {{-- Page header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                    <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    Dashboard
                </h1>
                <p class="mt-2 text-lg text-slate-600">Welcome back, <span class="font-semibold text-primary-600">{{ $user->name }}</span>. Ready to boost your band score?</p>
            </div>
            <div class="flex gap-2">
                 <button class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Resume Learning
                </button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $colors = [
                    'reading' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'ring' => 'text-blue-600'],
                    'writing' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'ring' => 'text-purple-600'],
                    'listening' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'ring' => 'text-amber-600'],
                    'speaking' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'ring' => 'text-emerald-600']
                ];
            @endphp

            @foreach($stats as $key => $data)
                @php 
                    $theme = $colors[$key] ?? $colors['reading']; 
                    $percent = $data['progress'];
                    $circumference = 2 * pi() * 20; // r=20
                    $strokeDashoffset = $circumference - ($percent / 100) * $circumference;
                @endphp
                <div class="bg-white overflow-hidden rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between h-full hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">{{ $key }}</p>
                            <p class="mt-1 text-3xl font-bold {{ $theme['text'] }}">{{ number_format($data['score'], 1) }}</p>
                        </div>
                        
                        {{-- SVG Circular Progress --}}
                        <div class="relative w-12 h-12">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 44 44">
                                <circle cx="22" cy="22" r="20" fill="none" stroke-width="4" class="text-slate-100" stroke="currentColor"></circle>
                                <circle cx="22" cy="22" r="20" fill="none" stroke-width="4" class="{{ $theme['text'] }}" stroke="currentColor" 
                                        stroke-dasharray="{{ $circumference }}" 
                                        stroke-dashoffset="{{ $strokeDashoffset }}"
                                        stroke-linecap="round"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-slate-700">
                                {{ $percent }}%
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500">
                        {{ $data['desc'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Account Overview Card --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-6">
                        <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        Account Status
                    </h2>

                    <div class="flex flex-col sm:flex-row items-center gap-8">
                         {{-- Large Completion Circle --}}
                        <div class="relative w-32 h-32 shrink-0">
                            @php
                                $circumferenceLg = 2 * pi() * 56; // r=56 (adjusted for padding)
                                $strokeDashoffsetLg = $circumferenceLg - ($progress / 100) * $circumferenceLg;
                            @endphp
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 128 128">
                                <circle cx="64" cy="64" r="56" fill="none" stroke-width="12" class="text-slate-100" stroke="currentColor"></circle>
                                <circle cx="64" cy="64" r="56" fill="none" stroke-width="12" class="text-primary-600 transition-all duration-1000 ease-out" stroke="currentColor" 
                                        stroke-dasharray="{{ $circumferenceLg }}" 
                                        stroke-dashoffset="{{ $strokeDashoffsetLg }}"
                                        stroke-linecap="round"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-bold text-slate-900">{{ $progress }}%</span>
                                <span class="text-xs font-medium text-slate-500">Complete</span>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 space-y-4 w-full">
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                <span class="text-sm font-medium text-slate-700">Email Verification</span>
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $emailVerified ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-yellow-50 text-yellow-800 ring-yellow-600/20' }}">
                                    {{ $emailVerified ? 'Verified' : 'Pending' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                <span class="text-sm font-medium text-slate-700">Two-Factor Auth</span>
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $twofaEnabled ? 'bg-primary-50 text-primary-700 ring-primary-700/10' : 'bg-slate-100 text-slate-600 ring-slate-500/10' }}">
                                    {{ $twofaEnabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>

                            @if($progress < 100)
                                <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
                                    <div class="flex">
                                        <div class="shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Complete your profile</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Unlock all features by verifying your email and adding a profile picture.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-6">
                        <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        Quick Actions
                    </h2>
                    
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium text-slate-700 rounded-md hover:text-slate-900 hover:bg-slate-50 transition-colors">
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Profile
                            </a>
                        </li>
                        <li>
                            <a href="#" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium text-slate-700 rounded-md hover:text-slate-900 hover:bg-slate-50 transition-colors">
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Change Password
                            </a>
                        </li>
                    </ul>

                     <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-white px-2 text-sm text-slate-500">Security</span>
                        </div>
                    </div>
                     
                     @if ($twofaEnabled)
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Disable 2FA
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors gap-2">
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
