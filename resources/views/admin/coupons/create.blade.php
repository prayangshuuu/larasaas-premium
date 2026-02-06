@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Create New Coupon</h2>
    </div>

    <div class="bg-zinc-900 overflow-hidden shadow-xl sm:rounded-lg border border-zinc-700 p-8">
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-zinc-300">Coupon Code</label>
                <div class="mt-1">
                    <input type="text" name="code" id="code" required value="{{ old('code') }}" class="block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. SAVE20">
                    @error('code') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Type & Value -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-zinc-300">Discount Type</label>
                    <select name="type" id="type" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-zinc-300">Discount Value</label>
                    <input type="number" step="0.01" name="value" id="value" required value="{{ old('value') }}" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="20">
                    @error('value') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Duration -->
            <div>
                <label for="duration" class="block text-sm font-medium text-zinc-300">Duration</label>
                <select name="duration" id="duration" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="toggleDurationMonths(this.value)">
                    <option value="once" {{ old('duration') == 'once' ? 'selected' : '' }}>Once (First Invoice)</option>
                    <option value="forever" {{ old('duration') == 'forever' ? 'selected' : '' }}>Forever</option>
                    <option value="repeating" {{ old('duration') == 'repeating' ? 'selected' : '' }}>Repeating (Specific Months)</option>
                </select>
                <p class="mt-1 text-xs text-zinc-500">How long this coupon applies to a subscription.</p>
            </div>

            <!-- Duration In Months (Hidden functionality) -->
            <div id="duration_months_wrapper" class="hidden">
                 <label for="duration_in_months" class="block text-sm font-medium text-zinc-300">Number of Months</label>
                 <input type="number" name="duration_in_months" id="duration_in_months" value="{{ old('duration_in_months') }}" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Limits -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-zinc-300">Expires At (Optional)</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="max_uses" class="block text-sm font-medium text-zinc-300">Max Redemptions (Optional)</label>
                    <input type="number" name="max_uses" id="max_uses" value="{{ old('max_uses') }}" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 border border-zinc-600 rounded-md shadow-sm text-sm font-medium text-zinc-300 hover:bg-zinc-800 focus:outline-none">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Coupon
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleDurationMonths(val) {
        const el = document.getElementById('duration_months_wrapper');
        if (val === 'repeating') {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }
    // Run on load in case validation failed
    document.addEventListener('DOMContentLoaded', () => {
        toggleDurationMonths(document.getElementById('duration').value);
    });
</script>
@endsection
