{{-- resources/views/guest.blade.php --}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IELTSBandBooster') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>

    {{-- LOGIN door animation (compact, no icon) --}}
    <style>
        .loginButton{
            --figure-duration:100ms; --transform-figure:none;
            --walking-duration:100ms;
            --transform-arm1:none; --transform-wrist1:none;
            --transform-arm2:none; --transform-wrist2:none;
            --transform-leg1:none; --transform-calf1:none;
            --transform-leg2:none; --transform-calf2:none;

            position:relative; display:inline-block;
            height:28px; width:56px;         /* compact canvas */
            padding-left:4px;
            background:transparent; border:0; outline:0;
            perspective:100px; text-align:left; vertical-align:middle;
            -webkit-tap-highlight-color:transparent;
        }
        .loginButton:focus-visible{outline:2px solid oklch(var(--color-primary)); outline-offset:4px; border-radius:10px;}
        .loginButton::before{content:''; position:absolute; inset:0; border-radius:10px; z-index:2;}

        .loginButton .button-text{position:relative; z-index:10; font-weight:600; font-size:14px;}
        .loginButton--light .button-text{ color: oklch(var(--color-base-content)); }
        .loginButton--dark  .button-text{ color: oklch(var(--color-neutral-content)); }

        .loginButton svg{position:absolute; display:block;}
        .loginButton .figure{
            bottom:2px; right:6px; width:18px; z-index:4;
            fill: oklch(var(--color-primary));
            transform:var(--transform-figure);
            transition:transform calc(var(--figure-duration)*1ms) cubic-bezier(.2,.1,.8,.9);
        }
        .loginButton .door, .loginButton .doorway{ bottom:2px; right:4px; width:20px;}
        .loginButton--light .door, .loginButton--light .doorway{ fill: oklch(var(--color-base-content)); }
        .loginButton--dark  .door, .loginButton--dark  .doorway{  fill: oklch(var(--color-neutral-content)); }
        .loginButton .door{ transform:rotateY(20deg); transform-origin:100% 50%; transform-style:preserve-3d; transition:transform 200ms ease; z-index:5; }
        .loginButton .door path{ fill: oklch(var(--color-primary)); stroke: oklch(var(--color-primary)); stroke-width:4; }
        .loginButton .doorway{ z-index:3; }
        .loginButton .bang{ opacity:0; }

        .loginButton .arm1,.loginButton .wrist1,.loginButton .arm2,.loginButton .wrist2,
        .loginButton .leg1,.loginButton .calf1,.loginButton .leg2,.loginButton .calf2{
            transition:transform calc(var(--walking-duration)*1ms) ease-in-out;
        }
        .loginButton .arm1{transform:var(--transform-arm1); transform-origin:52% 45%;}
        .loginButton .wrist1{transform:var(--transform-wrist1); transform-origin:59% 55%;}
        .loginButton .arm2{transform:var(--transform-arm2); transform-origin:47% 43%;}
        .loginButton .wrist2{transform:var(--transform-wrist2); transform-origin:35% 47%;}
        .loginButton .leg1{transform:var(--transform-leg1); transform-origin:47% 64.5%;}
        .loginButton .calf1{transform:var(--transform-calf1); transform-origin:55.5% 71.5%;}
        .loginButton .leg2{transform:var(--transform-leg2); transform-origin:43% 63%;}
        .loginButton .calf2{transform:var(--transform-calf2); transform-origin:41.5% 73%;}

        .loginButton:hover .door{ transform:rotateY(22deg); }
        .loginButton.clicked .door{ transform:rotateY(32deg); }
    </style>
</head>
<body class="bg-base-100 font-sans text-base-content">

{{-- NAVBAR --}}
<nav class="bg-base-100 border-b border-base-300 shadow-md"
     x-data="{ mobileOpen:false, dark: (localStorage.getItem('theme') === 'dim') }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between">
            {{-- LEFT: Brand --}}
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                <x-application-logo class="block h-10 w-auto text-primary" />
                <span class="hidden sm:inline font-semibold">IELTSBandBooster</span>
            </a>

            {{-- RIGHT (desktop) --}}
            <div class="hidden sm:flex items-center gap-3">
                {{-- THEME TOGGLE (Moon = dim, Sun = nord) --}}
                <button type="button"
                        class="group inline-flex items-center gap-3 rounded-xl px-3 py-1.5 hover:bg-base-200 transition"
                        @click="dark = !dark; window.toggleTheme(dark)"
                        aria-label="Toggle theme">
                    <div class="relative w-14 h-7 flex items-center">
                        <div class="w-full h-full rounded-full transition-colors bg-base-300"></div>
                        <div class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full shadow flex items-center justify-center transition-all duration-300 group-active:scale-95"
                             :class="{
                               'translate-x-7 bg-base-100': dark,   /* dim → light knob w/ moon */
                               'translate-x-0 bg-neutral'  : !dark  /* nord → dark knob w/ sun  */
                             }">
                            <!-- Sun -->
                            <svg class="w-3.5 h-3.5 text-warning transition-opacity duration-200"
                                 :class="{ 'opacity-0' : dark, 'opacity-100' : !dark }"
                                 xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 3v2m0 14v2m7.071-11.071l-1.414 1.414M6.343 17.657l-1.414 1.414M21 12h-2M5 12H3m14.657 6.657l-1.414-1.414M7.757 7.757L6.343 6.343M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                            </svg>
                            <!-- Moon -->
                            <svg class="w-3.5 h-3.5 text-indigo-500 absolute transition-opacity duration-200"
                                 :class="{ 'opacity-100' : dark, 'opacity-0' : !dark }"
                                 xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
                            </svg>
                        </div>
                    </div>
                </button>

                {{-- LOGIN (no icon). LIGHT → dark bg; DARK → white bg --}}
                <button
                    @click="animateLogin($refs.loginDesk)"
                    class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition shadow-sm border
                           bg-neutral text-neutral-content hover:opacity-95
                           dark:bg-neutral-content dark:text-neutral dark:border-base-300 dark:hover:opacity-95"
                    aria-label="Login">
                    <span>Login</span>
                    <span x-ref="loginDesk"
                          :class="dark ? 'loginButton loginButton--light' : 'loginButton loginButton--dark'">
                        <svg class="doorway" viewBox="0 0 100 100">
                            <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                            <path class="bang" d="M40.5 43.7L26.6 31.4l-2.5 6.7zM41.9 50.4l-19.5-4-1.4 6.3zM40 57.4l-17.7 3.9 3.9 5.7z" />
                        </svg>
                        <svg class="figure" viewBox="0 0 100 100">
                            <circle cx="52.1" cy="32.4" r="6.4" />
                            <path d="M50.7 62.8c-1.2 2.5-3.6 5-7.2 4-3.2-.9-4.9-3.5-4-7.8.7-3.4 3.1-13.8 4.1-15.8 1.7-3.4 1.6-4.6 7-3.7 4.3.7 4.6 2.5 4.3 5.4-.4 3.7-2.8 15.1-4.2 17.9z" />
                            <g class="arm1">
                                <path d="M55.5 56.5l-6-9.5c-1-1.5-.6-3.5.9-4.4 1.5-1 3.7-1.1 4.6.4l6.1 10c1 1.5.3 3.5-1.1 4.4-1.5.9-3.5.5-4.5-.9z" />
                                <path class="wrist1" d="M69.4 59.9L58.1 58c-1.7-.3-2.9-1.9-2.6-3.7.3-1.7 1.9-2.9 3.7-2.6l11.4 1.9c1.7.3 2.9 1.9 2.6 3.7-.4 1.7-2 2.9-3.8 2.6z" />
                            </g>
                            <g class="arm2">
                                <path d="M34.2 43.6L45 40.3c1.7-.6 3.5.3 4 2 .6 1.7-.3 4-2 4.5l-10.8 2.8c-1.7.6-3.5-.3-4-2-.6-1.6.3-3.4 2-4z" />
                                <path class="wrist2" d="M27.1 56.2L32 45.7c.7-1.6 2.6-2.3 4.2-1.6 1.6.7 2.3 2.6 1.6 4.2L33 58.8c-.7 1.6-2.6 2.3-4.2 1.6-1.7-.7-2.4-2.6-1.7-4.2z" />
                            </g>
                            <g class="leg1">
                                <path d="M52.1 73.2s-7-5.7-7.9-6.5c-.9-.9-1.2-3.5-.1-4.9 1.1-1.4 3.8-1.9 5.2-.9l7.9 7c1.4 1.1 1.7 3.5.7 4.9-1.1 1.4-4.4 1.5-5.8.4z" />
                                <path class="calf1" d="M52.6 84.4l-1-12.8c-.1-1.9 1.5-3.6 3.5-3.7 2-.1 3.7 1.4 3.8 3.4l1 12.8c.1 1.9-1.5 3.6-3.5 3.7-2 0-3.7-1.5-3.8-3.4z" />
                            </g>
                            <g class="leg2">
                                <path d="M37.8 72.7s1.3-10.2 1.6-11.4 2.4-2.8 4.1-2.6c1.7.2 3.6 2.3 3.4 4l-1.8 11.1c-.2 1.7-1.7 3.3-3.4 3.1-1.8-.2-4.1-2.4-3.9-4.2z" />
                                <path class="calf2" d="M29.5 82.3l9.6-10.9c1.3-1.4 3.6-1.5 5.1-.1 1.5 1.4.4 4.9-.9 6.3l-8.5 9.6c-1.3 1.4-3.6 1.5-5.1.1-1.4-1.3-1.5-3.5-.2-5z" />
                            </g>
                        </svg>
                        <svg class="door" viewBox="0 0 100 100">
                            <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                            <circle cx="66" cy="50" r="3.7" />
                        </svg>
                    </span>
                </button>
            </div>

            {{-- MOBILE burger --}}
            <button class="sm:hidden p-2 rounded-xl hover:bg-base-200 transition"
                    @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Toggle menu">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen }" class="inline-flex"
                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen }" class="hidden"
                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- MOBILE PANEL --}}
    <div x-cloak x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-3"
         class="sm:hidden bg-base-100 border-t border-base-300 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-3 space-y-3">
            {{-- THEME TOGGLE (mobile) --}}
            <button type="button"
                    class="w-full inline-flex items-center justify-center rounded-xl px-3 py-2 hover:bg-base-200 transition"
                    @click="dark = !dark; window.toggleTheme(dark)"
                    aria-label="Toggle theme (mobile)">
                <div class="relative w-14 h-7 flex items-center">
                    <div class="w-full h-full rounded-full transition-colors bg-base-300"></div>
                    <div class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full shadow flex items-center justify-center transition-all duration-300"
                         :class="{ 'translate-x-7 bg-base-100': dark, 'translate-x-0 bg-neutral': !dark }">
                        <!-- Sun -->
                        <svg class="w-3.5 h-3.5 text-warning transition-opacity duration-200"
                             :class="{ 'opacity-0' : dark, 'opacity-100' : !dark }"
                             xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v2m0 14v2m7.071-11.071l-1.414 1.414M6.343 17.657l-1.414 1.414M21 12h-2M5 12H3m14.657 6.657l-1.414-1.414M7.757 7.757L6.343 6.343M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                        </svg>
                        <!-- Moon -->
                        <svg class="w-3.5 h-3.5 text-indigo-500 absolute transition-opacity duration-200"
                             :class="{ 'opacity-100' : dark, 'opacity-0' : !dark }"
                             xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
                        </svg>
                    </div>
                </div>
            </button>

            {{-- LOGIN (mobile) – LIGHT → dark bg; DARK → white bg --}}
            <button
                @click="animateLogin($refs.loginMob)"
                class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition shadow-sm border w-full justify-start
                       bg-neutral text-neutral-content hover:opacity-95
                       dark:bg-neutral-content dark:text-neutral dark:border-base-300 dark:hover:opacity-95"
                aria-label="Login animated button (mobile)">
                <span>Login</span>
                <span x-ref="loginMob"
                      :class="dark ? 'loginButton loginButton--light' : 'loginButton loginButton--dark'">
                    <svg class="doorway" viewBox="0 0 100 100">
                        <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                        <path class="bang" d="M40.5 43.7L26.6 31.4l-2.5 6.7zM41.9 50.4l-19.5-4-1.4 6.3zM40 57.4l-17.7 3.9 3.9 5.7z" />
                    </svg>
                    <svg class="figure" viewBox="0 0 100 100">
                        <circle cx="52.1" cy="32.4" r="6.4" />
                        <path d="M50.7 62.8c-1.2 2.5-3.6 5-7.2 4-3.2-.9-4.9-3.5-4-7.8.7-3.4 3.1-13.8 4.1-15.8 1.7-3.4 1.6-4.6 7-3.7 4.3.7 4.6 2.5 4.3 5.4-.4 3.7-2.8 15.1-4.2 17.9z" />
                        <g class="arm1">
                            <path d="M55.5 56.5l-6-9.5c-1-1.5-.6-3.5.9-4.4 1.5-1 3.7-1.1 4.6.4l6.1 10c1 1.5.3 3.5-1.1 4.4-1.5.9-3.5.5-4.5-.9z" />
                            <path class="wrist1" d="M69.4 59.9L58.1 58c-1.7-.3-2.9-1.9-2.6-3.7.3-1.7 1.9-2.9 3.7-2.6l11.4 1.9c1.7.3 2.9 1.9 2.6 3.7-.4 1.7-2 2.9-3.8 2.6z" />
                        </g>
                        <g class="arm2">
                            <path d="M34.2 43.6L45 40.3c1.7-.6 3.5.3 4 2 .6 1.7-.3 4-2 4.5l-10.8 2.8c-1.7.6-3.5-.3-4-2-.6-1.6.3-3.4 2-4z" />
                            <path class="wrist2" d="M27.1 56.2L32 45.7c.7-1.6 2.6-2.3 4.2-1.6 1.6.7 2.3 2.6 1.6 4.2L33 58.8c-.7 1.6-2.6 2.3-4.2 1.6-1.7-.7-2.4-2.6-1.7-4.2z" />
                        </g>
                        <g class="leg1">
                            <path d="M52.1 73.2s-7-5.7-7.9-6.5c-.9-.9-1.2-3.5-.1-4.9 1.1-1.4 3.8-1.9 5.2-.9l7.9 7c1.4 1.1 1.7 3.5.7 4.9-1.1 1.4-4.4 1.5-5.8.4z" />
                            <path class="calf1" d="M52.6 84.4l-1-12.8c-.1-1.9 1.5-3.6 3.5-3.7 2-.1 3.7 1.4 3.8 3.4l1 12.8c.1 1.9-1.5 3.6-3.5 3.7-2 0-3.7-1.5-3.8-3.4z" />
                        </g>
                        <g class="leg2">
                            <path d="M37.8 72.7s1.3-10.2 1.6-11.4 2.4-2.8 4.1-2.6c1.7.2 3.6 2.3 3.4 4l-1.8 11.1c-.2 1.7-1.7 3.3-3.4 3.1-1.8-.2-4.1-2.4-3.9-4.2z" />
                            <path class="calf2" d="M29.5 82.3l9.6-10.9c1.3-1.4 3.6-1.5 5.1-.1 1.5 1.4.4 4.9-.9 6.3l-8.5 9.6c-1.3 1.4-3.6 1.5-5.1.1-1.4-1.3-1.5-3.5-.2-5z" />
                        </g>
                    </svg>
                    <svg class="door" viewBox="0 0 100 100">
                        <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                        <circle cx="66" cy="50" r="3.7" />
                    </svg>
                </span>
            </button>
        </div>
    </div>
</nav>

{{-- AUTH CARD AREA --}}
<main class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-10">
    <div class="card w-full max-w-md bg-base-100 shadow-xl rounded-2xl p-8">
        @if($errors->any())
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>

{{-- Theme + animation --}}
<script>
    // GLOBAL helpers (so Alpine in any partial can call them)
    window.setTheme = function(theme){
        try { localStorage.setItem("theme", theme); } catch(e) {}
        document.documentElement.setAttribute("data-theme", theme);
        // optional: if you use Tailwind `dark:` utilities anywhere, also keep this line:
        // document.documentElement.classList.toggle('dark', theme === 'dim');
    };
    window.toggleTheme = function(dark){
        window.setTheme(dark ? "dim" : "nord");
    };
    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem("theme") || "nord";
        window.setTheme(saved);
    });

    const loginStates = {
        default: {'--figure-duration':'100','--transform-figure':'none','--walking-duration':'100',
            '--transform-arm1':'none','--transform-wrist1':'none','--transform-arm2':'none','--transform-wrist2':'none',
            '--transform-leg1':'none','--transform-calf1':'none','--transform-leg2':'none','--transform-calf2':'none'},
        hover: {'--figure-duration':'100','--transform-figure':'translateX(1.2px)','--walking-duration':'100',
            '--transform-arm1':'rotate(-5deg)','--transform-wrist1':'rotate(-15deg)',
            '--transform-arm2':'rotate(5deg)','--transform-wrist2':'rotate(6deg)',
            '--transform-leg1':'rotate(-10deg)','--transform-calf1':'rotate(5deg)',
            '--transform-leg2':'rotate(20deg)','--transform-calf2':'rotate(-20deg)'},
        walking1: {'--figure-duration':'200','--transform-figure':'translateX(7px)','--walking-duration':'200',
            '--transform-arm1':'translateX(-4px) translateY(-2px) rotate(120deg)',
            '--transform-wrist1':'rotate(-5deg)','--transform-arm2':'translateX(4px) rotate(-110deg)',
            '--transform-wrist2':'rotate(-5deg)','--transform-leg1':'translateX(-3px) rotate(80deg)',
            '--transform-calf1':'rotate(-30deg)','--transform-leg2':'translateX(3px) rotate(-60deg)','--transform-calf2':'rotate(20deg)'},
        walking2: {'--figure-duration':'240','--transform-figure':'translateX(11px)','--walking-duration':'230',
            '--transform-arm1':'rotate(60deg)','--transform-wrist1':'rotate(-15deg)',
            '--transform-arm2':'rotate(-45deg)','--transform-wrist2':'rotate(6deg)',
            '--transform-leg1':'rotate(-5deg)','--transform-calf1':'rotate(10deg)',
            '--transform-leg2':'rotate(10deg)','--transform-calf2':'rotate(-20deg)'}
    };
    function applyState(el, key){ const s = loginStates[key]; if(!s) return; for(const k in s){ el.style.setProperty(k, s[k]); } }
    function animateLogin(canvasEl){
        if (!canvasEl || canvasEl.__busy) return;
        canvasEl.__busy = true;
        applyState(canvasEl,'hover');
        setTimeout(()=> {
            canvasEl.classList.add('clicked');
            applyState(canvasEl,'walking1');
            setTimeout(()=> {
                applyState(canvasEl,'walking2');
                setTimeout(()=> {
                    canvasEl.classList.remove('clicked');
                    applyState(canvasEl,'default');
                    window.location.href = @json(route('login'));
                    canvasEl.__busy = false;
                }, parseInt(loginStates.walking2['--figure-duration']) + 120);
            }, parseInt(loginStates.walking1['--figure-duration']) + 80);
        }, 80);
    }

    // Desktop hover feedback
    document.addEventListener('mouseover', e => {
        const b = e.target.closest('.loginButton'); if(!b || b.__busy) return; applyState(b,'hover');
    });
    document.addEventListener('mouseout', e => {
        const b = e.target.closest('.loginButton'); if(!b || b.__busy) return; applyState(b,'default');
    });
</script>
</body>
</html>
