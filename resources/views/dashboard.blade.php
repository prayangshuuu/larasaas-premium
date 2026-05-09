<x-app-layout>
    
    {{-- Dashboard Content --}}
    <div class="py-6">
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-800 rounded-2xl border border-zinc-700 shadow-lg relative group overflow-hidden">
                    <div class="absolute inset-0 bg-indigo-500/20 blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <svg class="relative w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tighter text-white">Dashboard Overview</h1>
                    <p class="mt-1 text-sm text-zinc-400 font-medium">Welcome back, <span class="text-indigo-400">{{ Auth::user()->name }}</span>. You're ready to build.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="px-4 py-2 rounded-lg bg-zinc-900 border border-zinc-800 text-xs font-bold text-zinc-500 uppercase tracking-widest">
                    V1.2.0 Stable
                </div>
            </div>
        </div>

        {{-- Current Plan Card --}}
        <div class="mb-10">
            <div class="relative rounded-3xl p-[1px] bg-gradient-to-br from-white/10 via-transparent to-transparent group overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative bg-[#090909] rounded-[23px] p-8">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="flex items-start gap-6">
                            @if($currentSubscription && $currentSubscription->plan)
                                @if($currentSubscription->plan->logo)
                                    <img src="{{ asset('storage/' . $currentSubscription->plan->logo) }}" alt="{{ $currentSubscription->plan->name }}" class="h-20 w-20 rounded-2xl object-cover border border-white/10 bg-zinc-800 shadow-2xl" />
                                @else
                                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-2xl shadow-indigo-500/20">
                                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-3xl font-black text-white tracking-tighter">{{ $currentSubscription->plan->name }}</h3>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            {{ $currentSubscription->status }}
                                        </span>
                                    </div>
                                    <p class="text-zinc-400 font-medium max-w-md">
                                        You are currently on the premium plan. You have access to all high-performance modules.
                                    </p>
                                    <div class="mt-4 flex items-center gap-4 text-xs font-bold text-zinc-500">
                                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" /></svg> Next Billing: {{ $currentSubscription->current_period_end ? $currentSubscription->current_period_end->format('M d, Y') : 'N/A' }}</span>
                                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> Auto-renew: On</span>
                                    </div>
                                </div>
                            @else
                                <div class="h-20 w-20 rounded-2xl bg-zinc-800 border border-white/5 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-white tracking-tighter mb-1">No Active Subscription</h3>
                                    <p class="text-zinc-400 font-medium max-w-sm">
                                        Upgrade to a premium plan to unlock unlimited API access, team collaboration, and priority support.
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <a href="{{ route('billing.index') }}" class="px-6 py-3 rounded-xl bg-white text-black font-bold text-sm hover:bg-zinc-200 transition-all active:scale-95 shadow-xl shadow-white/5">
                                Manage Billing
                            </a>
                            @if(!$currentSubscription)
                                <a href="{{ route('pricing.index') }}" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-500 transition-all active:scale-95 shadow-xl shadow-indigo-500/20">
                                    Upgrade Now
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Metric 1: API Usage --}}
            <div class="p-6 bg-zinc-900 border border-white/5 rounded-3xl group hover:border-indigo-500/30 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-indigo-400 transition-colors">API Requests</span>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-white tracking-tighter">84.2k</span>
                    <span class="text-xs font-bold text-emerald-500 mb-1">+12.5%</span>
                </div>
                <div class="mt-4 w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full" style="width: 65%"></div>
                </div>
            </div>

            {{-- Metric 2: Storage --}}
            <div class="p-6 bg-zinc-900 border border-white/5 rounded-3xl group hover:border-purple-500/30 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-purple-500/10 rounded-lg text-purple-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-purple-400 transition-colors">Storage Used</span>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-white tracking-tighter">1.2GB</span>
                    <span class="text-xs font-bold text-zinc-500 mb-1">of 10GB</span>
                </div>
                <div class="mt-4 w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 rounded-full" style="width: 12%"></div>
                </div>
            </div>

            {{-- Metric 3: Uptime --}}
            <div class="p-6 bg-zinc-900 border border-white/5 rounded-3xl group hover:border-emerald-500/30 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-emerald-400 transition-colors">Uptime</span>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-white tracking-tighter">99.9%</span>
                    <span class="text-xs font-bold text-emerald-500 mb-1">Healthy</span>
                </div>
                <div class="mt-4 flex gap-1">
                    @for($i=0; $i<20; $i++)
                        <div class="h-4 w-full bg-emerald-500/40 rounded-sm"></div>
                    @endfor
                </div>
            </div>

            {{-- Metric 4: Teams --}}
            <div class="p-6 bg-zinc-900 border border-white/5 rounded-3xl group hover:border-pink-500/30 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-pink-500/10 rounded-lg text-pink-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-pink-400 transition-colors">Team Members</span>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-white tracking-tighter">42</span>
                    <span class="text-xs font-bold text-zinc-500 mb-1">Seats Used</span>
                </div>
                <div class="mt-4 flex -space-x-2">
                    <img class="w-6 h-6 rounded-full border border-black shadow-lg" src="https://i.pravatar.cc/100?u=10" />
                    <img class="w-6 h-6 rounded-full border border-black shadow-lg" src="https://i.pravatar.cc/100?u=11" />
                    <img class="w-6 h-6 rounded-full border border-black shadow-lg" src="https://i.pravatar.cc/100?u=12" />
                    <div class="w-6 h-6 rounded-full bg-zinc-800 border border-black flex items-center justify-center text-[8px] font-black">+39</div>
                </div>
            </div>
        </div>

        {{-- Interactive Activity Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-zinc-900 border border-white/5 rounded-3xl p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-white tracking-tighter">Deployment History</h3>
                        <button class="text-xs font-bold text-indigo-400 hover:text-white transition-colors">View All</button>
                    </div>
                    
                    <div class="space-y-6">
                        @forelse([1, 2, 3] as $item)
                            <div class="flex items-center gap-4 group cursor-pointer">
                                <div class="w-12 h-12 rounded-xl bg-zinc-800 border border-white/5 flex items-center justify-center group-hover:border-indigo-500/50 transition-colors">
                                    <svg class="w-6 h-6 text-zinc-500 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" /></svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-white mb-0.5">Production Build #729</h4>
                                    <p class="text-xs font-medium text-zinc-500">v1.2.0-stable · Triggered by CI/CD</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-emerald-400 mb-1">Success</div>
                                    <div class="text-[10px] font-medium text-zinc-600">24m ago</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-zinc-500 text-sm">No recent deployments.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-8 text-white relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                    <h3 class="text-2xl font-black tracking-tighter mb-4">Quick Start Guide</h3>
                    <p class="text-sm text-white/80 font-medium mb-8 leading-relaxed">
                        Learn how to integrate LaraSaaS with your existing infrastructure in under 5 minutes.
                    </p>
                    <a href="{{ route('admin.docs.api') }}" class="inline-flex items-center gap-2 text-sm font-bold bg-white text-indigo-600 px-6 py-3 rounded-xl hover:scale-105 active:scale-95 transition-all">
                        Documentation
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
                
                <div class="mt-6 p-6 bg-zinc-900 border border-white/5 rounded-3xl">
                    <h4 class="text-xs font-black text-zinc-500 uppercase tracking-widest mb-4">System Status</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-300">Auth Service</span>
                            <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-300">Stripe Webhooks</span>
                            <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-300">Database Engine</span>
                            <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
