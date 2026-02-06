<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upgrade Plan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-black min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center mb-16 relative">
                 <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-500/20 blur-[100px] rounded-full pointer-events-none"></div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-6 tracking-tight">
                    Simple, Transparent <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">Pricing</span>
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Unlock the full potential of your IELTS preparation with our premium features. No hidden fees, cancel anytime.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-4">
                @foreach ($plans as $plan)
                <div class="relative bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-3xl p-8 shadow-xl flex flex-col group hover:border-indigo-500/50 transition-colors duration-300">
                    
                    {{-- Popular Badge --}}
                    @if($plan->name === 'Pro' || $plan->is_featured) {{-- Assuming 'Pro' or flag --}}
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-1 rounded-full text-xs font-bold tracking-wide shadow-lg shadow-indigo-500/30">
                        MOST POPULAR
                    </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">${{ number_format($plan->price, 0) }}</span>
                            <span class="ml-2 text-base font-medium text-gray-500 dark:text-gray-400">/ {{ $plan->interval }}</span>
                        </div>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Everything you need to boost your score.</p>
                    </div>

                    {{-- Features --}}
                    <ul role="list" class="space-y-4 mb-8 flex-1">
                        @if(is_array($plan->features) || is_object($plan->features))
                            @foreach ($plan->features as $feature)
                                <li class="flex items-start">
                                    <div class="shrink-0">
                                        <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-base text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                </li>
                            @endforeach
                        @endif
                    </ul>

                    <a href="{{ route('billing.checkout', $plan) }}" 
                       class="block w-full py-4 px-6 text-center rounded-2xl shadow-lg 
                              bg-gray-900 dark:bg-white text-white dark:text-gray-900 
                              hover:bg-indigo-600 dark:hover:bg-indigo-50 text-base font-bold 
                              transition-all hover:scale-[1.02] hover:shadow-xl active:scale-95">
                        Get Started
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
