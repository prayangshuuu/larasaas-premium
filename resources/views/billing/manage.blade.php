<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-black min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Status Card --}}
            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-3xl shadow-sm p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <svg class="w-32 h-32 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.15-1.46-3.27-3.4h1.96c.1 1.05 1.18 1.91 2.53 1.91 1.29 0 2.13-.86 2.13-1.9 0-1.35-1.07-1.96-2.6-2.43-1.67-.52-3.79-1.29-3.79-3.58 0-1.98 1.48-3.16 3.03-3.5V3h2.67v1.89c1.55.32 2.76 1.4 2.87 3h-2.02c-.11-1-.83-1.58-1.96-1.58-1.22 0-1.95.73-1.95 1.62 0 1.05 1.09 1.66 2.66 2.14 1.76.54 3.73 1.35 3.73 3.65 0 1.93-1.5 3.2-3.24 3.51z"/></svg>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Current Plan</h3>
                        <div class="flex items-center gap-3">
                            <span class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">
                                {{ $plan->name ?? 'Premium Plan' }}
                            </span>
                            
                            @if($subscription->status === 'canceled')
                                <span class="bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Canceled</span>
                            @elseif($subscription->status === 'active')
                                <span class="bg-green-500/10 text-green-500 border border-green-500/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Active</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @if($subscription->status === 'canceled')
                                Access until <strong>{{ $subscription->current_period_end->format('F j, Y') }}</strong>
                            @else
                                Renews on <strong>{{ $subscription->current_period_end ? $subscription->current_period_end->format('F j, Y') : 'N/A' }}</strong>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 w-full md:w-auto">
                        {{-- Billing Portal Button --}}
                        <a href="{{ route('billing.portal') }}" class="inline-flex items-center justify-center px-4 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-indigo-500/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Manage Cards & Billing
                        </a>
                        
                        @if($subscription->status === 'canceled')
                            <form method="POST" action="{{ route('billing.resume') }}">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-500/20">
                                    Resume Subscription
                                </button>
                            </form>
                        @elseif($subscription->status === 'active')
                            <form method="POST" action="{{ route('billing.cancel') }}" onsubmit="return confirm('Are you sure you want to cancel? You will keep access until the end of your billing cycle.');">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl font-semibold text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 hover:border-red-200 dark:hover:border-red-800 transition-all">
                                    Cancel Subscription
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Invoices & History --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Features (Left) --}}
                <div class="md:col-span-2 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-3xl p-8">
                     <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Plan Features</h3>
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($plan && (is_array($plan->features) || is_object($plan->features)))
                            @foreach ($plan->features as $feature)
                                <div class="flex items-center p-3 bg-gray-50 dark:bg-zinc-800/50 rounded-xl border border-gray-100 dark:border-zinc-800">
                                     <div class="p-2 bg-indigo-500/10 rounded-lg mr-3">
                                        <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                     </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                </div>
                            @endforeach
                        @endif
                     </div>
                </div>

                {{-- Invoice Link (Right) --}}
                <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-3xl p-8 flex flex-col justify-between">
                     <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Invoice History</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Download past invoices and view payment history.</p>
                     </div>
                     
                     <div class="mt-6">
                         <a href="{{ route('billing.invoices.index') }}" class="flex items-center justify-between w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-indigo-500 dark:hover:border-indigo-500 group transition-all">
                             <span class="font-medium text-gray-900 dark:text-white text-sm">View All Invoices</span>
                             <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                         </a>
                     </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
