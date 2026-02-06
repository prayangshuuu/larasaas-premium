{{-- resources/views/layouts/navigation.blade.php --}}
<style>[x-cloak]{display:none!important}</style>

@php
    $user = Auth::user();
@endphp

<nav x-data="{ mobileOpen: false, scrolled: false }"
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     class="sticky top-0 z-50 w-full border-b border-slate-200 transition-all duration-300"
     :class="{ 'bg-white/80 backdrop-blur-md shadow-sm': scrolled, 'bg-white': !scrolled }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            {{-- Logo --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="p-1.5 bg-primary-600 rounded-lg text-white group-hover:bg-primary-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-primary-600 transition-colors">
                            IELTS<span class="font-medium text-slate-600">BandBooster</span>
                        </span>
                    </a>
                </div>
            </div>

            {{-- Desktop Actions --}}
            <div class="hidden sm:flex sm:items-center sm:gap-4">
                
                {{-- Theme Switcher (Custom Implementation) --}}
                <div x-data="{ 
                        theme: localStorage.theme || 'light',
                        toggle() {
                            this.theme = this.theme === 'light' ? 'dark' : 'light';
                            localStorage.theme = this.theme;
                            if (this.theme === 'dark') {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        }
                    }"
                    class="relative"
                    x-init="$watch('theme', val => console.log('Theme changed to', val))">
                    
                    <button @click="toggle()" 
                            class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            
                        <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </button>
                </div>

                {{-- Profile Dropdown --}}
                <div class="relative ml-3" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @click.outside="open = false"
                            class="flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 hover:ring-2 hover:ring-slate-200 transition-all">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-9 w-9 rounded-full object-cover border border-slate-200" 
                             src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6366f1&color=fff' }}" 
                             alt="{{ $user->name }}" />
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Signed in as</p>
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary-600 transition-colors">
                            Profile Settings
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="mobileOpen = !mobileOpen" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 transition-colors">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden border-t border-slate-200 bg-white"
         @click.outside="mobileOpen = false"
         style="display: none;">
        
        <div class="space-y-1 pt-2 pb-3 px-4">
            <a href="{{ route('dashboard') }}" 
               class="block rounded-md py-2 px-3 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 border-l-4 border-primary-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Dashboard
            </a>
            <a href="{{ route('profile.edit') }}" 
               class="block rounded-md py-2 px-3 text-base font-medium {{ request()->routeIs('profile.edit') ? 'bg-primary-50 text-primary-700 border-l-4 border-primary-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                Profile
            </a>
        </div>

        <div class="border-t border-slate-200 pb-3 pt-4 px-4 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="shrink-0">
                    <img class="h-10 w-10 rounded-full border border-slate-200" src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="{{ $user->name }}" />
                </div>
                <div>
                    <div class="text-base font-medium text-slate-800">{{ $user->name }}</div>
                    <div class="text-sm font-medium text-slate-500">{{ $user->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-slate-600 hover:bg-white hover:text-red-600 hover:shadow-sm transition-all border border-transparent hover:border-slate-200">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
