@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    
    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ route('billing.plans') }}" class="text-sm text-zinc-400 hover:text-white flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Plans
        </a>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden">
        
        {{-- Header / Plan Summary --}}
        <div class="p-8 border-b border-zinc-800 bg-zinc-950/30">
            <h1 class="text-2xl font-bold text-white mb-2">Checkout</h1>
            <div class="flex items-center justify-between mt-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ $plan->name }} Plan</h2>
                    <p class="text-zinc-400 text-sm">Billed {{ $plan->interval }}ly</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-white">{{ $plan->currency }} {{ $plan->price }}</div>
                </div>
            </div>
        </div>

        <div class="p-8" x-data="{ method: '{{ $stripeEnabled ? 'stripe' : 'bkash' }}' }">
            
            {{-- Payment Method Selection --}}
            @if($stripeEnabled && $bkashEnabled)
                <div class="mb-8">
                    <label class="block text-sm font-medium text-zinc-300 mb-3">Select Payment Method</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" 
                                @click="method = 'stripe'"
                                class="relative flex items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all duration-200"
                                :class="method === 'stripe' ? 'border-indigo-600 bg-indigo-600/10 text-white' : 'border-zinc-800 bg-zinc-950 hover:bg-zinc-800 text-zinc-400 hover:text-white'">
                            @if(!empty($stripeLogo))
                                <img src="{{ $stripeLogo }}" alt="Stripe" class="h-8 object-contain">
                            @else
                                <span class="font-semibold">Stripe / Card</span>
                            @endif
                            <div x-show="method === 'stripe'" class="absolute -top-2 -right-2 bg-indigo-600 text-white rounded-full p-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        </button>

                        <button type="button" 
                                @click="method = 'bkash'"
                                class="relative flex items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all duration-200"
                                :class="method === 'bkash' ? 'border-pink-600 bg-pink-600/10 text-white' : 'border-zinc-800 bg-zinc-950 hover:bg-zinc-800 text-zinc-400 hover:text-white'">
                            @if(!empty($bkashLogo))
                                <img src="{{ $bkashLogo }}" alt="Bkash" class="h-8 object-contain">
                            @else
                                <span class="font-semibold">Bkash (Manual)</span>
                            @endif
                            <div x-show="method === 'bkash'" class="absolute -top-2 -right-2 bg-pink-600 text-white rounded-full p-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Stripe Form (Just a generic summary before redirect) --}}
            <div x-show="method === 'stripe'" x-transition:enter.duration.300ms>
                <div class="rounded-xl bg-zinc-950/50 p-6 border border-zinc-800 mb-6">
                    <p class="text-zinc-300 text-sm mb-4">
                        You will be redirected to Stripe's secure checkout page to complete your payment via Credit/Debit Card.
                    </p>
                    <div class="flex items-center gap-2 text-zinc-500 text-xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                        <span>Secure SSL Encryption</span>
                    </div>
                </div>

                <form action="{{ route('billing.payment.stripe', $plan) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                        Proceed to Pay {{ $plan->currency }} {{ $plan->price }}
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </form>
            </div>

            {{-- Bkash Manual Form --}}
            <div x-show="method === 'bkash'" x-transition:enter.duration.300ms style="display: none;">
                
                {{-- Instructions --}}
                <div class="rounded-xl bg-pink-500/10 p-6 border border-pink-500/20 mb-8">
                    <h3 class="text-pink-500 font-semibold mb-2">How to pay with Bkash</h3>
                    <ol class="list-decimal list-inside text-sm text-zinc-300 space-y-2">
                        <li>Go to your Bkash App or dial *247#</li>
                        <li>Choose "Send Money" option.</li>
                        <li>Enter Number: <span class="font-mono font-bold text-white selection:bg-pink-500 selection:text-white">{{ $bkashNumber ?? 'Not Configured' }}</span></li>
                        <li>Enter Amount: <span class="font-bold text-white">{{ $plan->price }}</span></li>
                        <li>Enter Reference: <span class="font-mono text-white">SUB{{ $plan->id }}</span></li>
                        <li>Complete the transaction and copy the <strong>Transaction ID</strong>.</li>
                        @if(!empty($bkashInstruction))
                            <li class="mt-2 text-pink-400 text-xs bg-pink-500/10 p-2 rounded border border-pink-500/20">
                                <strong>Note:</strong> {{ $bkashInstruction }}
                            </li>
                        @endif
                    </ol>
                </div>

                <form action="{{ route('billing.payment.bkash', $plan) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Your Bkash Number</label>
                        <x-ui.input type="text" name="sender_number" placeholder="01XXXXXXXXX" required />
                        <p class="text-xs text-zinc-500 mt-1">The number you sent money from.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Transaction ID (TrxID)</label>
                        <x-ui.input type="text" name="transaction_id" placeholder="8J7..." required />
                        <p class="text-xs text-zinc-500 mt-1">Found in the Bkash confirmation SMS.</p>
                    </div>
                    
                    {{-- Hidden Payment Date (auto-filled, or user input?)
                         Let's just use current date on backend, or ask user?
                         The controller validation asks for 'payment_date'. 
                         I'll add a date picker or just hidden today. 
                         Actually better to let user pick today, broadly speaking. --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Payment Date</label>
                        <x-ui.input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required />
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-lg bg-pink-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-pink-500 transition-colors">
                        Submit Payment Details
                    </button>
                </form>
            </div>

        </div>
    </div>
    
    <div class="mt-8 text-center">
        <p class="text-xs text-zinc-500">
            By subscribing, you agree to our Terms of Service and Privacy Policy.
        </p>
    </div>
</div>
@endsection
