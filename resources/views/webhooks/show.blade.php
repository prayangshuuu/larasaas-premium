<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Webhook Details') }}
            </h2>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $webhook->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $webhook->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Webhook Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Configuration</h3>
                            <dl class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <div class="mt-2">
                                    <dt class="font-medium text-gray-500">Payload URL</dt>
                                    <dd class="mt-1 font-mono break-all">{{ $webhook->url }}</dd>
                                </div>
                                <div class="mt-2">
                                    <dt class="font-medium text-gray-500">Secret</dt>
                                    <dd class="mt-1 font-mono break-all">{{ $webhook->secret }}</dd>
                                </div>
                                <div class="mt-2">
                                    <dt class="font-medium text-gray-500">Events</dt>
                                    <dd class="mt-1">
                                        @foreach($webhook->events as $event)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-2">
                                                {{ $event }}
                                            </span>
                                        @endforeach
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deliveries -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Deliveries</h3>
                    
                    @if($deliveries->isEmpty())
                        <p class="text-gray-500 text-sm">No deliveries yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($deliveries as $delivery)
                                <div x-data="{ open: false }" class="border border-gray-700 rounded-lg overflow-hidden">
                                    <div @click="open = !open" class="bg-gray-700 px-4 py-3 cursor-pointer hover:bg-gray-600 flex justify-between items-center transition">
                                        <div class="flex items-center space-x-4">
                                            @if($delivery->response_status >= 200 && $delivery->response_status < 300)
                                                <span class="flex-shrink-0 h-2 w-2 rounded-full bg-green-400" aria-hidden="true"></span>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $delivery->response_status }} OK
                                                </span>
                                            @else
                                                <span class="flex-shrink-0 h-2 w-2 rounded-full bg-red-400" aria-hidden="true"></span>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    {{ $delivery->response_status ?? 'ERR' }}
                                                </span>
                                            @endif
                                            
                                            <span class="text-xs font-mono text-gray-300">{{ $delivery->event }}</span>
                                            <span class="text-xs text-gray-400">{{ $delivery->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="text-gray-400">
                                            <svg :class="{'rotate-180': open}" class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div x-show="open" x-cloak class="bg-gray-900 p-4 border-t border-gray-700 text-sm font-mono text-gray-300">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <h4 class="font-bold text-gray-500 mb-1">Request Payload</h4>
                                                <pre class="whitespace-pre-wrap text-xs bg-black p-2 rounded">{{ json_encode($delivery->payload, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-500 mb-1">Response Body</h4>
                                                <pre class="whitespace-pre-wrap text-xs bg-black p-2 rounded">{{ Str::limit($delivery->response_body, 1000) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $deliveries->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
