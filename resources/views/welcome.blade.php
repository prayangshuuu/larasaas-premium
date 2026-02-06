<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
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
        // Check local storage for theme preference
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>
</head>
<body class=" antialiased font-sans">
    
    <!-- Navbar -->
    <div class="navbar bg-base-100/80 backdrop-blur-md sticky top-0 z-50 border-b border-base-200">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                </ul>
            </div>
            <a class="btn btn-ghost text-xl font-bold gap-2">
                <span class="text-primary">IELTS</span>BandBooster
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 font-medium">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#pricing">Pricing</a></li>
            </ul>
        </div>
        <div class="navbar-end gap-2">
            <!-- Theme Toggle -->
            <label class="swap swap-rotate btn btn-ghost btn-circle btn-sm">
                <!-- this hidden checkbox controls the state -->
                <input type="checkbox" class="theme-controller" value="dark" />
                
                <!-- sun icon -->
                <svg class="swap-off fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>
                
                <!-- moon icon -->
                <svg class="swap-on fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
            </label>

            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero min-h-[85vh] bg-base-100 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 z-0 opacity-30 dark:opacity-10 pointer-events-none">
            <div class="absolute top-[-10%] right-[-5%] w-[40rem] h-[40rem] bg-indigo-500/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[30rem] h-[30rem] bg-blue-500/20 rounded-full blur-3xl"></div>
        </div>

        <div class="hero-content text-center z-10 max-w-4xl px-4">
            <div class="">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-base-200 text-sm font-medium mb-8 border border-base-300">
                    <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                    Now with AI-Powered Speaking Tests
                </div>
                <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6">
                    Master IELTS with <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Intelligent Practice</span>
                </h1>
                <p class="py-6 text-xl text-base-content/70 max-w-2xl mx-auto leading-relaxed">
                    Join over 10,000 students achieving Band 7+ scores. Get personalized feedback, realistic mock tests, and AI-driven insights to boost your confidence.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mt-4">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg shadow-lg shadow-primary/30">Start Free Trial</a>
                    <a href="#features" class="btn btn-neutral btn-lg btn-outline">Explore Features</a>
                </div>
                <div class="mt-12 flex items-center justify-center gap-8 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                    <span class="text-xl font-bold">University of Oxford</span>
                    <span class="text-xl font-bold">Cambridge</span>
                    <span class="text-xl font-bold">British Council</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-base-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Complete Preparation Suite</h2>
                <p class="text-lg text-base-content/70">Everything you need to crush every module of the IELTS exam.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Reading -->
                <div class="card bg-base-100 shadow-xl border border-base-200 hover:border-primary/50 transition-all duration-300 group">
                    <div class="card-body items-center text-center">
                        <div class="w-14 h-14 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        </div>
                        <h3 class="card-title">Reading</h3>
                        <p class="text-base-content/70 text-sm">Authentic passages with instant analysis and vocabulary enhancement tools.</p>
                    </div>
                </div>

                <!-- Writing -->
                <div class="card bg-base-100 shadow-xl border border-base-200 hover:border-blue-500/50 transition-all duration-300 group">
                    <div class="card-body items-center text-center">
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </div>
                        <h3 class="card-title">Writing</h3>
                        <p class="text-base-content/70 text-sm">Get AI-powered grading on your essays with detailed grammar and coherence feedback.</p>
                    </div>
                </div>

                <!-- Listening -->
                <div class="card bg-base-100 shadow-xl border border-base-200 hover:border-green-500/50 transition-all duration-300 group">
                    <div class="card-body items-center text-center">
                        <div class="w-14 h-14 rounded-2xl bg-green-500/10 text-green-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
                        </div>
                        <h3 class="card-title">Listening</h3>
                        <p class="text-base-content/70 text-sm">Diverse accents and real-exam scenarios to train your ear for perfection.</p>
                    </div>
                </div>

                <!-- Speaking -->
                <div class="card bg-base-100 shadow-xl border border-base-200 hover:border-amber-500/50 transition-all duration-300 group">
                    <div class="card-body items-center text-center">
                        <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                        </div>
                        <h3 class="card-title">Speaking</h3>
                        <p class="text-base-content/70 text-sm">Real-time interview simulations with pronunciation and fluency scoring.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="py-24 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="stats stats-vertical lg:stats-horizontal shadow-xl bg-base-200/50 w-full max-w-4xl border border-base-200">
                <div class="stat place-items-center">
                    <div class="stat-title">Active Students</div>
                    <div class="stat-value text-primary">12K+</div>
                    <div class="stat-desc">From 50+ countries</div>
                </div>
                
                <div class="stat place-items-center">
                    <div class="stat-title">Average Score</div>
                    <div class="stat-value text-secondary">7.5</div>
                    <div class="stat-desc">Band improvement in 30 days</div>
                </div>
                
                <div class="stat place-items-center">
                    <div class="stat-title">Success Rate</div>
                    <div class="stat-value text-accent">96%</div>
                    <div class="stat-desc">Students hitting target</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-24 bg-gradient-to-br from-primary to-blue-700 text-primary-content relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl font-bold mb-6">Ready to hit your target band?</h2>
            <p class="text-xl mb-8 opacity-90">Start your free trial today and get access to our full suite of IELTS preparation tools.</p>
            <a href="{{ route('register') }}" class="btn btn-lg bg-base-100 text-primary border-none hover:bg-base-200">Start Free Trial</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer p-10 bg-base-300 text-base-content">
        <aside>
            <div class="text-2xl font-bold text-primary mb-2">IELTS Band Booster</div>
            <p class="max-w-xs opacity-70">
                Helping students achieve their dream IELTS scores through technology and personalized learning since 2024.
            </p>
        </aside> 
        <nav>
            <h6 class="footer-title">Services</h6> 
            <a class="link link-hover">Mock Tests</a>
            <a class="link link-hover">Writing Evaluation</a>
            <a class="link link-hover">Speaking Coach</a>
            <a class="link link-hover">Study Plans</a>
        </nav> 
        <nav>
            <h6 class="footer-title">Company</h6> 
            <a class="link link-hover">About us</a>
            <a class="link link-hover">Contact</a>
            <a class="link link-hover">Success Stories</a>
            <a class="link link-hover">Blog</a>
        </nav> 
        <nav>
            <h6 class="footer-title">Legal</h6> 
            <a class="link link-hover">Terms of use</a>
            <a class="link link-hover">Privacy policy</a>
            <a class="link link-hover">Cookie policy</a>
        </nav>
    </footer>
</body>
</html>
