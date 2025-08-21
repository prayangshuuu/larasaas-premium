{{-- resources/views/layouts/navigation.blade.php --}}
<style>[x-cloak]{display:none!important}</style>

<nav class="bg-base-100 border-b border-base-300 shadow-md"
     x-data="{ mobileOpen:false, dark: (localStorage.getItem('theme') === 'dim') }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between">
            {{-- LEFT: Logo + links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2">
                    <x-application-logo class="block h-10 w-auto text-primary" />
                    <span class="hidden sm:inline font-semibold text-base-content">IELTSBandBooster</span>
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-medium">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            {{-- RIGHT: Profile dropdown (desktop) --}}
            <div class="hidden sm:flex items-center gap-3">
                {{-- Profile + dropdown --}}
                <div class="relative"
                     x-data="{ open:false }"
                     @keydown.escape.window="open=false"
                     @click.outside="open=false">
                    {{-- Profile (links to profile) --}}
                    <a href="{{ route('profile.edit') }}"
                       class="inline-flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-base-200 transition">
                        <div class="avatar">
                            <div class="w-9 h-9 rounded-full ring ring-primary ring-offset-base-100 ring-offset-1">
                                <img src="{{ Auth::user()->profile_picture ? Storage::url(Auth::user()->profile_picture) : asset('images/default-avatar.png') }}"
                                     alt="{{ Auth::user()->name }}" />
                            </div>
                        </div>
                        <div class="hidden lg:flex flex-col leading-tight text-left">
                            <span class="text-sm font-semibold text-base-content truncate max-w-[12rem]">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-base-content/70 truncate max-w-[12rem]">{{ Auth::user()->email }}</span>
                        </div>
                    </a>

                    {{-- Caret toggles dropdown --}}
                    <button type="button"
                            @click.stop="open = !open"
                            :aria-expanded="open"
                            class="btn btn-ghost btn-sm px-2 rounded-lg hover:bg-base-200">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-cloak x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-3 w-80 z-50 bg-base-100 rounded-2xl shadow-xl border border-base-200 overflow-hidden">

                        {{-- THEME MODE (in dropdown) – exactly like guest.blade.php --}}
                        <div class="px-4 py-4 border-b border-base-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-base-content">{{ __('Theme mode') }}</span>
                                <button type="button"
                                        class="group inline-flex items-center gap-3 rounded-xl px-3 py-1.5 hover:bg-base-200 transition"
                                        @click="dark = !dark; window.toggleTheme(dark)"
                                        aria-label="Toggle theme (dropdown)">
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
                            </div>
                        </div>

                        {{-- LOGOUT (Light → dark button, Dark → white button like guest login) --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <div class="px-4 py-4">
                                <button type="button"
                                        @click="animateLogout($el)"
                                        class="inline-flex items-center justify-between w-full rounded-xl px-3 py-2 text-sm font-semibold transition shadow-sm border
                                               bg-neutral text-neutral-content hover:opacity-95
                                               dark:bg-neutral-content dark:text-neutral dark:border-base-300 dark:hover:opacity-95">
                                    <span>Log Out</span>

                                    {{-- compact door animation canvas on the right --}}
                                    <span class="logoutCanvas doorCanvas">
                                        <svg class="doorway" viewBox="0 0 100 100" aria-hidden="true">
                                            <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                                            <path class="bang" d="M40.5 43.7L26.6 31.4l-2.5 6.7zM41.9 50.4l-19.5-4-1.4 6.3zM40 57.4l-17.7 3.9 3.9 5.7z" />
                                        </svg>
                                        <svg class="figure" viewBox="0 0 100 100" aria-hidden="true">
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
                                        <svg class="door" viewBox="0 0 100 100" aria-hidden="true">
                                            <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                                            <circle cx="66" cy="50" r="3.7" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- MOBILE: hamburger + panel --}}
            <div class="flex sm:hidden items-center" x-data="{ open:false }">
                <button @click="open = !open" :aria-expanded="open"
                        class="btn btn-ghost p-2 rounded-xl hover:bg-base-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div x-cloak x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-3"
                     class="absolute top-16 left-0 w-full bg-base-100 border-t border-base-300 shadow-lg z-40">
                    <div class="p-4 space-y-3">
                        {{-- Profile header --}}
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-base-200 transition">
                            <div class="avatar">
                                <div class="w-9 h-9 rounded-full">
                                    <img src="{{ Auth::user()->profile_picture ? Storage::url(Auth::user()->profile_picture) : asset('images/default-avatar.png') }}"
                                         alt="{{ Auth::user()->name }}" />
                                </div>
                            </div>
                            <div class="min-w-0 text-left">
                                <p class="text-sm font-semibold truncate text-base-content">{{ Auth::user()->name }}</p>
                                <p class="text-xs truncate text-base-content/70">{{ Auth::user()->email }}</p>
                            </div>
                            <svg class="ml-auto w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <div class="divider my-1"></div>

                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-base-200 transition">
                            <svg class="w-5 h-5 shrink-0 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h14a1 1 0 001-1V10" />
                            </svg>
                            <span class="text-sm">Dashboard</span>
                        </a>

                        {{-- THEME TOGGLE (mobile) — EXACTLY LIKE guest.blade.php --}}
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

                        {{-- Logout (mobile) --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="button"
                                    @click="animateLogout($el)"
                                    class="inline-flex items-center justify-between w-full rounded-xl px-3 py-2 text-sm font-semibold transition shadow-sm border
                                           bg-neutral text-neutral-content hover:opacity-95
                                           dark:bg-neutral-content dark:text-neutral dark:border-base-300 dark:hover:opacity-95">
                                <span>Log Out</span>
                                <span class="logoutCanvas doorCanvas">
                                    <svg class="doorway" viewBox="0 0 100 100" aria-hidden="true">
                                        <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                                        <path class="bang" d="M40.5 43.7L26.6 31.4l-2.5 6.7zM41.9 50.4l-19.5-4-1.4 6.3zM40 57.4l-17.7 3.9 3.9 5.7z" />
                                    </svg>
                                    <svg class="figure" viewBox="0 0 100 100" aria-hidden="true">
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
                                    <svg class="door" viewBox="0 0 100 100" aria-hidden="true">
                                        <path d="M93.4 86.3H58.6c-1.9 0-3.4-1.5-3.4-3.4V17.1c0-1.9 1.5-3.4 3.4-3.4h34.8c1.9 0 3.4 1.5 3.4 3.4v65.8c0 1.9-1.5 3.4-3.4 3.4z" />
                                        <circle cx="66" cy="50" r="3.7" />
                                    </svg>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ===== Compact door canvas styling (shared) ===== --}}
<style>
    .doorCanvas{
        --figure-duration:100ms; --transform-figure:none;
        --walking-duration:100ms;
        --transform-arm1:none; --transform-wrist1:none;
        --transform-arm2:none; --transform-wrist2:none;
        --transform-leg1:none; --transform-calf1:none;
        --transform-leg2:none; --transform-calf2:none;

        position:relative; display:inline-block;
        height:28px; width:56px; padding-left:4px;
        background:transparent; border:0; outline:0;
        perspective:100px; text-align:left; vertical-align:middle;
    }
    .doorCanvas svg{position:absolute; display:block;}
    .doorCanvas .figure{
        bottom:2px; right:6px; width:18px; z-index:4;
        fill: oklch(var(--color-primary));
        transform:var(--transform-figure);
        transition:transform calc(var(--figure-duration)*1ms) cubic-bezier(.2,.1,.8,.9);
    }
    .doorCanvas .door, .doorCanvas .doorway{ bottom:2px; right:4px; width:20px; }
    .doorCanvas .door{ transform:rotateY(20deg); transform-origin:100% 50%; transform-style:preserve-3d; transition:transform 200ms ease; z-index:5; }
    .doorCanvas .door path{ fill: oklch(var(--color-primary)); stroke: oklch(var(--color-primary)); stroke-width:4; }
    .doorCanvas .doorway{ z-index:3; }
    .doorCanvas .bang{ opacity:0; }

    .doorCanvas .arm1,.doorCanvas .wrist1,.doorCanvas .arm2,.doorCanvas .wrist2,
    .doorCanvas .leg1,.doorCanvas .calf1,.doorCanvas .leg2,.doorCanvas .calf2{
        transition:transform calc(var(--walking-duration)*1ms) ease-in-out;
    }
    .doorCanvas .arm1{transform:var(--transform-arm1); transform-origin:52% 45%;}
    .doorCanvas .wrist1{transform:var(--transform-wrist1); transform-origin:59% 55%;}
    .doorCanvas .arm2{transform:var(--transform-arm2); transform-origin:47% 43%;}
    .doorCanvas .wrist2{transform:var(--transform-wrist2); transform-origin:35% 47%;}
    .doorCanvas .leg1{transform:var(--transform-leg1); transform-origin:47% 64.5%;}
    .doorCanvas .calf1{transform:var(--transform-calf1); transform-origin:55.5% 71.5%;}
    .doorCanvas .leg2{transform:var(--transform-leg2); transform-origin:43% 63%;}
    .doorCanvas .calf2{transform:var(--transform-calf2); transform-origin:41.5% 73%;}
    .doorCanvas:hover .door{ transform:rotateY(22deg); }
    .doorCanvas.clicked .door{ transform:rotateY(32deg); }
</style>

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

    /* ===== Logout animation (compact) — unchanged ===== */
    const logoutStates = {
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
    function setLogoutState(el, key){ const s = logoutStates[key]; if(!s) return; for(const k in s){ el.style.setProperty(k, s[k]); } }

    function animateLogout(buttonEl){
        if (buttonEl.__busy) return;
        const canvas = buttonEl.querySelector('.logoutCanvas');
        if (!canvas) return;

        buttonEl.__busy = true;
        setLogoutState(canvas,'hover');
        setTimeout(()=> {
            canvas.classList.add('clicked');
            setLogoutState(canvas,'walking1');
            setTimeout(()=> {
                setLogoutState(canvas,'walking2');
                setTimeout(()=> {
                    canvas.classList.remove('clicked');
                    setLogoutState(canvas,'default');

                    const form = buttonEl.closest('form');
                    if (form) form.submit();

                    buttonEl.__busy = false;
                }, parseInt(logoutStates.walking2['--figure-duration']) + 120);
            }, parseInt(logoutStates.walking1['--figure-duration']) + 80);
        }, 80);
    }

    // Optional hover hint for the mini-canvas
    document.addEventListener('mouseover', e=>{
        const c = e.target.closest('.doorCanvas'); if(!c || c.__busy) return; setLogoutState(c,'hover');
    });
    document.addEventListener('mouseout', e=>{
        const c = e.target.closest('.doorCanvas'); if(!c || c.__busy) return; setLogoutState(c,'default');
    });
</script>
