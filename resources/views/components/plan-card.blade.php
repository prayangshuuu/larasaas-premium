@props(['plan'])

<div class="rounded-3xl p-8 ring-1 {{ $plan->is_featured ? 'ring-indigo-500 bg-zinc-900/70 shadow-[0_0_30px_rgba(79,70,229,0.15)]' : 'ring-zinc-800 bg-zinc-900/50' }} xl:p-10 hover:ring-indigo-500 transition-all duration-300 flex flex-col h-full relative overflow-hidden group">
    {{-- Featured Glow --}}
    @if($plan->is_featured)
        <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/10 to-transparent pointer-events-none"></div>
    @endif

    <div class="flex items-center gap-4 relative z-10">
        {{-- Plan Logo --}}
        @if($plan->logo)
            <img src="{{ asset('storage/' . $plan->logo) }}" alt="{{ $plan->name }}" class="h-12 w-12 rounded-xl object-cover border border-zinc-700 bg-zinc-800 shrink-0" />
        @else
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        @endif
        <div class="flex-1">
            <div class="flex items-center justify-between gap-x-4">
                <h3 id="tier-{{ $plan->slug }}" class="text-lg font-semibold leading-8 text-white">{{ $plan->name }}</h3>
                @if($plan->is_featured)
                    <div class="rounded-full bg-indigo-500/10 px-2.5 py-1 text-xs font-semibold leading-5 text-indigo-400 border border-indigo-500/20">Most popular</div>
                @endif
            </div>
        </div>
    </div>

    <p class="mt-4 text-sm leading-6 text-zinc-400 relative z-10">{{ $plan->description ?? 'Unlock full access to all features.' }}</p>
    <div class="mt-6 flex items-baseline gap-x-1 relative z-10">
        <span class="text-4xl font-bold tracking-tight text-white">${{ $plan->price }}</span>
        <span class="text-sm font-semibold leading-6 text-zinc-400">/{{ $plan->interval }}</span>
    </div>
    <a href="{{ route('billing.checkout', ['plan' => $plan->slug]) }}"
       aria-describedby="tier-{{ $plan->slug }}"
       class="mt-6 block rounded-md py-2 px-3 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 relative z-10 {{ $plan->is_featured ? 'bg-indigo-600 text-white hover:bg-indigo-500 shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
        Subscribe now
    </a>
    <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-zinc-400 xl:mt-10 flex-1 relative z-10">
        @if(is_array($plan->features))
            @foreach($plan->features as $feature)
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                    {{ $feature }}
                </li>
            @endforeach
        @endif
    </ul>
</div>
