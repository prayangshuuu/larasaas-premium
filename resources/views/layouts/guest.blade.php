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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak]{display:none!important}
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased bg-black text-slate-300 selection:bg-indigo-500/30">
    {{-- Full screen grid background --}}
    <div class="relative min-h-screen w-full flex flex-col items-center justify-center overflow-y-auto bg-black">
        <x-ui.background-grid class="absolute inset-0 z-0 opacity-40 pointer-events-none" />
        
        {{-- Spotlight effect for dramatic entrance --}}
        <x-ui.spotlight className="-top-40 left-0 md:left-60 md:-top-20 z-10" />

        <div class="relative z-20 w-full max-w-md px-6 py-12">
            {{-- Logo / Branding --}}
            <div class="flex justify-center mb-10">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="p-2 rounded-xl bg-zinc-900 border border-zinc-800 group-hover:border-indigo-500/50 transition-all active:scale-95 shadow-2xl">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <span class="text-3xl font-black text-white tracking-tighter">
                        Lara<span class="text-indigo-500">SaaS</span>
                    </span>
                </a>
            </div>

            {{-- Form Container --}}
            <div class="bg-black/50 backdrop-blur-xl border border-white/10 p-8 rounded-2xl shadow-2xl relative">
                {{-- Glow effect behind card --}}
                <div class="absolute inset-0 bg-indigo-500/5 blur-3xl rounded-full pointer-events-none -z-10"></div>
                
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-zinc-500">
                &copy; {{ date('Y') }} LaraSaaS. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
