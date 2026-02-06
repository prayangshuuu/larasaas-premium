@php
    $user = Auth::user();
@endphp

<nav x-data="{ mobileOpen: false, scrolled: false }"
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     class="fixed top-0 z-50 w-full border-b border-transparent transition-all duration-300"
     :class="{ 'bg-black/50 backdrop-blur-xl border-white/10': scrolled, 'bg-transparent': !scrolled }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            {{-- Logo --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="p-1.5 rounded-lg border border-transparent group-hover:border-indigo-500/50 transition-colors">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white group-hover:text-indigo-400 transition-colors">
                            IELTS<span class="font-medium text-zinc-500">BandBooster</span>
                        </span>
                    </a>
                </div>
            </div>

            {{-- Desktop Actions --}}
            <div class="hidden sm:flex sm:items-center sm:gap-4">
                
                {{-- Admin Navigation --}}
                @if($user && $user->isAdmin())
                    <div class="hidden md:flex items-center gap-6 mr-4 border-r border-zinc-800 pr-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Admin</a>
                        <a href="{{ route('admin.coupons.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.coupons.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Coupons</a>
                        <a href="{{ route('admin.subscriptions.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.subscriptions.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Subscriptions</a>
                    </div>
                @endif

                {{-- Profile Dropdown --}}
                <div class="relative ml-3" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @click.outside="open = false"
                            class="flex rounded-full bg-zinc-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-black transition-all">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-9 w-9 rounded-full object-cover border border-zinc-700" 
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
                         class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl bg-zinc-900 border border-zinc-800 py-1 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-zinc-800">
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Signed in as</p>
                            <p class="truncate text-sm font-semibold text-white">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors">
                            Profile Settings
                        </a>

                        @if(Route::has('billing.index'))
                        <a href="{{ route('billing.index') }}" class="block px-4 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors">
                            Billing & Plans
                        </a>
                        @endif
                        

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="mobileOpen = !mobileOpen" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-zinc-400 hover:text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-colors">
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
         class="sm:hidden border-t border-zinc-800 bg-black/90 backdrop-blur-xl"
         @click.outside="mobileOpen = false"
         style="display: none;">
        
        <div class="space-y-1 pt-2 pb-3 px-4">
            <a href="{{ route('dashboard') }}" 
               class="block rounded-md py-2 px-3 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                Dashboard
            </a>
            <a href="{{ route('profile.edit') }}" 
               class="block rounded-md py-2 px-3 text-base font-medium {{ request()->routeIs('profile.edit') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                Profile
            </a>
            
            @if($user && $user->isAdmin())
                <div class="border-t border-zinc-800 mt-2 pt-2 pb-1">
                    <p class="px-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Admin</p>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="block rounded-md py-2 px-3 text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" 
                       class="block rounded-md py-2 px-3 text-base font-medium {{ request()->routeIs('admin.coupons.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                        Coupons
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}" 
                       class="block rounded-md py-2 px-3 text-base font-medium {{ request()->routeIs('admin.subscriptions.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                        Subscriptions
                    </a>
                </div>
            @endif
        </div>

        <div class="border-t border-zinc-800 pb-3 pt-4 px-4">
            <div class="flex items-center gap-3">
                <div class="shrink-0">
                    <img class="h-10 w-10 rounded-full border border-zinc-700" src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="{{ $user->name }}" />
                </div>
                <div>
                    <div class="text-base font-medium text-white">{{ $user->name }}</div>
                    <div class="text-sm font-medium text-zinc-500">{{ $user->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-zinc-400 hover:bg-zinc-800 hover:text-white transition-all">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
