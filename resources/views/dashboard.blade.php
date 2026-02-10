<x-app-layout>
    
    {{-- Dashboard Content --}}
    <div class="py-6">
        <div class="mb-8 flex items-center gap-4">
            <div class="p-3 bg-zinc-800 rounded-2xl border border-zinc-700 shadow-lg">
                <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Dashboard</h1>
                <p class="mt-1 text-sm text-zinc-400">Welcome back, {{ Auth::user()->name }}. Here's your overview.</p>
            </div>
        </div>

        {{-- Current Plan Card --}}
        <div class="mb-8">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group hover:border-zinc-700 transition-colors">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Current Plan</h3>
                    @if($currentSubscription && $currentSubscription->plan)
                        <div class="flex items-center gap-4">
                            {{-- Plan Logo --}}
                            @if($currentSubscription->plan->logo)
                                <img src="{{ asset('storage/' . $currentSubscription->plan->logo) }}" alt="{{ $currentSubscription->plan->name }}" class="h-14 w-14 rounded-xl object-cover border border-zinc-700 bg-zinc-800 shrink-0" />
                            @else
                                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shrink-0">
                                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-xl font-bold text-white">{{ $currentSubscription->plan->name }}</h4>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ $currentSubscription->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                        {{ ucfirst(str_replace('_', ' ', $currentSubscription->status)) }}
                                    </span>
                                </div>
                                <p class="text-sm text-zinc-400 mt-1">
                                    ${{ $currentSubscription->plan->price }}/{{ $currentSubscription->plan->interval }}
                                    @if($currentSubscription->current_period_end)
                                        · Renews {{ $currentSubscription->current_period_end->format('M d, Y') }}
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('billing.plans') }}" class="inline-flex items-center rounded-lg bg-zinc-800 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 transition-colors border border-zinc-700">
                                Manage Plan
                            </a>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-white">No active plan</h4>
                                    <p class="text-sm text-zinc-400">Subscribe to unlock premium features and boost your IELTS score.</p>
                                </div>
                            </div>
                            <a href="{{ route('plans.index') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">
                                Upgrade Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <x-ui.bento-grid class="mb-8">
            {{-- Stat 1 --}}
            <x-ui.bento-grid-item
                title="Current Band Estimate"
                description="Based on your recent mock tests and practice sessions."
                className="md:col-span-1"
            >
                <x-slot:header>
                     <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-zinc-900 border border-zinc-800 items-center justify-center relative group overflow-hidden">
                        {{-- Circular Progress Mock --}}
                        <div class="relative w-24 h-24 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-800" />
                                <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-indigo-500" stroke-dasharray="251.2" stroke-dashoffset="60" />
                            </svg>
                            <span class="absolute text-2xl font-bold text-white">7.5</span>
                        </div>
                     </div>
                </x-slot:header>
            </x-ui.bento-grid-item>

            {{-- Stat 2 --}}
            <x-ui.bento-grid-item
                title="Practice Streak"
                description="Consistency is key. Keep up the good work!"
                className="md:col-span-1"
            >
                 <x-slot:header>
                     <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-zinc-900 border border-zinc-800 items-center justify-center relative group overflow-hidden">
                        <div class="text-center">
                            <div class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-tr from-orange-400 to-red-600">
                                12
                            </div>
                            <div class="text-sm font-medium text-zinc-500 uppercase tracking-widest mt-1">Days</div>
                        </div>
                     </div>
                </x-slot:header>
            </x-ui.bento-grid-item>

             {{-- Stat 3 --}}
            <x-ui.bento-grid-item
                title="Tasks Completed"
                description="Total essays written and speaking sessions recorded."
                className="md:col-span-1"
            >
                <x-slot:header>
                     <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-zinc-900 border border-zinc-800 items-center justify-center relative group overflow-hidden">
                        <div class="flex gap-1 items-end h-16">
                            <div class="w-3 bg-indigo-500/20 h-[30%] rounded-t"></div>
                            <div class="w-3 bg-indigo-500/40 h-[50%] rounded-t"></div>
                            <div class="w-3 bg-indigo-500/60 h-[70%] rounded-t"></div>
                            <div class="w-3 bg-indigo-500/80 h-[40%] rounded-t"></div>
                            <div class="w-3 bg-indigo-500 h-[80%] rounded-t"></div>
                        </div>
                     </div>
                </x-slot:header>
            </x-ui.bento-grid-item>

             {{-- Recent Activity --}}
            <x-ui.bento-grid-item
                title="Recent Activity"
                description="Your latest milestones and tests."
                className="md:col-span-3"
            >
                <x-slot:header>
                    <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-zinc-900 border border-zinc-800 p-6 overflow-hidden">
                        <ul class="space-y-4 w-full">
                            <li class="flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-zinc-300 text-sm">Completed Writing Task 2 Mock</span>
                                <span class="text-zinc-600 text-xs ml-auto">2 hours ago</span>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                <span class="text-zinc-300 text-sm">Started Speaking Module</span>
                                <span class="text-zinc-600 text-xs ml-auto">Yesterday</span>
                            </li>
                            <li class="flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                                <span class="text-zinc-300 text-sm">Vocabulary Quiz: High Score!</span>
                                <span class="text-zinc-600 text-xs ml-auto">2 days ago</span>
                            </li>
                        </ul>
                    </div>
                </x-slot:header>
            </x-ui.bento-grid-item>
        </x-ui.bento-grid>
        
        <div class="mt-8 flex justify-end">
            <x-ui.button-shimmer href="#">
                Start New Practice
            </x-ui.button-shimmer>
        </div>
    </div>
</x-app-layout>
