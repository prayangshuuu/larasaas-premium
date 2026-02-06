<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IELTSBandBooster') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-black text-slate-300 selection:bg-indigo-500/30" style="background-color: #000; color: #cbd5e1;">
    <div class="min-h-screen relative w-full flex flex-col">
        {{-- Impersonation Banner --}}
        @if(session()->has('impersonator_id'))
            <div class="bg-red-600 text-white px-4 py-2 text-center text-sm font-bold shadow-lg z-50 sticky top-0 flex items-center justify-center gap-4">
                <span>
                    ⚠️ You are currently impersonating <strong>{{ Auth::user()->name }}</strong>.
                </span>
                <form action="{{ route('admin.impersonate.stop') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="underline hover:text-red-100 focus:outline-none bg-white/20 px-3 py-1 rounded ml-2 text-xs uppercase tracking-wide transition">
                        Stop Impersonating
                    </button>
                </form>
            </div>
        @endif

        {{-- Navigation can be injected or included here --}}
        @include('layouts.navigation')

        {{-- Page Content --}}
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-8 relative z-10">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        {{-- Ambient Background (optional, subtle grid) --}}
        <div class="fixed inset-0 z-0 pointer-events-none">
             <x-ui.background-grid class="opacity-40" />
        </div>
    </div>
</body>
</html>
