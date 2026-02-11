@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-8">
        <a href="{{ route('webhooks.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-white transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Webhooks
        </a>
        <h1 class="text-3xl font-bold text-white tracking-tight">Add Webhook Endpoint</h1>
        <p class="mt-1 text-sm text-zinc-400">Configure a new URL to receive event notifications.</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl overflow-hidden">
        <form action="{{ route('webhooks.store') }}" method="POST" class="divide-y divide-zinc-800">
            @csrf

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Name / Description --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-white mb-2">Description</label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name') }}"
                           class="w-full bg-zinc-900 border border-zinc-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 rounded-xl px-4 py-3 text-sm text-white placeholder:text-zinc-600 transition-all"
                           placeholder="e.g. Production Receiver">
                    @error('name')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Payload URL --}}
                <div>
                    <label for="url" class="block text-sm font-semibold text-white mb-2">Payload URL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <input type="url" name="url" id="url" required
                               value="{{ old('url') }}"
                               class="w-full bg-zinc-900 border border-zinc-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 rounded-xl pl-11 pr-4 py-3 text-sm text-white placeholder:text-zinc-600 font-mono transition-all"
                               placeholder="https://api.myapp.com/webhooks/listener">
                    </div>
                    @error('url')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Events (Toggles) --}}
                <div>
                    <label class="block text-sm font-semibold text-white mb-1">Events to Subscribe</label>
                    <p class="text-xs text-zinc-500 mb-4">Choose which events will trigger a webhook delivery to your URL.</p>

                    @error('events')
                        <p class="mb-3 text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="space-y-3">
                        @php
                            $availableEvents = [
                                'ticket.updated' => [
                                    'label' => 'Ticket Updated',
                                    'desc'  => 'Triggered when a support ticket status changes.',
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>',
                                ],
                                'invoice.paid' => [
                                    'label' => 'Invoice Paid',
                                    'desc'  => 'Triggered when an invoice payment is successfully processed.',
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/>',
                                ],
                            ];
                        @endphp

                        @foreach($availableEvents as $eventValue => $eventMeta)
                            <div x-data="{ on: {{ collect(old('events', []))->contains($eventValue) ? 'true' : 'false' }} }"
                                 @click="on = !on"
                                 class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-xl cursor-pointer hover:border-zinc-700 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center group-hover:border-zinc-600 transition-colors">
                                        <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $eventMeta['icon'] !!}</svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-medium text-white">{{ $eventMeta['label'] }}</span>
                                        <span class="block text-xs text-zinc-500 mt-0.5">{{ $eventMeta['desc'] }}</span>
                                    </div>
                                </div>

                                {{-- Toggle Switch --}}
                                <div class="relative shrink-0 ml-4">
                                    <input type="checkbox" name="events[]" value="{{ $eventValue }}" class="hidden" :checked="on" x-ref="cb_{{ $loop->index }}">
                                    <div class="w-11 h-6 rounded-full transition-colors duration-200"
                                         :class="on ? 'bg-indigo-600' : 'bg-zinc-700'">
                                    </div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-200 pointer-events-none"
                                         :class="on ? 'translate-x-5' : 'translate-x-0'">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 sm:px-8 py-4 bg-zinc-900/30">
                <a href="{{ route('webhooks.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-zinc-400 hover:text-white rounded-xl hover:bg-zinc-800 transition-all">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-900/30 hover:shadow-indigo-900/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Webhook
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
