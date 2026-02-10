<x-app-layout>
    {{-- Header Background --}}
    <div class="relative bg-zinc-900 border-b border-zinc-800 pb-32">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-zinc-900/50 to-purple-500/10 opacity-50"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                
                {{-- Team Info --}}
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg ring-1 ring-white/10">
                            {{ substr($team->name, 0, 1) }}
                        </div>
                        <h1 class="text-3xl font-bold text-white tracking-tight">{{ $team->name }}</h1>
                    </div>
                    <div class="flex items-center gap-2 group cursor-pointer" onclick="navigator.clipboard.writeText('{{ url('/teams/' . $team->slug) }}'); alert('Slug copied!')">
                        <span class="px-2.5 py-0.5 rounded-md bg-zinc-800 border border-zinc-700 text-xs font-mono text-zinc-400 group-hover:text-indigo-400 group-hover:border-indigo-500/50 transition-colors">
                            {{ $team->slug }}
                        </span>
                        <svg class="w-4 h-4 text-zinc-500 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="flex gap-4">
                    <div class="px-4 py-3 bg-zinc-800/50 backdrop-blur-md border border-zinc-700/50 rounded-xl">
                        <p class="text-xs text-zinc-500 uppercase font-semibold">Members</p>
                        <p class="text-2xl font-bold text-white">{{ $team->users->count() }}</p>
                    </div>
                    <div class="px-4 py-3 bg-zinc-800/50 backdrop-blur-md border border-zinc-700/50 rounded-xl">
                        <p class="text-xs text-zinc-500 uppercase font-semibold">Pending</p>
                        <p class="text-2xl font-bold text-white">{{ $team->invitations->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-20 space-y-8">
        {{-- Status Messages --}}
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition class="rounded-xl border border-green-500/20 bg-green-500/10 p-4 text-green-400 flex justify-between items-center backdrop-blur-md">
                <span class="text-sm font-medium">{{ session('status') }}</span>
                <button @click="show = false" class="text-green-500 hover:text-green-300"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        @endif
        @if (session('error'))
             <div x-data="{ show: true }" x-show="show" x-transition class="rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-red-400 flex justify-between items-center backdrop-blur-md">
                <span class="text-sm font-medium">{{ session('error') }}</span>
                 <button @click="show = false" class="text-red-500 hover:text-red-300"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        @endif

        {{-- Main Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Members & Invites --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Team Members --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-xl">
                    <div class="px-6 py-5 border-b border-zinc-800 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white">Team Members</h3>
                        <span class="px-2 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-xs font-medium">{{ $team->users->count() }} Active</span>
                    </div>
                    
                    <div class="divide-y divide-zinc-800/50">
                        {{-- Members Loop --}}
                        @foreach($team->users as $user)
                            <div class="p-4 hover:bg-zinc-800/30 transition-colors flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-zinc-800 group-hover:ring-indigo-500/50 transition-all" 
                                         src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6366f1&color=fff' }}" 
                                         alt="{{ $user->name }}">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                                            @if($user->id === $team->user_id)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">Owner</span>
                                            @elseif($user->membership->role === 'admin')
                                                 <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold bg-purple-500/10 text-purple-500 border border-purple-500/20">Admin</span>
                                            @else
                                                 <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold bg-blue-500/10 text-blue-500 border border-blue-500/20">Member</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-zinc-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                
                                {{-- Actions --}}
                                @if($permissions['canRemoveTeamMembers'] && $user->id !== $team->user_id)
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.outside="open = false" class="p-1 rounded-md text-zinc-500 hover:text-white hover:bg-zinc-700 transition-colors">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-zinc-800 ring-1 ring-black ring-opacity-5 z-50 divide-y divide-zinc-700" 
                                             style="display: none;">
                                            
                                            <form method="POST" action="{{ route('teams.members.destroy', [$team, $user]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-400 hover:bg-zinc-700 hover:text-red-300 transition-colors" onclick="return confirm('Remove this user?')">
                                                    Remove from Team
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pending Invites --}}
                @if($team->invitations->isNotEmpty())
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-xl">
                    <div class="px-6 py-5 border-b border-zinc-800">
                        <h3 class="text-lg font-semibold text-white">Pending Invitations</h3>
                    </div>
                    <div class="divide-y divide-zinc-800/50">
                        @foreach($team->invitations as $invitation)
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-300">{{ $invitation->email }}</p>
                                        <p class="text-xs text-zinc-500">Sent {{ $invitation->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <button class="text-xs text-zinc-500 hover:text-red-400 transition-colors">Cancel</button>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            {{-- Right Column: Actions & Settings --}}
            <div class="space-y-8">
                
                {{-- Invite Card --}}
                @if($permissions['canAddTeamMembers'])
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden p-6 relative group">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <h3 class="text-lg font-semibold text-white mb-2 relative z-10">Invite New Member</h3>
                    <p class="text-sm text-zinc-400 mb-4 relative z-10">Grow your team by adding new members via email.</p>
                    
                    <form method="post" action="{{ route('teams.members.store', $team) }}" class="relative z-10">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label for="email" class="sr-only">Email Address</label>
                                <input id="email" name="email" type="email" required placeholder="colleague@example.com" 
                                       class="block w-full rounded-xl border-zinc-700 bg-zinc-800/50 text-white placeholder-zinc-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-indigo-500 transition-colors">
                                Send Invitation
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- Settings Card --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-zinc-800">
                        <h3 class="text-lg font-semibold text-white">Team Settings</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <form method="post" action="{{ route('teams.update', $team) }}">
                            @csrf
                            @method('put')
                            <div>
                                <x-input-label for="name" :value="__('Team Name')" class="text-zinc-300" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-zinc-800 border-zinc-700 text-white focus:border-indigo-500 focus:ring-indigo-500" :value="old('name', $team->name)" required :disabled="!$permissions['canUpdateTeam']" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            
                            @if($permissions['canUpdateTeam'])
                                <div class="mt-4 flex justify-end">
                                    <x-primary-button class="bg-zinc-700 hover:bg-zinc-600">{{ __('Save Changes') }}</x-primary-button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Danger Zone --}}
                @if($permissions['canDeleteTeam'])
                <div class="bg-red-500/5 border border-red-500/20 rounded-2xl shadow-xl overflow-hidden p-6">
                    <h3 class="text-lg font-semibold text-red-500 mb-2">Danger Zone</h3>
                    <p class="text-sm text-red-400/60 mb-4">Permanently delete this team and all data.</p>
                    
                    <form method="post" action="{{ route('teams.destroy', $team) }}">
                        @csrf
                        @method('delete')
                        <x-danger-button type="submit" onclick="return confirm('Are you sure you want to delete this team? This action cannot be undone.')" class="w-full justify-center">
                            {{ __('Delete Team') }}
                        </x-danger-button>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
