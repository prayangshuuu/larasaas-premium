<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plans — {{ config('app.name', 'IELTSBandBooster') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-black text-white font-sans selection:bg-indigo-500/30">

    {{-- Navigation --}}
    <header class="fixed top-0 inset-x-0 z-50 p-6 border-b border-white/10 bg-black/50 backdrop-blur-md transition-all duration-300">
        <div class="flex justify-between items-center max-w-7xl mx-auto w-full">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="p-1.5 rounded-lg border border-white/10 bg-black/50 backdrop-blur-md">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-xl font-bold">IELTS<span class="text-indigo-500">BandBooster</span></span>
            </a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="w-32 h-10 flex items-center justify-center text-sm font-semibold text-zinc-400 hover:text-white transition-colors border border-transparent hover:border-white/10 rounded-full">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-32 h-10 flex items-center justify-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-full transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.5)]">Get Started</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    {{-- Pricing Content --}}
    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center mb-16">
                <p class="text-base font-semibold leading-7 text-indigo-400">Pricing</p>
                <h1 class="mt-2 text-4xl font-bold tracking-tight text-white sm:text-5xl">Simple, transparent pricing</h1>
                <p class="mt-6 text-lg leading-8 text-zinc-300">
                    Choose an affordable plan that's packed with the best features for your IELTS preparation journey.
                </p>
            </div>

            @if($plans->count() > 0)
                <div class="isolate mx-auto grid max-w-md grid-cols-1 gap-y-8 lg:mx-0 lg:max-w-none lg:grid-cols-{{ min($plans->count(), 3) }} lg:gap-x-8 xl:gap-x-12">
                    @foreach($plans as $plan)
                        <x-plan-card :plan="$plan" />
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-800 mb-4">
                        <svg class="h-8 w-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white">No plans available yet</h3>
                    <p class="mt-1 text-sm text-zinc-500">Check back soon for our subscription offerings.</p>
                </div>
            @endif

            <div class="mt-12 text-center space-y-2">
                <p class="text-sm text-zinc-400">
                    Have a coupon code? You can apply it during checkout!
                </p>
                <p class="text-sm text-zinc-500">
                    Secure payments powered by Stripe. You can cancel at any time.
                </p>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-black py-12 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-zinc-500">&copy; 2026 IELTSBandBooster, Build With <span class="text-red-500">&lt;3</span> By Prayangshu</p>
        </div>
    </footer>

</body>
</html>
