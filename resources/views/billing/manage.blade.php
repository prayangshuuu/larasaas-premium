<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Subscription Status Card -->
            <div class="bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-800">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-white">Current Subscription</h3>
                            <p class="mt-1 text-sm text-zinc-400">
                                You are currently subscribed to the <span class="font-bold text-indigo-400">{{ $plan->name }}</span> plan.
                            </p>
                             <div class="mt-4 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                                <span class="text-sm text-zinc-500">
                                    Renews/Expires on {{ $subscription->current_period_end->format('F j, Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-4">
                            <!-- Stripe Portal Button -->
                             <a href="{{ route('billing.portal') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-zinc-700 hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Update Payment Method
                            </a>
                            <a href="{{ route('billing.plans') }}" class="inline-flex items-center px-4 py-2 border border-zinc-600 rounded-md shadow-sm text-sm font-medium text-white bg-transparent hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Change Plan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Limits -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div class="bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-800 p-6">
                    <h3 class="text-lg font-medium leading-6 text-white mb-4">Plan Usage</h3>
                    <div class="space-y-4">
                        @if(is_array($plan->features))
                            @foreach($plan->features as $key => $limit)
                                @php
                                    $usage = auth()->user()->getUsage($key);
                                    $percentage = ($limit > 0) ? min(100, ($usage / $limit) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm font-medium text-zinc-300 mb-1">
                                        <span>{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                        <span>{{ $usage }} / {{ $limit == -1 ? '∞' : $limit }}</span>
                                    </div>
                                    @if($limit != -1)
                                        <div class="w-full bg-zinc-700 rounded-full h-2.5">
                                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                 </div>

                <!-- Invoices -->
                <div class="bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-800 p-6">
                     <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium leading-6 text-white">Invoice History</h3>
                        <a href="{{ route('billing.invoices.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">View All</a>
                     </div>
                     
                     <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-zinc-700">
                             @forelse(auth()->user()->invoices()->latest()->take(5)->get() as $invoice)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white truncate">
                                                {{ $invoice->number }}
                                            </p>
                                            <p class="text-sm text-zinc-400 truncate">
                                                {{ $invoice->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ $invoice->invoice_pdf }}" target="_blank" class="text-zinc-400 hover:text-white">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                             @empty
                                <li class="py-4 text-center text-zinc-500 text-sm">No invoices found.</li>
                             @endforelse
                        </ul>
                     </div>
                </div>
            </div>

            <!-- Cancel Actions -->
             <div class="bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-800 mt-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-white">Cancel Subscription</h3>
                    <div class="mt-2 max-w-xl text-sm text-zinc-400">
                        <p>
                            Once you cancel, you will lose access to premium features at the end of your current billing period.
                        </p>
                    </div>
                    <div class="mt-5">
                        <form action="{{ route('billing.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Are you sure you want to cancel your subscription?')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                                Cancel Subscription
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
