@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Billing & Subscription</h1>
            <p class="text-sm text-zinc-400 mt-1">Manage your plan, payment methods, and view invoices.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('billing.plans') }}" class="inline-flex items-center justify-center rounded-lg bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 hover:text-indigo-400 transition-all border border-zinc-700">
                View Plans
            </a>
            @if($subscription)
            <a href="{{ route('billing.portal') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all">
                Update Payment Method
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-emerald-500/10 p-4 border border-emerald-500/20">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 text-sm text-emerald-400">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Subscription Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent pointer-events-none"></div>
                
                <h2 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    Current Subscription
                </h2>

                @if($subscription)
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="bg-zinc-950/50 p-4 rounded-lg border border-zinc-800">
                                <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Plan</label>
                                <div class="text-xl font-bold text-white">{{ $plan->name ?? 'Unknown Plan' }}</div>
                                <div class="text-sm text-zinc-400 mt-1">
                                    {{ $plan->currency }} {{ $plan->price }} / {{ $plan->interval }}
                                </div>
                            </div>
                            <div class="bg-zinc-950/50 p-4 rounded-lg border border-zinc-800">
                                <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Status</label>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-sm font-medium
                                        @if($subscription->status === 'active') bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20
                                        @elseif($subscription->status === 'canceled') bg-red-500/10 text-red-400 ring-1 ring-red-500/20
                                        @else bg-yellow-500/10 text-yellow-400 ring-1 ring-yellow-500/20 @endif">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </div>
                                <div class="text-sm text-zinc-400 mt-1">
                                    @if($subscription->status === 'active')
                                        Renews on {{ $subscription->current_period_end ? $subscription->current_period_end->format('M d, Y') : 'N/A' }}
                                    @elseif($subscription->status === 'canceled')
                                        Ends on {{ $subscription->current_period_end ? $subscription->current_period_end->format('M d, Y') : 'N/A' }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 pt-4 border-t border-zinc-800">
                            <a href="{{ route('billing.plans') }}" class="text-sm text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                                Change Plan &rarr;
                            </a>
                            
                            @if($subscription->status === 'active' && !$subscription->onGracePeriod())
                                <form action="{{ route('billing.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel? You will keep access until the end of the billing period.');">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-500 hover:text-red-400 font-medium transition-colors">
                                        Cancel Subscription
                                    </button>
                                </form>
                            @elseif($subscription->onGracePeriod())
                                <form action="{{ route('billing.resume') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm text-emerald-500 hover:text-emerald-400 font-medium transition-colors">
                                        Resume Subscription
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-800 mb-4">
                            <svg class="w-6 h-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </div>
                        <h3 class="text-lg font-medium text-white">No active subscription</h3>
                        <p class="mt-2 text-sm text-zinc-500">You are currently on the free tier. Upgrade to unlock more features.</p>
                        <div class="mt-6">
                            <a href="{{ route('billing.plans') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all">
                                View Plans
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Invoice History --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-zinc-800">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Invoice History
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-800 text-left">
                        <thead class="bg-zinc-950/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider text-right">Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800 bg-zinc-900">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                        {{ $invoice->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                        {{ $invoice->amount }} <span class="text-zinc-500 text-xs uppercase">{{ $invoice->currency ?? 'USD' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($invoice->status === 'paid')
                                            <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-2 py-1 text-xs font-medium text-emerald-400 ring-1 ring-inset ring-emerald-400/20">Paid</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-zinc-400/10 px-2 py-1 text-xs font-medium text-zinc-400 ring-1 ring-inset ring-zinc-400/20">{{ ucfirst($invoice->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        @if($invoice->invoice_pdf_url)
                                            <a href="{{ $invoice->invoice_pdf_url }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 font-medium inline-flex items-center gap-1">
                                                Download
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                            </a>
                                        @else
                                            <span class="text-zinc-600 italic">Unavailable</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-zinc-500">
                                        No invoices found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column (Optional: Features, Stats, or FAQs) --}}
        <div class="space-y-6">
             <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-xl">
                 <h3 class="text-base font-semibold text-white mb-4">Payment Methods</h3>
                 <p class="text-sm text-zinc-400 mb-6">Manage your saved cards and billing details securely via Stripe.</p>
                 <a href="{{ route('billing.portal') }}" class="w-full inline-flex items-center justify-center rounded-lg bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-700 hover:text-indigo-400 transition-colors border border-zinc-700">
                     Manage in Stripe Portal
                 </a>
             </div>
        </div>
    </div>
</div>
@endsection
