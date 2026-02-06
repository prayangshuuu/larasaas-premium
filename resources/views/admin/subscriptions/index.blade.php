@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Subscriptions</h2>
        <a href="{{ route('admin.subscriptions.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
            Assign Subscription
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-zinc-900 overflow-hidden shadow-xl sm:rounded-lg border border-zinc-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-700">
                <thead class="bg-zinc-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            Plan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            Renewal / Ends
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-zinc-900 divide-y divide-zinc-700">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-white">{{ $sub->user->name ?? 'Unknown' }}</div>
                                    <div class="ml-2 text-xs text-zinc-500">{{ $sub->user->email ?? '' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-zinc-300 font-semibold">{{ $sub->plan->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $sub->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $sub->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $sub->status === 'past_due' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                ">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                {{ $sub->current_period_end ? \Carbon\Carbon::parse($sub->current_period_end)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.subscriptions.edit', $sub) }}" class="text-indigo-400 hover:text-indigo-300 mr-2">Edit</a>
                                    <form action="{{ route('admin.subscriptions.destroy', $sub) }}" method="POST" onsubmit="return confirm('Are you sure? This will cancel the subscription immediately.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400">Cancel</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 text-center">
                                No active subscriptions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
            <div class="bg-zinc-800 px-4 py-3 border-t border-zinc-700">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
