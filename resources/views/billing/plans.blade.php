@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-12 py-8">
    
    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">Simple, transparent pricing</h1>
        <p class="mt-4 text-lg text-zinc-400">Choose the plan that best fits your needs. Upgrade or cancel anytime.</p>
    </div>

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-start">
        @foreach($plans as $plan)
            {{-- Plan Card --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 shadow-2xl flex flex-col relative overflow-hidden group hover:border-zinc-700 transition-all duration-300">
                
                {{-- Active/Current Badge --}}
                @if(isset($currentPlanId) && $currentPlanId == $plan->id)
                    <div class="absolute top-0 right-0 bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg shadow-sm">
                        CURRENT PLAN
                    </div>
                @endif
                
                {{-- Glow --}}
                <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

                <div class="mb-6 relative z-10">
                    <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                    <p class="text-sm text-zinc-400 mt-2 min-h-[40px]">
                        {{-- Description placeholder if plan has one, currently plan model doesn't have description field in migration, so generic text --}}
                        Unlock premium features to boost your IELTS score.
                    </p>
                </div>

                <div class="mb-8 relative z-10">
                    <span class="text-4xl font-bold text-white">{{ $plan->currency }} {{ $plan->price }}</span>
                    <span class="text-zinc-500 font-medium">/ {{ $plan->interval }}</span>
                </div>

                <ul class="space-y-4 mb-8 flex-1 relative z-10">
                    @if($plan->features)
                        @foreach(is_string($plan->features) ? json_decode($plan->features, true) ?? [] : $plan->features as $feature)
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-emerald-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-zinc-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                    @else
                        <li class="text-sm text-zinc-500 italic">Standard features included.</li>
                    @endif
                </ul>

                <div class="relative z-10">
                    @if(isset($currentPlanId) && $currentPlanId == $plan->id)
                        <button disabled class="w-full inline-flex justify-center items-center rounded-lg bg-zinc-800 px-4 py-3 text-sm font-semibold text-zinc-400 cursor-not-allowed border border-zinc-700">
                           Current Plan
                        </button>
                    @else
                        <a href="{{ route('billing.checkout', $plan) }}" class="w-full inline-flex justify-center items-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-indigo-500">
                           Subscribe
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12 text-center">
        <p class="text-sm text-zinc-500">
            Secure payments powered by Stripe. You can cancel at any time.
        </p>
    </div>
</div>
@endsection
