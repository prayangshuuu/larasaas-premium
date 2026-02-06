@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Subscription Plans</h1>
                <p class="text-sm text-zinc-400 mt-1">Manage billing tiers and feature sets.</p>
            </div>
            <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Plan
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-emerald-500/10 p-4 border border-emerald-500/20">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 text-sm text-emerald-400">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        {{-- Plans Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($plans as $plan)
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 flex flex-col hover:border-zinc-700 transition-colors group relative overflow-hidden">
                    {{-- Glow effect --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

                    <div class="flex justify-between items-start mb-4 z-10">
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                            <div class="text-sm text-zinc-400 font-mono mt-1">{{ $plan->stripe_price_id }}</div>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-zinc-800 px-2.5 py-0.5 text-xs font-medium text-zinc-300 border border-zinc-700">
                             {{ $plan->is_active ? 'Active' : 'Archived' }}
                        </span>
                    </div>

                    <div class="mb-6 z-10">
                        <span class="text-3xl font-bold text-white">{{ $plan->currency }} {{ $plan->price }}</span>
                        <span class="text-zinc-500">/ {{ $plan->interval }}</span>
                    </div>

                    <div class="flex-1 space-y-2 mb-8 z-10">
                        @if($plan->features)
                            @foreach(is_string($plan->features) ? json_decode($plan->features, true) ?? [] : $plan->features as $feature)
                                <div class="flex items-center text-sm text-zinc-300">
                                    <svg class="h-4 w-4 text-indigo-500 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $feature }}
                                </div>
                            @endforeach
                        @else
                            <div class="text-sm text-zinc-500 italic">No features listed</div>
                        @endif
                    </div>

                    <div class="flex gap-2 z-10 pt-4 border-t border-zinc-800">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="flex-1 inline-flex justify-center items-center rounded-lg bg-zinc-800 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-700 transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-red-500/10 px-3 py-2 text-sm font-semibold text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-800 mb-4">
                        <svg class="h-8 w-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white">No plans found</h3>
                    <p class="mt-1 text-sm text-zinc-500">Get started by creating a new subscription plan.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
