@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div>
             <a href="{{ route('admin.plans.index') }}" class="text-sm text-zinc-400 hover:text-white transition-colors mb-2 inline-block">&larr; Back to Plans</a>
            <h1 class="text-3xl font-bold tracking-tight text-white">Edit Plan: {{ $plan->name }}</h1>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 sm:p-8 shadow-xl">
            <form action="{{ route('admin.plans.update', $plan) }}" method="POST" x-data="planForm({
                initialFeatures: {{ json_encode($plan->features ?? []) }}
            })">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    {{-- Name --}}
                    <div>
                        <x-ui.label for="name" value="Plan Name" class="text-white" />
                        <x-ui.input id="name" name="name" type="text" class="mt-1 block w-full bg-zinc-950 border-zinc-800 text-white" value="{{ old('name', $plan->name) }}" required />
                        @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Price & Currency --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-ui.label for="price" value="Price" class="text-white" />
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-zinc-500 sm:text-sm">$</span>
                                </div>
                                <x-ui.input id="price" name="price" type="number" step="0.01" class="pl-7 block w-full bg-zinc-950 border-zinc-800 text-white" value="{{ old('price', $plan->price) }}" required />
                            </div>
                            @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-ui.label for="currency" value="Currency" class="text-white" />
                            <x-ui.input id="currency" name="currency" type="text" class="mt-1 block w-full bg-zinc-950 border-zinc-800 text-white" value="{{ old('currency', $plan->currency) }}" required />
                             @error('currency') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Interval --}}
                    <div>
                         <x-ui.label for="interval" value="Billing Interval" class="text-white" />
                        <select id="interval" name="interval" class="mt-1 block w-full rounded-md border-0 bg-zinc-950 py-2.5 px-3 text-white shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="month" {{ old('interval', $plan->interval) == 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ old('interval', $plan->interval) == 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                         @error('interval') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Stripe Price ID --}}
                    <div>
                         <x-ui.label for="stripe_price_id" value="Stripe Price ID" class="text-white" />
                        <x-ui.input id="stripe_price_id" name="stripe_price_id" type="text" class="mt-1 block w-full bg-zinc-950 border-zinc-800 text-white font-mono text-sm" value="{{ old('stripe_price_id', $plan->stripe_price_id) }}" />
                        @error('stripe_price_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Features --}}
                    <div>
                        <x-ui.label for="features_input" value="Features" class="text-white" />
                        <p class="text-xs text-zinc-400 mb-2">Enter one feature per line.</p>
                        <textarea id="features_input" rows="5" class="block w-full rounded-md border-0 bg-zinc-950 py-1.5 text-white shadow-sm ring-1 ring-inset ring-zinc-800 placeholder:text-zinc-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" x-model="rawFeatures"></textarea>
                        
                        {{-- Hidden input for JSON --}}
                        <input type="hidden" name="features" :value="jsonFeatures">
                         @error('features') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                     {{-- Active Status --}}
                    <div class="flex items-center gap-3 pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50 h-5 w-5" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-zinc-300">Plan is Active (Visible to users)</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('admin.plans.index') }}" class="rounded-md bg-zinc-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700">Cancel</a>
                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Update Plan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('planForm', ({ initialFeatures }) => ({
                rawFeatures: '',
                init() {
                    // Check if initialFeatures is a string (JSON) or array
                    let features = initialFeatures;
                    if (typeof features === 'string') {
                        try {
                            features = JSON.parse(features);
                        } catch (e) {
                            features = [];
                        }
                    }
                    if (Array.isArray(features)) {
                        this.rawFeatures = features.join('\n');
                    }
                },
                get jsonFeatures() {
                    return JSON.stringify(this.rawFeatures.split('\n').filter(line => line.trim() !== ''));
                }
            }))
        });
    </script>
@endsection
