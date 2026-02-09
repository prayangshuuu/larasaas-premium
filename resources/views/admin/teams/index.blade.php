@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Teams</h1>
                <p class="mt-1 text-sm text-zinc-400">Manage all teams in the system.</p>
            </div>
            
            {{-- Search --}}
            <form action="{{ route('admin.teams.index') }}" method="GET" class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-zinc-500 group-focus-within:text-indigo-500 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search teams or owners..." 
                       class="block w-full pl-10 pr-3 py-2 border border-zinc-800 rounded-lg leading-5 bg-zinc-900/50 text-zinc-300 placeholder-zinc-500 focus:outline-none focus:bg-zinc-900 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm">
            </form>
        </div>

        {{-- Content --}}
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-2xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
            
            <div class="relative bg-zinc-900 border border-zinc-800/50 rounded-2xl shadow-xl overflow-hidden backdrop-blur-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-800/50">
                        <thead class="bg-zinc-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Team Name</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Owner</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-zinc-400 uppercase tracking-wider">Members</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-zinc-400 uppercase tracking-wider">Personal</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">Created</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse ($teams as $team)
                                <tr class="hover:bg-zinc-800/30 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-300 font-bold text-xs ring-1 ring-indigo-500/30">
                                                {{ substr($team->name, 0, 2) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ $team->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.users.edit', $team->owner) }}" class="flex items-center hover:opacity-80 transition-opacity group/owner">
                                            <img class="h-8 w-8 rounded-full object-cover ring-2 ring-zinc-800 group-hover/owner:ring-indigo-500/50 transition-all" 
                                                 src="{{ $team->owner->profile_photo_url }}" 
                                                 alt="{{ $team->owner->name }}">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-zinc-300 group-hover/owner:text-indigo-400 transition-colors">{{ $team->owner->name }}</div>
                                                <div class="text-xs text-zinc-500">{{ $team->owner->email }}</div>
                                            </div>
                                        </a>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-800 text-zinc-300 border border-zinc-700">
                                            {{ $team->users_count }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($team->personal_team)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-900/30 text-blue-400 border border-blue-800/50">
                                                Personal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900/30 text-green-400 border border-green-800/50">
                                                Team
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-zinc-500">
                                        {{ $team->created_at->format('M d, Y') }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center gap-3">
                                            <a href="{{ route('admin.teams.show', $team) }}" 
                                               class="text-indigo-400 hover:text-indigo-300 transition-colors">
                                                View
                                            </a>
                                            
                                            <form action="{{ route('admin.teams.destroy', $team) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this team? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="h-12 w-12 rounded-full bg-zinc-800 flex items-center justify-center mb-3">
                                                <svg class="h-6 w-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-sm font-medium text-zinc-300">No teams found</h3>
                                            <p class="mt-1 text-sm text-zinc-500">Try adjusting your search or create a new team.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($teams->hasPages())
                    <div class="px-6 py-4 border-t border-zinc-800/50 bg-zinc-900/30">
                        {{ $teams->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
