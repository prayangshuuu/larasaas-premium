<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IELTS Band Booster - Achieve Your Target Score</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased font-sans text-slate-600 bg-white">
    
    <!-- Navbar -->
    <nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="p-1.5 bg-primary-600 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">
                        IELTS<span class="font-medium text-slate-600">BandBooster</span>
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors">Features</a>
                    <a href="#how-it-works" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors">How It Works</a>
                    <a href="#pricing" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors">Pricing</a>
                    
                    <div class="h-6 w-px bg-slate-200"></div>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-900 hover:text-primary-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-900 hover:text-primary-600">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="text-slate-500 hover:text-slate-900 p-2">
                        <span class="sr-only">Open menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-collapse class="md:hidden border-t border-slate-200 bg-white">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="#features" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-primary-600 rounded-md">Features</a>
                <a href="#how-it-works" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-primary-600 rounded-md">How It Works</a>
                <a href="#pricing" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-primary-600 rounded-md">Pricing</a>
                
                <div class="border-t border-slate-100 my-2"></div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block px-3 py-2 text-base font-medium text-slate-900 hover:bg-slate-50 rounded-md">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-slate-900 hover:bg-slate-50 rounded-md">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block mt-2 text-center w-full rounded-lg bg-primary-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-primary-700">Get Started</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden pt-16 pb-24 lg:pt-32 lg:pb-40">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 text-xs font-medium text-primary-700 mb-8 border border-primary-100">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                </span>
                Now with AI-Powered Speaking Tests
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-slate-900 mb-6">
                Master IELTS with <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-purple-600">Intelligent Practice</span>
            </h1>
            
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                Join over 10,000 students achieving Band 7+ scores. Get personalized feedback, realistic mock tests, and AI-driven insights to boost your confidence.
            </p>
            
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="{{ route('register') }}" class="rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all hover:scale-105">Start Free Trial</a>
                <a href="#features" class="text-sm font-semibold leading-6 text-slate-900 hover:text-primary-600 flex items-center gap-1 group">
                    Explore Features <span aria-hidden="true" class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

            <div class="mt-16 flex items-center justify-center gap-8 opacity-50 grayscale transition-all duration-500 hover:grayscale-0 hover:opacity-100">
                <span class="text-xl font-bold text-slate-800">University of Oxford</span>
                <span class="text-xl font-bold text-slate-800">Cambridge</span>
                <span class="text-xl font-bold text-slate-800">British Council</span>
            </div>
        </div>

        <!-- Background Blobs -->
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-[30%] -right-[10%] w-[50rem] h-[50rem] bg-indigo-100/50 rounded-full blur-3xl opacity-60"></div>
            <div class="absolute bottom-[-20%] -left-[10%] w-[40rem] h-[40rem] bg-purple-100/50 rounded-full blur-3xl opacity-60"></div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <h2 class="text-base font-semibold leading-7 text-primary-600">Complete Suite</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Everything you need to crush every module.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Reading -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 group">
                    <div class="w-12 h-12 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mb-6 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Reading</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">Authentic passages with instant analysis and vocabulary enhancement tools.</p>
                </div>

                <!-- Writing -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 group">
                    <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Writing</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">Get AI-powered grading on your essays with detailed grammar feedback.</p>
                </div>

                <!-- Listening -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 group">
                    <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Listening</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">Diverse accents and real-exam scenarios to train your ear for perfection.</p>
                </div>

                <!-- Speaking -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 group">
                    <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-6 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Speaking</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">Real-time interview simulations with pronunciation and fluency scoring.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="py-24 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3 text-center">
                <div class="flex flex-col gap-y-2">
                    <dt class="text-base leading-7 text-slate-600">Active Students</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-slate-900">12K+</dd>
                </div>
                <div class="flex flex-col gap-y-2">
                    <dt class="text-base leading-7 text-slate-600">Average Score</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-slate-900">7.5</dd>
                </div>
                <div class="flex flex-col gap-y-2">
                    <dt class="text-base leading-7 text-slate-600">Success Rate</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-slate-900">96%</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative isolate overflow-hidden bg-slate-900 py-24 sm:py-32">
        <div class="absolute inset-0 -z-10 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 to-slate-900 opacity-90"></div>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Ready to hit your target band?</h2>
            <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-slate-300">
                Start your free trial today and get access to our full suite of IELTS preparation tools. No credit card required.
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="{{ route('register') }}" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Get started</a>
                <a href="#how-it-works" class="text-sm font-semibold leading-6 text-white">Learn more <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200">
        <div class="mx-auto max-w-7xl overflow-hidden py-12 px-6 sm:py-16 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-1.5 bg-primary-600 rounded-lg text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <span class="text-lg font-bold text-slate-900">IELTSBandBooster</span>
                    </div>
                    <p class="text-sm text-slate-500">Helping students achieve their dream IELTS scores through technology since 2024.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 tracking-wider uppercase mb-4">Product</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Mock Tests</a></li>
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Evaluation</a></li>
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Speaking Coach</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 tracking-wider uppercase mb-4">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">About</a></li>
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Blog</a></li>
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 tracking-wider uppercase mb-4">Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Privacy</a></li>
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Terms</a></li>
                        <li><a href="#" class="text-sm text-slate-600 hover:text-primary-600">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center border-t border-slate-200 pt-8">
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} IELTSBandBooster. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
