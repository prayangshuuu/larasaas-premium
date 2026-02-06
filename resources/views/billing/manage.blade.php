<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Current Plan Status --}}
            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Current Plan</h3>
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-indigo-500">{{ $plan->name ?? 'Premium Plan' }}</span>
                            @if($subscription->onGracePeriod())
                                <span class="bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 px-2.5 py-0.5 rounded-full text-xs font-bold">Canceled (Grace Period)</span>
                            @elseif($subscription->active())
                                <span class="bg-green-500/10 text-green-500 border border-green-500/20 px-2.5 py-0.5 rounded-full text-xs font-bold">Active</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            @if($subscription->onGracePeriod())
                                Access until <strong>{{ $subscription->ends_at->format('F j, Y') }}</strong>
                            @else
                                Next billing date: <strong>{{ $subscription->asStripeSubscription()->current_period_end ? \Carbon\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->format('F j, Y') : 'N/A' }}</strong>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col gap-3">
                         <a href="{{ route('billing.invoices.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150">
                            View Invoices
                        </a>
                        
                        @if($subscription->onGracePeriod())
                            <form method="POST" action="{{ route('billing.resume') }}">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150">
                                    Resume Subscription
                                </button>
                            </form>
                        @elseif(!$subscription->cancelled())
                            <form method="POST" action="{{ route('billing.cancel') }}" onsubmit="return confirm('Are you sure you want to cancel? You will keep access until the end of your billing cycle.');">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150">
                                    Cancel Subscription
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Plan Features Summary (Optional) --}}
            @if($plan && (is_array($plan->features) || is_object($plan->features)))
            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Plan Features</h3>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($plan->features as $feature)
                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                             <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
