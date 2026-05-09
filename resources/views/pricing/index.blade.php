<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing — {{ config('app.name', 'LaraSaaS Premium') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#030303] text-zinc-200 selection:bg-indigo-500/30 overflow-x-hidden">

    {{-- Navigation --}}
    <header class="fixed top-0 inset-x-0 z-50 p-6 bg-black/50 backdrop-blur-md border-b border-white/10 transition-all duration-300">
        <div class="flex justify-between items-center max-w-7xl mx-auto w-full">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                 <div class="p-2 rounded-lg bg-indigo-500/10 border border-indigo-500/20 group-hover:border-indigo-500/50 transition-colors">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-2xl font-black text-white tracking-tighter">Lara<span class="text-indigo-500">SaaS</span></span>
            </a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-6">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-zinc-400 hover:text-white transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-zinc-400 hover:text-white transition-colors">Sign In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white text-black rounded-full text-sm font-bold hover:bg-zinc-200 transition-all active:scale-95">Get Started</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    {{-- Pricing Content --}}
    <main class="pt-40 pb-32 relative">
        {{-- Background Glow --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none -z-10">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[60%] h-[60%] bg-indigo-600/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center mb-24">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-widest mb-6">
                    Pricing Plans
                </div>
                <h1 class="text-5xl md:text-7xl font-black tracking-tighter text-white mb-8">Scale your business <br /> without the overhead.</h1>
                <p class="text-xl text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    Transparent pricing designed to grow with you. Choose the plan that fits your current needs and upgrade whenever you're ready.
                </p>
            </div>

            @if($plans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($plans as $plan)
                        <x-plan-card :plan="$plan" />
                    @endforeach
                </div>
            @else
                <div class="text-center py-24 bg-zinc-900/50 border border-white/5 rounded-[40px]">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-zinc-800 border border-white/10 mb-6 shadow-2xl">
                        <svg class="h-10 w-10 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tighter">No plans available</h3>
                    <p class="mt-2 text-zinc-500 font-medium">We're currently updating our subscription tiers. Check back soon!</p>
                </div>
            @endif

            <div class="mt-20 p-10 bg-zinc-900/50 border border-white/5 rounded-[40px] flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h4 class="text-2xl font-black text-white tracking-tighter mb-2">Need a custom solution?</h4>
                    <p class="text-zinc-400 font-medium">We offer enterprise-grade plans for large teams and high-volume usage.</p>
                </div>
                <a href="{{ route('support.create') }}" class="px-8 py-4 bg-zinc-800 text-white rounded-2xl font-bold hover:bg-zinc-700 transition-all active:scale-95 border border-white/10">
                    Contact Enterprise
                </a>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-black py-20 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6 text-center">
             <div class="flex items-center justify-center gap-2 mb-8">
                <div class="p-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-xl font-black text-white tracking-tighter">Lara<span class="text-indigo-500">SaaS</span></span>
            </div>
            <p class="text-zinc-500 text-sm font-medium">&copy; 2026 LaraSaaS Premium. Built with ❤️ for developers.</p>
        </div>
    </footer>

</body>
</html>
