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

    {{-- Hero Section with Spotlight and Grid --}}
    <x-ui.background-grid class="min-h-screen relative overflow-hidden flex flex-col items-center pt-32 pb-20 px-4">
        <x-ui.spotlight className="-top-40 left-0 md:left-60 md:-top-20" fill="white" />
        
        {{-- Navigation Placeholder (Absolute) --}}
        <header class="absolute top-0 inset-x-0 z-50 p-6 flex justify-between items-center max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-2">
                 <div class="p-1.5 rounded-lg border border-white/10 bg-black/50 backdrop-blur-md">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-xl font-bold">IELTS<span class="text-indigo-500">BandBooster</span></span>
            </div>
            
            @if (Route::has('login'))
                <nav class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-full transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.5)]">Get Started</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                     </x-slot:icon>
                </x-ui.bento-grid-item>
            </x-ui.bento-grid>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-black py-12 border-t border-white/10 relative z-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-zinc-500">&copy; {{ date('Y') }} IELTSBandBooster. Built with <span class="text-indigo-500 font-semibold">Aceternity UI</span> port.</p>
        </div>
    </footer>

</body>
</html>
