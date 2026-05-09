<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LaraSaaS Premium') }}</title>

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

    {{-- Glassmorphism Navigation --}}
    <header x-data="{ scrolled: false }" 
            @scroll.window="scrolled = window.pageYOffset > 20"
            class="fixed top-0 inset-x-0 z-50 transition-all duration-500 border-b"
            :class="scrolled ? 'p-4 bg-black/60 backdrop-blur-xl border-white/10 shadow-2xl' : 'p-6 bg-transparent border-transparent'">
        <div class="flex justify-between items-center max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-3 group cursor-pointer">
                 <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative p-2 rounded-lg bg-black border border-white/10 transition-colors group-hover:border-white/20">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                </div>
                <span class="text-2xl font-black tracking-tighter text-white">Lara<span class="text-indigo-500">SaaS</span></span>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-400">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#solutions" class="hover:text-white transition-colors">Solutions</a>
                <a href="#pricing" class="hover:text-white transition-colors">Pricing</a>
                <a href="#faq" class="hover:text-white transition-colors">Resources</a>
            </nav>

            @if (Route::has('login'))
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-full bg-white text-black text-sm font-bold hover:bg-zinc-200 transition-all active:scale-95">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">Sign In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="relative group">
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full blur opacity-50 group-hover:opacity-100 transition duration-200"></div>
                                <button class="relative px-6 py-2.5 bg-black rounded-full leading-none flex items-center divide-x divide-gray-600">
                                    <span class="text-white text-sm font-bold">Get Started</span>
                                </button>
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </header>

    {{-- Hero Section --}}
    <main>
        <section class="relative min-h-screen flex flex-col items-center justify-center pt-20 px-4 overflow-hidden">
            {{-- Animated Background Elements --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none -z-10">
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/20 rounded-full blur-[120px] animate-pulse"></div>
                <div class="absolute bottom-[10%] right-[-10%] w-[30%] h-[30%] bg-purple-600/10 rounded-full blur-[120px]"></div>
            </div>

            <x-ui.background-grid class="absolute inset-0 -z-20 opacity-20" />
            <x-ui.spotlight className="-top-40 left-0 md:left-60 md:-top-20" fill="white" />

            <div class="relative z-10 max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold mb-8 animate-bounce">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    The Ultimate SaaS Starter Kit
                </div>
                
                <h1 class="text-6xl md:text-8xl font-black tracking-tighter text-white leading-[0.9] mb-8">
                    Build Faster. <br /> 
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400">Scale Smarter.</span>
                </h1>
                
                <p class="mt-4 text-lg md:text-xl text-zinc-400 max-w-2xl mx-auto mb-12 leading-relaxed">
                    The most powerful Laravel SaaS Kit ever built. Featuring Stripe, Support, Admin Panel, API Docs, and a stunning UI out of the box.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <x-ui.button-shimmer href="{{ route('register') }}" class="w-full sm:w-auto h-14 px-10 text-lg">
                        Deploy Your SaaS
                    </x-ui.button-shimmer>

                    <a href="#features" class="group flex items-center gap-2 text-sm font-bold text-zinc-400 hover:text-white transition-colors">
                        Explore Modules
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </a>
                </div>

                {{-- Social Proof / Logos --}}
                <div class="mt-24 pt-10 border-t border-white/5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 mb-8">Trusted by developers at</p>
                    <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-30 grayscale hover:grayscale-0 transition-all duration-500">
                        <span class="text-xl font-bold text-white">Stripe</span>
                        <span class="text-xl font-bold text-white">Sentry</span>
                        <span class="text-xl font-bold text-white">DigitalOcean</span>
                        <span class="text-xl font-bold text-white">LemonSqueezy</span>
                        <span class="text-xl font-bold text-white">Vercel</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Features Grid --}}
        <section id="features" class="py-32 bg-[#050505] relative border-y border-white/5">
            <div class="max-w-7xl mx-auto px-6">
                <div class="mb-20">
                    <h2 class="text-4xl md:text-5xl font-black text-white tracking-tighter mb-4">Powerful Core Features</h2>
                    <p class="text-zinc-400 max-w-xl">Everything you need to launch a production-ready SaaS in minutes, not months.</p>
                </div>

                <x-ui.bento-grid>
                    <x-ui.bento-grid-item
                        title="Stripe Subscriptions"
                        description="Complete billing engine with plans, coupons, and automated invoicing."
                        className="md:col-span-2 group"
                    >
                         <x-slot:header>
                             <div class="flex flex-1 w-full h-full min-h-[10rem] rounded-xl bg-gradient-to-br from-indigo-950/50 to-black border border-white/10 items-center justify-center relative overflow-hidden group-hover:border-indigo-500/50 transition-colors">
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-indigo-500/10 via-transparent to-transparent"></div>
                                <div class="p-6 bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-white/10 shadow-2xl transform group-hover:scale-105 transition-transform">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-8 h-8 rounded bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold">$</div>
                                        <div class="h-2 w-20 bg-white/10 rounded"></div>
                                    </div>
                                    <div class="h-2 w-32 bg-white/5 rounded mb-2"></div>
                                    <div class="h-2 w-24 bg-white/5 rounded"></div>
                                </div>
                             </div>
                         </x-slot:header>
                    </x-ui.bento-grid-item>

                    <x-ui.bento-grid-item
                        title="Advanced Admin"
                        description="Real-time analytics, user management, and system settings."
                        className="md:col-span-1 group"
                    >
                        <x-slot:header>
                            <div class="flex flex-1 w-full h-full min-h-[10rem] rounded-xl bg-gradient-to-br from-purple-950/50 to-black border border-white/10 p-4 group-hover:border-purple-500/50 transition-colors">
                                <div class="w-full h-full rounded-lg border border-white/5 bg-zinc-900/50 p-3">
                                    <div class="flex gap-1 mb-4">
                                        <div class="w-2 h-2 rounded-full bg-red-500/50"></div>
                                        <div class="w-2 h-2 rounded-full bg-amber-500/50"></div>
                                        <div class="w-2 h-2 rounded-full bg-emerald-500/50"></div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="h-1 w-full bg-white/10 rounded"></div>
                                        <div class="h-1 w-2/3 bg-white/10 rounded"></div>
                                        <div class="h-1 w-3/4 bg-white/10 rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </x-slot:header>
                    </x-ui.bento-grid-item>

                    <x-ui.bento-grid-item
                        title="Helpdesk System"
                        description="Native support ticket system with auto-replies and file attachments."
                        className="md:col-span-1 group"
                    >
                        <x-slot:header>
                             <div class="flex flex-1 w-full h-full min-h-[10rem] rounded-xl bg-gradient-to-br from-emerald-950/50 to-black border border-white/10 items-center justify-center group-hover:border-emerald-500/50 transition-colors">
                                <svg class="w-12 h-12 text-emerald-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                             </div>
                        </x-slot:header>
                    </x-ui.bento-grid-item>

                    <x-ui.bento-grid-item
                        title="API Infrastructure"
                        description="Interactive API docs, Sanctum auth, and personal access tokens."
                        className="md:col-span-2 group"
                    >
                        <x-slot:header>
                             <div class="flex flex-1 w-full h-full min-h-[10rem] rounded-xl bg-zinc-900/50 border border-white/10 p-4 font-mono text-[10px] text-zinc-500 group-hover:border-pink-500/50 transition-colors relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-2 bg-pink-500/10 text-pink-400 border-l border-b border-pink-500/20">JSON</div>
                                <code>{<br />&nbsp;&nbsp;"status": "success",<br />&nbsp;&nbsp;"data": {<br />&nbsp;&nbsp;&nbsp;&nbsp;"user_id": "usr_98231",<br />&nbsp;&nbsp;&nbsp;&nbsp;"plan": "Pro"<br />&nbsp;&nbsp;}<br />}</code>
                             </div>
                        </x-slot:header>
                    </x-ui.bento-grid-item>
                </x-ui.bento-grid>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="py-32 relative overflow-hidden">
            <div class="absolute inset-0 bg-indigo-600/5 skew-y-3 origin-right"></div>
            <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-4xl md:text-6xl font-black text-white tracking-tighter mb-8 italic">Ready to ship your next big idea?</h2>
                <p class="text-xl text-zinc-400 mb-12">Join 1,000+ developers building with LaraSaaS Premium.</p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <x-ui.button-shimmer href="{{ route('register') }}" class="h-14 px-10 text-lg">
                        Get Started for Free
                    </x-ui.button-shimmer>
                    <div class="flex -space-x-3">
                        <img class="w-10 h-10 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?u=1" />
                        <img class="w-10 h-10 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?u=2" />
                        <img class="w-10 h-10 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?u=3" />
                        <div class="w-10 h-10 rounded-full bg-zinc-800 border-2 border-black flex items-center justify-center text-[10px] font-bold text-white">+1k</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Minimal Footer --}}
    <footer class="bg-black py-20 border-t border-white/5 relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="p-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <span class="text-xl font-black text-white">LaraSaaS</span>
                    </div>
                    <p class="text-sm text-zinc-500 max-w-xs">Building the future of SaaS development. One component at a time.</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white mb-6 uppercase tracking-widest">Product</h4>
                    <ul class="space-y-4 text-sm text-zinc-500">
                        <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Roadmap</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white mb-6 uppercase tracking-widest">Company</h4>
                    <ul class="space-y-4 text-sm text-zinc-500">
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-zinc-600">
                <p>&copy; 2026 LaraSaaS Premium. All rights reserved.</p>
                <div class="flex gap-8">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
