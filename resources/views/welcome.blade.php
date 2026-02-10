<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'IELTSBandBooster') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-black text-white font-sans selection:bg-indigo-500/30">

    {{-- Fixed Navigation --}}
    <header class="fixed top-0 inset-x-0 z-50 p-6 border-b border-white/10 bg-black/50 backdrop-blur-md transition-all duration-300">
        <div class="flex justify-between items-center max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-2">
                 <div class="p-1.5 rounded-lg border border-white/10 bg-black/50 backdrop-blur-md">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-xl font-bold">IELTS<span class="text-indigo-500">BandBooster</span></span>
            </div>

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

    {{-- Hero Section with Spotlight and Grid --}}
    <x-ui.background-grid class="min-h-screen relative overflow-hidden flex flex-col items-center pt-32 pb-20 px-4">
        <x-ui.spotlight className="-top-40 left-0 md:left-60 md:-top-20" fill="white" />

        <div class="relative z-10 max-w-4xl mx-auto text-center mt-12">
            <h1 class="text-5xl md:text-7xl font-bold bg-clip-text text-transparent bg-gradient-to-b from-neutral-50 to-neutral-400 bg-opacity-50 tracking-tight mb-8">
                Master IELTS with <br /> AI Precision.
            </h1>
            <p class="mt-4 font-normal text-base text-neutral-300 max-w-lg mx-auto mb-10">
                Elevate your band score using advanced AI-driven analysis, real-time feedback, and personalized study plans tailored to your weaknesses.
            </p>

            <div class="flex items-center justify-center gap-4">
                <x-ui.button-shimmer href="{{ route('register') }}">
                    Start Your Journey
                </x-ui.button-shimmer>

                <a href="#features" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">
                    Learn more <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </x-ui.background-grid>

    {{-- Features Section (Bento Grid) --}}
    <section id="features" class="py-24 bg-black relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-16 text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl text-white">Why Choose Us?</h2>
                <p class="mt-4 text-zinc-400">Everything you need to achieve your target band score.</p>
            </div>

            <x-ui.bento-grid>
                <x-ui.bento-grid-item
                    title="Real-time Writing Analysis"
                    description="Get instant feedback on your essays with detailed grammar and vocabulary corrections."
                    className="md:col-span-2"
                >
                     <x-slot:header>
                         <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-gradient-to-br from-indigo-900/50 to-purple-900/50 border border-indigo-500/20 items-center justify-center relative overflow-hidden group">
                            {{-- Content mock --}}
                            <div class="absolute inset-0 bg-dot-white/[0.1]"></div>
                            <div class="p-4 bg-black/50 backdrop-blur-sm rounded-lg border border-white/10 max-w-[80%] mx-auto transform group-hover:-translate-y-2 transition-transform duration-300">
                                <div class="h-2 w-2/3 bg-zinc-700 rounded mb-2"></div>
                                <div class="h-2 w-full bg-zinc-700 rounded mb-2"></div>
                                <div class="h-2 w-5/6 bg-indigo-500/50 rounded"></div>
                            </div>
                         </div>
                     </x-slot:header>
                     <x-slot:icon>
                        <svg class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                     </x-slot:icon>
                </x-ui.bento-grid-item>

                <x-ui.bento-grid-item
                    title="Speaking Mock Tests"
                    description="Practice with AI-simulated examiners covering the latest IELTS topics."
                    className="md:col-span-1"
                >
                    <x-slot:header>
                        <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-gradient-to-br from-emerald-900/50 to-cyan-900/50 border border-emerald-500/20 relative overflow-hidden">
                             <div class="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-black to-transparent"></div>
                             {{-- Audio wave mock --}}
                             <div class="flex justify-center items-center h-full gap-1">
                                <div class="w-1 h-8 bg-emerald-500 animate-pulse"></div>
                                <div class="w-1 h-12 bg-emerald-500 animate-[pulse_1.5s_infinite]"></div>
                                <div class="w-1 h-6 bg-emerald-500 animate-pulse"></div>
                                <div class="w-1 h-10 bg-emerald-500 animate-[pulse_0.8s_infinite]"></div>
                             </div>
                        </div>
                    </x-slot:header>
                    <x-slot:icon>
                        <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" /></svg>
                     </x-slot:icon>
                </x-ui.bento-grid-item>

                <x-ui.bento-grid-item
                    title="Vocabulary Builder"
                    description="Expand your lexical resource with context-aware suggestions."
                    className="md:col-span-1"
                >
                    <x-slot:header>
                         <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-zinc-900/50 border border-zinc-800 flex items-center justify-center">
                            <span class="text-4xl font-serif italic text-zinc-600">Aa</span>
                         </div>
                    </x-slot:header>
                    <x-slot:icon>
                        <svg class="w-6 h-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    </x-slot:icon>
                </x-ui.bento-grid-item>

                <x-ui.bento-grid-item
                    title="Progress Tracking"
                    description="Visualize your improvement over time with detailed analytics."
                    className="md:col-span-2"
                >
                    <x-slot:header>
                         <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-gradient-to-br from-orange-900/40 to-red-900/40 border border-orange-500/20 relative items-end px-4 pb-4">
                            {{-- Chart mock --}}
                            <div class="flex items-end gap-2 w-full h-2/3">
                                <div class="w-full bg-orange-500/20 rounded-t h-[40%]"></div>
                                <div class="w-full bg-orange-500/40 rounded-t h-[60%]"></div>
                                <div class="w-full bg-orange-500/60 rounded-t h-[50%]"></div>
                                <div class="w-full bg-orange-500/80 rounded-t h-[80%]"></div>
                                <div class="w-full bg-orange-500 rounded-t h-[95%]"></div>
                            </div>
                         </div>
                    </x-slot:header>
                     <x-slot:icon>
                        <svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                     </x-slot:icon>
                </x-ui.bento-grid-item>
            </x-ui.bento-grid>
        </div>
    </section>

    {{-- Featured Plans Section --}}
    @if(isset($featuredPlans) && $featuredPlans->count() > 0)
        <section id="pricing" class="py-24 bg-zinc-950 relative z-20 border-t border-white/5">
            <div class="max-w-7xl mx-auto px-6">
                <div class="mb-16 text-center">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl text-white">Pricing Plans</h2>
                    <p class="mt-4 text-zinc-400">Choose the perfect plan for your IELTS preparation journey.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredPlans as $plan)
                        <x-plan-card :plan="$plan" />
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('pricing.index') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold text-sm">
                        View all plans <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Footer --}}
    <footer class="bg-black py-12 border-t border-white/10 relative z-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-zinc-500">&copy; 2026 IELTSBandBooster, Build With <span class="text-red-500">&lt;3</span> By Prayangshu</p>
        </div>
    </footer>

</body>
</html>
