<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LaraSaaS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak]{display:none!important}
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#030303] text-slate-300 selection:bg-indigo-500/30">
    <div class="min-h-screen relative w-full flex flex-col">
        {{-- Navigation can be injected or included here --}}
        @include('layouts.navigation')

        {{-- Announcement Banner --}}
        <x-announcement-banner />

        {{-- Impersonation Banner --}}
        @if(session()->has('impersonator_id'))
            <div 
                class="fixed top-16 left-0 w-full z-40 bg-orange-600/95 backdrop-blur-md border-b border-white/10 shadow-lg transform transition-all duration-300 ease-out"
                x-data x-init="$el.classList.remove('-translate-y-full')"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between">
                    <div class="flex items-center gap-3 text-white">
                        <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium tracking-wide">
                            You are currently impersonating <span class="font-bold text-white border-b border-white/30 pb-0.5">{{ Auth::user()->name }}</span>
                        </span>
                    </div>

                    <form action="{{ route('admin.impersonate.stop') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-1.5 bg-white text-orange-600 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-orange-600 focus:ring-white transition-all shadow-sm">
                            Stop Impersonating
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ session()->has('impersonator_id') ? 'pt-32' : 'pt-24' }} pb-8 relative z-10">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        {{-- Ambient Background (optional, subtle grid) --}}
        <div class="fixed inset-0 z-0 pointer-events-none">
             <x-ui.background-grid class="opacity-40" />
        </div>
    </div>

    {{-- Global Command Palette --}}
    @auth
        <x-command-palette />
    @endauth
</body>
</html>
