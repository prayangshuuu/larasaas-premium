<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upgrade Plan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Choose Your Plan</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">Unlock premium features and boost your IELTS score.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($plans as $plan)
                <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-xl flex flex-col relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    
                    {{-- Gradient Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

                    <div class="p-8 flex-1">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h3>
                        <div class="flex items-baseline mb-6">
                            <span class="text-4xl font-extrabold text-gray-900 dark:text-white">${{ number_format($plan->price, 0) }}</span>
                            <span class="ml-2 text-gray-500 dark:text-gray-400">/ {{ $plan->billing_interval }}</span>
                        </div>

                        {{-- Features --}}
                        <ul role="list" class="space-y-4 mb-6">
                            @if(is_array($plan->features) || is_object($plan->features))
                                @foreach ($plan->features as $feature)
                                    <li class="flex items-start">
                                        <svg class="h-6 w-6 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="ml-3 text-gray-700 dark:text-gray-300 text-sm">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <div class="p-8 border-t border-gray-100 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800/50">
                        <a href="{{ route('billing.checkout', $plan) }}" 
                           class="block w-full py-3 px-6 text-center rounded-xl shadow-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition-all hover:shadow-indigo-500/30">
                            Subscribe Now
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
