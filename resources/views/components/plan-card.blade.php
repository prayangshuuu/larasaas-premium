@props(['plan'])

<div class="rounded-3xl p-8 ring-1 ring-zinc-800 bg-zinc-900/50 xl:p-10 hover:ring-indigo-500 transition-all duration-300 flex flex-col h-full">
    <div class="flex items-center justify-between gap-x-4">
        <h3 id="tier-{{ $plan->slug }}" class="text-lg font-semibold leading-8 text-white">{{ $plan->name }}</h3>
        @if($plan->is_featured)
            <div class="rounded-full bg-indigo-500/10 px-2.5 py-1 text-xs font-semibold leading-5 text-indigo-400">Most popular</div>
        @endif
    </div>
    <p class="mt-4 text-sm leading-6 text-zinc-400">{{ $plan->description ?? 'Unlock full access to all features.' }}</p>
    <div class="mt-6 flex items-baseline gap-x-1">
        <span class="text-4xl font-bold tracking-tight text-white">${{ $plan->price }}</span>
        <span class="text-sm font-semibold leading-6 text-zinc-400">/{{ $plan->interval }}</span>
    </div>
    <a href="{{ route('billing.checkout', ['plan' => $plan->slug]) }}"
       aria-describedby="tier-{{ $plan->slug }}"
       class="mt-6 block rounded-md py-2 px-3 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 {{ $plan->is_featured ? 'bg-indigo-600 text-white hover:bg-indigo-500 shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
        Subscribe now
    </a>
    <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-zinc-400 xl:mt-10 flex-1">
        @if(is_array($plan->features))
            @foreach($plan->features as $feature)
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                    {{ $feature }}
                </li>
            @endforeach
        @endif
    </ul>
</div>
