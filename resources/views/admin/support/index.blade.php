@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-white">Support Tickets</h1>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-lg bg-green-900/50 border border-green-800 text-green-300">
                {{ session('status') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
            <form action="{{ route('admin.support.index') }}" method="GET" class="flex gap-4 w-full">
                <select name="status" class="bg-zinc-800 border-zinc-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>Answered</option>
                    <option value="customer_reply" {{ request('status') === 'customer_reply' ? 'selected' : '' }}>Customer Reply</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>

                <select name="priority" class="bg-zinc-800 border-zinc-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-indigo-600/10 text-indigo-400 border border-indigo-600/20 rounded-md text-sm font-semibold hover:bg-indigo-600/20 transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-400">
                    <thead class="bg-zinc-800/50 text-zinc-200 uppercase font-medium">
                        <tr>
                            <th class="px-6 py-4">Ticket ID</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Priority</th>
                            <th class="px-6 py-4">Created</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @forelse ($tickets as $ticket)
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 font-medium text-white">{{ $ticket->ticket_id }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $ticket->user->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $ticket->user->email }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $ticket->subject }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'open' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                            'answered' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                            'customer_reply' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                            'closed' => 'bg-zinc-500/10 text-zinc-500 border-zinc-500/20',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusColors[$ticket->status] ?? 'bg-zinc-500/10 text-zinc-500' }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityColors = [
                                            'low' => 'text-zinc-400',
                                            'medium' => 'text-yellow-500',
                                            'high' => 'text-red-500 font-bold',
                                        ];
                                    @endphp
                                    <span class="{{ $priorityColors[$ticket->priority] }}">{{ ucfirst($ticket->priority) }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $ticket->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.support.show', $ticket) }}" class="text-indigo-400 hover:text-indigo-300 font-medium hover:underline">Manage</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                                    No tickets found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-zinc-800">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
