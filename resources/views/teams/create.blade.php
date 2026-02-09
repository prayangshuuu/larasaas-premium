<x-app-layout>
    <div class="min-h-[80vh] flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        {{-- Background Gradient --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-500/20 via-zinc-900/0 to-zinc-900/0"></div>
        </div>

        {{-- Main Card --}}
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-zinc-900/80 border border-zinc-800 backdrop-blur-xl shadow-2xl overflow-hidden sm:rounded-2xl relative z-10">
            
            {{-- Header & Icon --}}
            <div class="mb-8 text-center">
                <div class="mx-auto h-16 w-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 mb-6 transform rotate-3">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">
                    {{ __('Create Your Team') }}
                </h2>
                <p class="mt-2 text-sm text-zinc-400">
                    {{ __('Collaborate with others, share resources, and manage projects together.') }}
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('teams.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Team Name')" class="sr-only" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="e.g. Acme Corp Marketing"
                            class="block w-full pl-10 pr-3 py-3 border border-zinc-700 rounded-xl leading-5 bg-zinc-800/50 text-white placeholder-zinc-500 focus:outline-none focus:bg-zinc-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm transition-all shadow-inner" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-500/20 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        {{ __('Create Team') }}
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('dashboard') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">
                        {{ __('Cancel and return to dashboard') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
