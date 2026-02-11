@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('webhooks.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-white transition-colors mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Webhooks
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ $webhook->name }}</h1>
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
        </div>
        <form action="{{ route('webhooks.destroy', $webhook) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this webhook?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 text-sm font-medium rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </form>
    </div>

    <div class="space-y-6">

        {{-- Configuration Card --}}
        <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Configuration</h3>
            </div>
            <div class="p-6 space-y-5">
                {{-- Payload URL --}}
                <div>
                    <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Payload URL</span>
                    <div class="flex items-center gap-3 bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span class="text-sm font-mono text-zinc-300 break-all">{{ $webhook->url }}</span>
                    </div>
                </div>

                {{-- Secret --}}
                <div x-data="{ revealed: false }">
                    <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Signing Secret</span>
                    <div class="flex items-center gap-3 bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        <span class="text-sm font-mono text-zinc-300 break-all flex-1" x-text="revealed ? '{{ $webhook->secret }}' : '••••••••••••••••••••••••••••••••'"></span>
                        <button @click="revealed = !revealed" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium transition-colors shrink-0" x-text="revealed ? 'Hide' : 'Reveal'"></button>
                    </div>
                </div>

                {{-- Subscribed Events --}}
                <div>
                    <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Subscribed Events</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($webhook->events as $event)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-mono font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ $event }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery History Card --}}
        <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Recent Deliveries</h3>
                @if(!$deliveries->isEmpty())
                    <span class="text-xs text-zinc-500">{{ $deliveries->total() }} total</span>
                @endif
            </div>

            @if($deliveries->isEmpty())
                <div class="p-12 text-center">
                    <div class="mx-auto w-12 h-12 bg-zinc-800 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm text-zinc-500">No deliveries yet. Waiting for events to trigger...</p>
                </div>
            @else
                <div class="divide-y divide-zinc-800">
                    @foreach($deliveries as $delivery)
                        <div x-data="{ open: false }" class="group">
                            {{-- Delivery Row --}}
                            <button @click="open = !open" class="w-full text-left px-6 py-4 flex items-center justify-between hover:bg-zinc-800/50 transition-colors">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    {{-- Status dot --}}
                                    @if($delivery->response_status >= 200 && $delivery->response_status < 300)
                                        <span class="shrink-0 w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @else
                                        <span class="shrink-0 w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </span>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-mono font-bold {{ ($delivery->response_status >= 200 && $delivery->response_status < 300) ? 'text-emerald-400' : 'text-red-400' }}">
                                                {{ $delivery->response_status ?? 'ERR' }}
                                            </span>
                                            <span class="text-sm font-mono text-zinc-300 truncate">{{ $delivery->event }}</span>
                                        </div>
                                        <span class="text-xs text-zinc-500 mt-0.5">{{ $delivery->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <svg class="w-4 h-4 text-zinc-500 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Expandable Payload/Response --}}
                            <div x-show="open" x-cloak x-transition.opacity.duration.200ms class="border-t border-zinc-800/50 bg-zinc-950">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-0 md:divide-x md:divide-zinc-800">
                                    <div class="p-5">
                                        <h4 class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3">Request Payload</h4>
                                        <pre class="text-xs text-indigo-400 font-mono whitespace-pre-wrap bg-zinc-900 border border-zinc-800 rounded-lg p-3 max-h-48 overflow-auto scrollbar-thin scrollbar-thumb-zinc-800 scrollbar-track-transparent">{{ json_encode($delivery->payload, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                    <div class="p-5 border-t border-zinc-800/50 md:border-t-0">
                                        <h4 class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3">Response Body</h4>
                                        <pre class="text-xs text-emerald-400 font-mono whitespace-pre-wrap bg-zinc-900 border border-zinc-800 rounded-lg p-3 max-h-48 overflow-auto scrollbar-thin scrollbar-thumb-zinc-800 scrollbar-track-transparent">{{ Str::limit($delivery->response_body, 1000) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($deliveries->hasPages())
                    <div class="px-6 py-4 border-t border-zinc-800">
                        {{ $deliveries->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>
@endsection
