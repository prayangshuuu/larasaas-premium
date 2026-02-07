<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Support Tickets</h2>
                <a href="{{ route('support.create') }}" class="inline-flex h-10 animate-shimmer items-center justify-center rounded-md border border-zinc-800 bg-[linear-gradient(110deg,#18181b,45%,#27272a,55%,#18181b)] bg-[length:200%_100%] px-6 font-medium text-zinc-300 transition-colors focus:outline-none focus:ring-2 focus:ring-zinc-400 focus:ring-offset-2 focus:ring-offset-zinc-50 relative z-10">
                    Create New Ticket
                </a>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-green-900/50 border border-green-800 text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-400">
                        <thead class="bg-zinc-800/50 text-zinc-200 uppercase font-medium">
                            <tr>
                                <th class="px-6 py-4">Ticket ID</th>
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
                                        <a href="{{ route('support.show', $ticket) }}" class="text-indigo-400 hover:text-indigo-300 font-medium hover:underline">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-zinc-500">
                                        No support tickets found.
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
    </div>
</x-app-layout>
