{{-- resources/views/layouts/navigation.blade.php --}}
<style>[x-cloak]{display:none!important}</style>

@php
    $appName = config('app.name', 'IELTSBandBooster');
    // Ensure we have a user for the avatar
    $user = Auth::user();
@endphp

<nav x-data="{ mobileOpen: false, scrolled: false }"
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="{ 'bg-base-100/80 backdrop-blur-md shadow-sm': scrolled, 'bg-base-100 border-b border-base-300': !scrolled }"
     class="sticky top-0 z-50 transition-all duration-300 border-b border-base-300">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            {{-- Logo --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl gap-2 normal-case px-0 hover:bg-transparent">
                        <span class="text-primary font-bold text-2xl">IELTS</span><span class="font-semibold text-base-content">BandBooster</span>
                    </a>
                </div>
            </div>

            {{-- Desktop Actions --}}
            <div class="hidden sm:flex sm:items-center sm:gap-4">
                
                {{-- Theme Toggle (Alpine) --}}
                <div title="Toggle Theme" class="tooltip tooltip-bottom">
                    <label class="swap swap-rotate btn btn-ghost btn-circle btn-sm">
                        <input type="checkbox" class="theme-controller" value="dark" />
                        <svg class="swap-off fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>
                        <svg class="swap-on fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
                    </label>
                </div>

                {{-- Profile Dropdown (Alpine "Headless" Pattern) --}}
                <div class="relative ml-3" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @click.outside="open = false"
                            class="btn btn-ghost btn-circle avatar online transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <div class="w-9 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img alt="{{ $user->name }}" 
                                 src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" />
                        </div>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl bg-base-100 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-base-200">
                            <p class="text-xs text-base-content/70">Signed in as</p>
                            <p class="truncate text-sm font-semibold text-base-content">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-base-content hover:bg-base-200 transition-colors">
                            Profile Settings
                        </a>
                        
                        {{-- API Tokens link removed as Jetstream is not installed --}}

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-error hover:bg-error/10 transition-colors">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="mobileOpen = !mobileOpen" class="btn btn-ghost btn-circle">
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
         class="sm:hidden border-t border-base-200 bg-base-100 shadow-lg"
         @click.outside="mobileOpen = false"
         style="display: none;">
        
        <div class="space-y-1 pt-2 pb-3 px-4">
            <a href="{{ route('dashboard') }}" class="block rounded-lg py-2 pl-3 pr-4 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content' }}">
                Dashboard
            </a>
            <a href="{{ route('profile.edit') }}" class="block rounded-lg py-2 pl-3 pr-4 text-base font-medium {{ request()->routeIs('profile.edit') ? 'bg-primary/10 text-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content' }}">
                Profile
            </a>
        </div>

        <div class="border-t border-base-200 pb-1 pt-4 px-4 bg-base-100/50">
            <div class="flex items-center gap-3">
                <div class="avatar">
                    <div class="w-10 rounded-full">
                        <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="{{ $user->name }}" />
                    </div>
                </div>
                <div>
                    <div class="text-base font-medium text-base-content">{{ $user->name }}</div>
                    <div class="text-sm font-medium text-base-content/70">{{ $user->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-error btn-outline btn-sm w-full justify-start">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
