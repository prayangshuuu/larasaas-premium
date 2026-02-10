@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div>
            <a href="{{ route('admin.plans.index') }}" class="text-sm text-zinc-400 hover:text-white transition-colors mb-2 inline-block">&larr; Back to Plans</a>
            <h1 class="text-3xl font-bold tracking-tight text-white">Create New Plan</h1>
            <p class="text-sm text-zinc-500">Define price, interval, and features for your new subscription tier.</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 sm:p-8 shadow-xl">
            <form action="{{ route('admin.plans.store') }}" method="POST" enctype="multipart/form-data" x-data="planForm()">
                @csrf
                <div class="space-y-6">
                    {{-- Name --}}
                    <div>
                        <x-ui.label for="name" value="Plan Name" class="text-white" />
                        <x-ui.input id="name" name="name" type="text" class="mt-1 block w-full bg-zinc-950 border-zinc-800 text-white" value="{{ old('name') }}" placeholder="e.g. Pro, Business" required autofocus />
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
                                <x-ui.input id="price" name="price" type="number" step="0.01" class="pl-7 block w-full bg-zinc-950 border-zinc-800 text-white" value="{{ old('price') }}" placeholder="0.00" required />
                            </div>
                            @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-ui.label for="currency" value="Currency" class="text-white" />
                            <x-ui.input id="currency" name="currency" type="text" class="mt-1 block w-full bg-zinc-950 border-zinc-800 text-white" value="{{ old('currency', 'USD') }}" required />
                             @error('currency') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Interval --}}
                    <div>
                         <x-ui.label for="interval" value="Billing Interval" class="text-white" />
                        <select id="interval" name="interval" class="mt-1 block w-full rounded-md border-0 bg-zinc-950 py-2.5 px-3 text-white shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="month" {{ old('interval') == 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ old('interval') == 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                         @error('interval') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Stripe Price ID --}}
                    <div>
                         <x-ui.label for="stripe_price_id" value="Stripe Price ID" class="text-white" />
                        <x-ui.input id="stripe_price_id" name="stripe_price_id" type="text" class="mt-1 block w-full bg-zinc-950 border-zinc-800 text-white font-mono text-sm" value="{{ old('stripe_price_id') }}" placeholder="price_..." />
                        <p class="text-xs text-zinc-500 mt-1">Optional. If generated dynamically, you can leave this blank (depending on backend logic).</p>
                        @error('stripe_price_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Plan Logo --}}
                    <div>
                        <x-ui.label for="logo" value="Plan Logo" class="text-white" />
                        <p class="text-xs text-zinc-400 mb-2">Upload an icon/logo for this plan (PNG, JPG, SVG, WebP — max 2MB).</p>
                        <input type="file" id="logo" name="logo" accept="image/*"
                               class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer file:transition-colors" />
                        @error('logo') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Features --}}
                    <div>
                        <x-ui.label for="features_input" value="Features" class="text-white" />
                        <p class="text-xs text-zinc-400 mb-2">Enter one feature per line.</p>
                        <textarea id="features_input" rows="5" class="block w-full rounded-md border-0 bg-zinc-950 py-1.5 text-white shadow-sm ring-1 ring-inset ring-zinc-800 placeholder:text-zinc-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Access to all courses&#10;Premium support&#10;..." x-model="rawFeatures"></textarea>
                        
                        {{-- Hidden input for JSON --}}
                        <input type="hidden" name="features" :value="jsonFeatures">
                         @error('features') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 border-t border-zinc-800 flex justify-end gap-3">
                        <a href="{{ route('admin.plans.index') }}" class="rounded-md bg-zinc-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700">Cancel</a>
                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Create Plan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('planForm', () => ({
                rawFeatures: '',
                get jsonFeatures() {
                    return JSON.stringify(this.rawFeatures.split('\n').filter(line => line.trim() !== ''));
                }
            }))
        });
    </script>
@endsection
