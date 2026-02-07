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
                {{-- User Support Link --}}
                <a href="{{ route('support.index') }}" class="text-sm font-medium {{ request()->routeIs('support.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Support</a>
                
                {{-- Admin Navigation --}}
                @if($user && $user->isAdmin())
                    <div class="hidden md:flex items-center gap-6 mr-4 border-r border-zinc-800 pr-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Admin</a>
                        <a href="{{ route('admin.coupons.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.coupons.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Coupons</a>
                        <a href="{{ route('admin.subscriptions.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.subscriptions.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Subscriptions</a>
                        <a href="{{ route('admin.support.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.support.*') ? 'text-indigo-400' : 'text-zinc-400 hover:text-white' }} transition-colors">Tickets</a>
                    </div>
                @endif

                {{-- Notifications Dropdown --}}
                <div class="relative ml-4 mr-4" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @click.outside="open = false"
                            class="relative p-2 rounded-full text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-black">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        
                        @if($user->unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-black"></span>
                        @endif
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-zinc-900 border border-zinc-800 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-zinc-800 flex justify-between items-center bg-zinc-900/50 backdrop-blur-xl">
                            <span class="text-sm font-semibold text-white">Notifications</span>
                            @if($user->unreadNotifications->count() > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                                        Mark all read
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse($user->notifications->take(5) as $notification)
                                <div class="relative group border-b border-zinc-800/50 last:border-0 hover:bg-zinc-800/50 transition-colors p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="shrink-0">
                                            @if($notification->read_at)
                                                <div class="mt-1 h-2 w-2 rounded-full bg-zinc-600"></div>
                                            @else
                                                <div class="mt-1 h-2 w-2 rounded-full bg-indigo-500 ring-2 ring-indigo-500/20"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-zinc-200 {{ $notification->read_at ? 'text-zinc-500' : '' }}">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                            <p class="text-xs text-zinc-500 mt-0.5 line-clamp-2">
                                                {{ $notification->data['message'] ?? 'No detail provided.' }}
                                            </p>
                                            <p class="text-[10px] text-zinc-600 mt-1.5">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        
                                        @if(is_null($notification->read_at))
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                @csrf
                                                <button type="submit" class="p-1 rounded-md text-zinc-500 hover:text-white hover:bg-zinc-700 transition-colors" title="Mark as read">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-12 text-center">
                                    <div class="mx-auto h-12 w-12 rounded-full bg-zinc-800/50 flex items-center justify-center mb-3">
                                        <svg class="h-6 w-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-white">All caught up!</h3>
                                    <p class="text-xs text-zinc-500 mt-1">No recent notifications</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @if($user->notifications->count() > 5)
                            <div class="px-4 py-2 bg-zinc-900/50 border-t border-zinc-800 text-center backdrop-blur-xl">
                                <a href="#" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">View all notifications</a>
                            </div>
                        @endif
                    </div>
                </div>

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
