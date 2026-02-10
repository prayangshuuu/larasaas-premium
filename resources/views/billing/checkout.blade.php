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

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden"
         x-data="checkoutCoupon()"
         x-init="init()">
        
        {{-- Header / Plan Summary --}}
        <div class="p-8 border-b border-zinc-800 bg-zinc-950/30">
            <h1 class="text-2xl font-bold text-white mb-2">Checkout</h1>
            <div class="flex items-start justify-between mt-4">
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold text-white">{{ $plan->name }}</h2>
                    @if($plan->description)
                        <p class="text-zinc-400 text-sm">{{ $plan->description }}</p>
                    @endif
                    @if(!empty($plan->features) && is_array($plan->features))
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($plan->features as $feature)
                                <span class="inline-flex items-center gap-1 text-xs text-zinc-400 bg-zinc-800 rounded-full px-2.5 py-1">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ $feature }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <p class="text-zinc-500 text-xs pt-1">Billed {{ $plan->interval }}ly</p>
                </div>
                <div class="text-right shrink-0 ml-4">
                    <div class="text-3xl font-bold text-white">{{ $plan->currency }} {{ $plan->price }}</div>
                </div>
            </div>
        </div>

        <div class="p-8 space-y-8">

            {{-- ═══════════════════════════════════════════ --}}
            {{-- COUPON SECTION --}}
            {{-- ═══════════════════════════════════════════ --}}
            @if(!empty($couponEnabled))
            <div class="rounded-xl border border-zinc-800 bg-zinc-950/40 p-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" /></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-white">Coupon Code</h3>
                </div>
                <p class="text-xs text-zinc-400 mb-4">Have a coupon code? You can apply it during checkout!</p>
                
                <div class="flex gap-3">
                    <input type="text" 
                           x-model="couponCode" 
                           placeholder="Enter coupon code"
                           :disabled="couponApplied"
                           class="flex-1 rounded-lg bg-zinc-950 border border-zinc-800 text-white px-4 py-2.5 text-sm placeholder:text-zinc-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-colors uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed"
                           @keydown.enter.prevent="applyCoupon()" />
                    
                    <template x-if="!couponApplied">
                        <button @click="applyCoupon()" 
                                :disabled="loading || !couponCode.trim()"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                            <template x-if="loading">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            Apply
                        </button>
                    </template>
                    <template x-if="couponApplied">
                        <button @click="removeCoupon()" 
                                class="inline-flex items-center gap-2 rounded-lg bg-zinc-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-600 transition-all">
                            Remove
                        </button>
                    </template>
                </div>

                {{-- Success / Error Messages --}}
                <div x-show="successMessage" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 flex items-center gap-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-4 py-2.5">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span class="text-sm text-emerald-400" x-text="successMessage"></span>
                </div>
                <div x-show="errorMessage" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 flex items-center gap-2 rounded-lg bg-red-500/10 border border-red-500/20 px-4 py-2.5">
                    <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    <span class="text-sm text-red-400" x-text="errorMessage"></span>
                </div>
            </div>
            @endif

            {{-- ═══════════════════════════════════════════ --}}
            {{-- ORDER SUMMARY --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="rounded-xl border border-zinc-800 bg-zinc-950/40 p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Order Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-400">Subtotal</span>
                        <span class="text-zinc-300">{{ $plan->currency }} {{ $plan->price }}</span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="couponApplied" x-transition>
                        <span class="text-emerald-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" /></svg>
                            Discount
                            <span class="text-xs bg-emerald-500/10 text-emerald-400 px-1.5 py-0.5 rounded-md font-mono" x-text="appliedCode"></span>
                        </span>
                        <span class="text-emerald-400" x-text="'-{{ $plan->currency }} ' + discountDisplay"></span>
                    </div>
                    <div class="border-t border-zinc-800 pt-3 flex justify-between">
                        <span class="text-sm font-semibold text-white">Total</span>
                        <span class="text-lg font-bold text-white" x-text="'{{ $plan->currency }} ' + totalDisplay">{{ $plan->currency }} {{ $plan->price }}</span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- PAYMENT METHOD SELECTION --}}
            {{-- ═══════════════════════════════════════════ --}}
            @php
                $defaultMethod = $stripeEnabled ? 'stripe' : ($bkashEnabled ? 'bkash' : 'none');
            @endphp

            <div x-data="{ method: '{{ $defaultMethod }}' }">
            
            {{-- Payment Method Selection (only when both enabled) --}}
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
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
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
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span class="font-semibold">Bkash (Manual)</span>
                            @endif
                            <div x-show="method === 'bkash'" class="absolute -top-2 -right-2 bg-pink-600 text-white rounded-full p-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Stripe Form --}}
            @if($stripeEnabled)
            <div x-show="method === 'stripe'" x-transition:enter.duration.300ms>
                <div class="rounded-xl bg-zinc-950/50 p-6 border border-zinc-800 mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-white">Credit / Debit Card via Stripe</h3>
                    </div>
                    <p class="text-zinc-300 text-sm mb-4">
                        You will be redirected to Stripe's secure checkout page to complete your payment.
                    </p>
                    <div class="flex items-center gap-2 text-zinc-500 text-xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                        <span>Secure SSL Encryption · Powered by Stripe</span>
                    </div>
                </div>

                <form action="{{ route('billing.payment.stripe') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $plan->slug }}">
                    <input type="hidden" name="coupon_code" :value="couponApplied ? appliedCode : ''">
                    <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                        <span x-text="'Proceed to Pay {{ $plan->currency }} ' + totalDisplay">Proceed to Pay {{ $plan->currency }} {{ $plan->price }}</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </form>
            </div>
            @endif

            {{-- Bkash Manual Form --}}
            @if($bkashEnabled)
            <div x-show="method === 'bkash'" x-transition:enter.duration.300ms @if($stripeEnabled) style="display: none;" @endif>
                
                {{-- Instructions --}}
                <div class="rounded-xl bg-pink-500/10 p-6 border border-pink-500/20 mb-8">
                    <h3 class="text-pink-500 font-semibold mb-2">How to pay with Bkash</h3>
                    <ol class="list-decimal list-inside text-sm text-zinc-300 space-y-2">
                        <li>Go to your Bkash App or dial *247#</li>
                        <li>Choose "Send Money" option.</li>
                        <li>Enter Number: <span class="font-mono font-bold text-white selection:bg-pink-500 selection:text-white">{{ $bkashNumber ?? 'Not Configured' }}</span></li>
                        <li>Enter Amount: <span class="font-bold text-white" x-text="totalDisplay">{{ $plan->price }}</span></li>
                        <li>Enter Reference: <span class="font-mono text-white">SUB{{ $plan->id }}</span></li>
                        <li>Complete the transaction and copy the <strong>Transaction ID</strong>.</li>
                        @if(!empty($bkashInstruction))
                            <li class="mt-2 text-pink-400 text-xs bg-pink-500/10 p-2 rounded border border-pink-500/20">
                                <strong>Note:</strong> {{ $bkashInstruction }}
                            </li>
                        @endif
                    </ol>
                </div>

                <form action="{{ route('billing.payment.bkash') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $plan->slug }}">
                    <input type="hidden" name="coupon_code" :value="couponApplied ? appliedCode : ''">
                    
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Your Bkash Number</label>
                        <input type="text" name="sender_number" placeholder="01XXXXXXXXX" required
                               class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-4 py-2.5 text-sm placeholder:text-zinc-600 focus:border-pink-500 focus:ring-1 focus:ring-pink-500 focus:outline-none transition-colors" />
                        <p class="text-xs text-zinc-500 mt-1">The number you sent money from.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Transaction ID (TrxID)</label>
                        <input type="text" name="transaction_id" placeholder="8J7..." required
                               class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-4 py-2.5 text-sm placeholder:text-zinc-600 focus:border-pink-500 focus:ring-1 focus:ring-pink-500 focus:outline-none transition-colors" />
                        <p class="text-xs text-zinc-500 mt-1">Found in the Bkash confirmation SMS.</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Payment Date</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                               class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-4 py-2.5 text-sm focus:border-pink-500 focus:ring-1 focus:ring-pink-500 focus:outline-none transition-colors" />
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-lg bg-pink-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-pink-500 transition-colors">
                        Submit Payment Details
                    </button>
                </form>
            </div>
            @endif

            {{-- No gateway --}}
            @if(!$stripeEnabled && !$bkashEnabled)
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-800 mb-4">
                        <svg class="w-6 h-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-white">No Payment Methods Available</h3>
                    <p class="mt-2 text-sm text-zinc-500">No payment gateways are currently configured. Please contact support.</p>
                </div>
            @endif

            </div> {{-- end x-data method --}}
        </div>
    </div>
    
    {{-- Footer --}}
    <div class="mt-8 text-center space-y-2">
        <div class="flex items-center justify-center gap-2 text-zinc-500">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            <p class="text-xs">Secure payments powered by Stripe. You can cancel at any time.</p>
        </div>
        <p class="text-xs text-zinc-600">
            By subscribing, you agree to our Terms of Service and Privacy Policy.
        </p>
    </div>

</div>

<script>
function checkoutCoupon() {
    return {
        couponCode: '',
        loading: false,
        couponApplied: false,
        appliedCode: '',
        successMessage: '',
        errorMessage: '',
        discountAmount: 0,
        newTotal: {{ $plan->price }},
        planPrice: {{ $plan->price }},

        get discountDisplay() {
            return this.discountAmount.toFixed(2);
        },

        get totalDisplay() {
            return this.newTotal.toFixed(2);
        },

        init() {
            this.newTotal = this.planPrice;
        },

        async applyCoupon() {
            if (!this.couponCode.trim() || this.loading) return;
            
            this.loading = true;
            this.successMessage = '';
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("billing.payment.check-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ coupon_code: this.couponCode.trim(), plan: '{{ $plan->slug }}' }),
                });

                const data = await response.json();

                if (response.ok && data.valid) {
                    this.couponApplied = true;
                    this.appliedCode = data.coupon_code;
                    this.discountAmount = parseFloat(data.discount_amount);
                    this.newTotal = parseFloat(data.new_total);
                    this.successMessage = data.message;
                    this.errorMessage = '';
                } else {
                    this.errorMessage = data.message || 'Invalid coupon code.';
                    this.successMessage = '';
                }
            } catch (err) {
                this.errorMessage = 'Something went wrong. Please try again.';
                this.successMessage = '';
            } finally {
                this.loading = false;
            }
        },

        removeCoupon() {
            this.couponApplied = false;
            this.appliedCode = '';
            this.couponCode = '';
            this.discountAmount = 0;
            this.newTotal = this.planPrice;
            this.successMessage = '';
            this.errorMessage = '';
        }
    };
}
</script>
@endsection
