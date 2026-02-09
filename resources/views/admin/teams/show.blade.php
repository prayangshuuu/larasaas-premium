@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.teams.index') }}" class="group flex items-center justify-center p-2 rounded-lg bg-zinc-800/50 hover:bg-zinc-800 text-zinc-400 hover:text-white transition-all">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-tight">{{ $team->name }}</h1>
                        <p class="text-sm text-zinc-400">Team Details & Members</p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('admin.teams.destroy', $team) }}" 
                  method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this team? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all shadow-lg shadow-red-500/20">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Team
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Team Info Card --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-2xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-zinc-900 border border-zinc-800/50 rounded-2xl p-6 shadow-xl backdrop-blur-xl">
                        <h3 class="text-lg font-medium text-white mb-4">Team Information</h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Owner</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('admin.users.edit', $team->owner) }}" class="flex items-center group/owner">
                                        <img class="h-8 w-8 rounded-full object-cover ring-2 ring-zinc-800 group-hover/owner:ring-indigo-500/50 transition-all" 
                                             src="{{ $team->owner->profile_photo_url }}" 
                                             alt="{{ $team->owner->name }}">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-zinc-300 group-hover/owner:text-indigo-400 transition-colors">{{ $team->owner->name }}</div>
                                            <div class="text-xs text-zinc-500">{{ $team->owner->email }}</div>
                                        </div>
                                    </a>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Type</dt>
                                <dd class="mt-1">
                                    @if($team->personal_team)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-900/30 text-blue-400 border border-blue-800/50">Personal</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900/30 text-green-400 border border-green-800/50">Team</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Created</dt>
                                <dd class="mt-1 text-sm text-zinc-300">{{ $team->created_at->format('M d, Y H:i') }}</dd>
                            </div>

                             <div>
                                <dt class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Updated</dt>
                                <dd class="mt-1 text-sm text-zinc-300">{{ $team->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Members List --}}
            <div class="lg:col-span-2">
                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-2xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    
                    <div class="relative bg-zinc-900 border border-zinc-800/50 rounded-2xl shadow-xl overflow-hidden backdrop-blur-xl">
                        <div class="px-6 py-4 border-b border-zinc-800/50">
                            <h3 class="text-lg font-medium text-white">Team Members</h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-800/50">
                                <thead class="bg-zinc-900/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">User</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Role</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">Joined</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800/50">
                                    {{-- Owner --}}
                                    <tr class="bg-zinc-800/20">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.users.edit', $team->owner) }}" class="flex items-center hover:opacity-80 transition-opacity">
                                                <img class="h-8 w-8 rounded-full object-cover ring-2 ring-indigo-500/50" 
                                                     src="{{ $team->owner->profile_photo_url }}" 
                                                     alt="">
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-white">{{ $team->owner->name }}</div>
                                                    <div class="text-xs text-zinc-500">{{ $team->owner->email }}</div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-900/30 text-indigo-400 border border-indigo-800/50">
                                                Owner
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-zinc-500">
                                            {{ $team->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            {{-- No actions for owner --}}
                                        </td>
                                    </tr>

                                    {{-- Members --}}
                                    @foreach ($members as $member)
                                         <tr class="hover:bg-zinc-800/30 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('admin.users.edit', $member) }}" class="flex items-center hover:opacity-80 transition-opacity">
                                                    <img class="h-8 w-8 rounded-full object-cover ring-2 ring-zinc-800" 
                                                         src="{{ $member->profile_photo_url }}" 
                                                         alt="">
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-zinc-300">{{ $member->name }}</div>
                                                        <div class="text-xs text-zinc-500">{{ $member->email }}</div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-800 text-zinc-400 border border-zinc-700">
                                                    {{ ucfirst($member->membership->role ?? 'Member') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-zinc-500">
                                                {{ $member->membership->created_at ? $member->membership->created_at->diffForHumans() : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('admin.teams.members.remove', [$team, $member]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this member?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Pagination --}}
                        @if($members->hasPages())
                            <div class="px-6 py-4 border-t border-zinc-800/50 bg-zinc-900/30">
                                {{ $members->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
