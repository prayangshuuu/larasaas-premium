@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Outgoing Webhooks</h1>
            <p class="mt-1 text-sm text-zinc-400">Receive real-time HTTP callbacks when events occur in your account.</p>
        </div>
        <a href="{{ route('webhooks.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-900/30 hover:shadow-indigo-900/50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Endpoint
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3.5 rounded-xl text-sm font-medium" role="alert">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Webhooks List --}}
    @if($webhooks->isEmpty())
        <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-16 text-center">
            <div class="mx-auto w-16 h-16 bg-zinc-800 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">No webhooks configured</h3>
            <p class="text-sm text-zinc-500 mb-6 max-w-md mx-auto">Create your first webhook endpoint to start receiving real-time event notifications.</p>
            <a href="{{ route('webhooks.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Webhook
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($webhooks as $webhook)
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl overflow-hidden hover:border-zinc-700 transition-colors group">
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Icon + Name --}}
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center {{ $webhook->is_active ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-zinc-800 border border-zinc-700' }}">
                                    <svg class="w-5 h-5 {{ $webhook->is_active ? 'text-emerald-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-semibold text-white truncate">{{ $webhook->name }}</h3>
                                    <p class="text-xs font-mono text-zinc-500 truncate mt-0.5">{{ $webhook->url }}</p>
                                </div>
                            </div>

                            {{-- Status + Meta --}}
                            <div class="flex items-center gap-4 shrink-0">
                                <div class="flex items-center gap-3">
                                    {{-- Events count --}}
                                    <span class="hidden sm:inline-flex items-center gap-1.5 text-xs text-zinc-400 bg-zinc-800 border border-zinc-700 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        {{ count($webhook->events) }} event{{ count($webhook->events) !== 1 ? 's' : '' }}
                                    </span>

                                    {{-- Status badge --}}
                                    @if($webhook->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('webhooks.show', $webhook) }}"
                                       class="p-2 rounded-lg text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all"
                                       title="View History">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                    <form action="{{ route('webhooks.destroy', $webhook) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this webhook?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-lg text-zinc-400 hover:text-red-400 hover:bg-red-500/10 transition-all"
                                                title="Delete Webhook">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
