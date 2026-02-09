<x-app-layout>
    {{-- Header Background --}}
    <div class="relative bg-zinc-900 border-b border-zinc-800 pb-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-zinc-900/50 to-purple-500/10 opacity-50 z-0"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">{{ __('My Teams') }}</h1>
                    <p class="mt-2 text-zinc-400 max-w-2xl">
                        {{ __('Manage your workspaces and collaborate with your team members.') }}
                    </p>
                </div>
                <a href="{{ route('teams.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all shadow-lg shadow-indigo-500/20 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Create New Team') }}
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-20 space-y-8">
        
        @if($teams->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($teams as $team)
                    <a href="{{ route('teams.show', $team) }}" class="group block h-full">
                        <div class="h-full bg-zinc-900/50 border border-zinc-800 rounded-2xl p-6 backdrop-blur-xl transition-all duration-300 hover:bg-zinc-800/80 hover:border-indigo-500/30 hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-1 relative overflow-hidden">
                            
                            {{-- Gradient Glow on Hover --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="relative z-10 flex flex-col h-full">
                                {{-- Header --}}
                                <div class="flex items-start justify-between mb-4">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-zinc-700 flex items-center justify-center text-white font-bold text-lg shadow-inner group-hover:from-indigo-900/50 group-hover:to-purple-900/50 group-hover:border-indigo-500/30 transition-all">
                                        {{ substr($team->name, 0, 1) }}
                                    </div>
                                    @if(Auth::user()->isCurrentTeam($team))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                            Current
                                        </span>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="mb-4 flex-grow">
                                    <h3 class="text-lg font-bold text-white mb-1 group-hover:text-indigo-400 transition-colors">{{ $team->name }}</h3>
                                    <p class="text-sm text-zinc-500 font-mono flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                        {{ $team->slug }}
                                    </p>
                                </div>

                                {{-- Footer --}}
                                <div class="pt-4 border-t border-zinc-800 flex items-center justify-between">
                                    <div class="flex -space-x-2 overflow-hidden">
                                        @foreach($team->users->take(4) as $user)
                                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-zinc-900" src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="{{ $user->name }}">
                                        @endforeach
                                        @if($team->users->count() > 4)
                                            <div class="h-6 w-6 rounded-full ring-2 ring-zinc-900 bg-zinc-800 flex items-center justify-center text-[10px] font-medium text-zinc-400">
                                                +{{ $team->users->count() - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="text-xs font-medium uppercase tracking-wider">
                                        @if(Auth::user()->ownsTeam($team))
                                            <span class="text-amber-500">Owner</span>
                                        @elseif(Auth::user()->hasTeamRole($team, 'admin'))
                                            <span class="text-purple-500">Admin</span>
                                        @else
                                            <span class="text-blue-500">Member</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach

                {{-- Add New Team Card --}}
                <a href="{{ route('teams.create') }}" class="group block h-full">
                    <div class="h-full bg-zinc-900/30 border border-zinc-800 border-dashed rounded-2xl p-6 backdrop-blur-sm transition-all duration-300 hover:bg-zinc-800/50 hover:border-zinc-700 flex flex-col items-center justify-center text-center min-h-[240px]">
                        <div class="h-16 w-16 rounded-full bg-zinc-800 group-hover:bg-zinc-700 flex items-center justify-center text-zinc-500 group-hover:text-zinc-300 transition-colors mb-4">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-white group-hover:text-indigo-400 transition-colors">{{ __('Create Another Team') }}</h3>
                        <p class="mt-2 text-sm text-zinc-500">{{ __('Launch a new project or workspace.') }}</p>
                    </div>
                </a>
            </div>
        @else
            {{-- Empty State --}}
            <div class="max-w-md mx-auto text-center py-12">
                <div class="mb-8">
                     <div class="mx-auto h-24 w-24 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full flex items-center justify-center">
                        <svg class="h-12 w-12 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">{{ __('No Teams Found') }}</h2>
                <p class="text-zinc-400 mb-8">{{ __('You aren\'t part of any teams yet. Create one to get started!') }}</p>
                <a href="{{ route('teams.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Create Your First Team') }}
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
