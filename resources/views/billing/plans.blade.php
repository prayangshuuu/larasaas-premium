<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-white">
                    Choose Your Plan
                </h2>
                <p class="mt-4 text-xl text-zinc-400">
                    Unlock the full potential of your IELTS preparation with premium features.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($plans as $plan)
                    <div class="bg-zinc-900 rounded-2xl border border-zinc-800 p-8 flex flex-col hover:border-indigo-500 transition-colors duration-300">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                            <div class="mt-4 flex items-baseline text-white">
                                <span class="text-4xl font-extrabold tracking-tight">${{ number_format($plan->price, 2) }}</span>
                                <span class="ml-1 text-xl font-medium text-zinc-400">/{{ $plan->interval }}</span>
                            </div>
                        </div>
                        
                        <p class="mt-2 text-zinc-400 text-sm mb-6">
                            Perfect for {{ $plan->name }} preparation.
                        </p>

                        <ul role="list" class="space-y-4 mb-8 flex-1">
                            @if(is_array($plan->features))
                                @foreach($plan->features as $key => $limit)
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <!-- Check icon -->
                                            <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-base text-zinc-300">
                                            {{ ucwords(str_replace('_', ' ', $key)) }}: <span class="font-semibold text-white">{{ $limit === -1 ? 'Unlimited' : $limit }}</span>
                                        </p>
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                        <a href="{{ route('billing.checkout', $plan) }}"
                           class="w-full block bg-indigo-600 border border-transparent rounded-md py-3 text-center text-base font-medium text-white hover:bg-indigo-700 transition duration-150 ease-in-out">
                            Subscribe to {{ $plan->name }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
