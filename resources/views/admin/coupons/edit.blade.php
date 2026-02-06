@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Edit Coupon: {{ $coupon->code }}</h2>
    </div>

    <div class="bg-zinc-900 overflow-hidden shadow-xl sm:rounded-lg border border-zinc-700 p-8">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Read-Only Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-zinc-800 rounded-lg border border-zinc-600">
                <div>
                    <label class="block text-sm font-medium text-zinc-400">Coupon Code</label>
                    <p class="text-lg text-white font-mono mt-1">{{ $coupon->code }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400">Discount</label>
                    <p class="text-lg text-white mt-1">
                        {{ $coupon->type === 'percent' ? $coupon->value . '%' : '$' . $coupon->value }} Off
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-400">Duration</label>
                    <p class="text-lg text-white mt-1 capitalize">{{ $coupon->duration }}</p>
                </div>
                 <div>
                    <label class="block text-sm font-medium text-zinc-400">Usage So Far</label>
                    <p class="text-lg text-white mt-1">{{ $coupon->times_used }}</p>
                </div>
            </div>

            <div class="border-t border-zinc-700 pt-6">
                <h3 class="text-lg font-medium text-white mb-4">Editable Settings</h3>
                
                <!-- Status -->
                <div class="mb-6">
                    <label for="is_active" class="block text-sm font-medium text-zinc-300">Status</label>
                    <select name="is_active" id="is_active" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="1" {{ old('is_active', $coupon->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $coupon->is_active) ? '' : 'selected' }}>Inactive</option>
                    </select>
                </div>

                <!-- Limits -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-zinc-300">Expires At (Optional)</label>
                        <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="max_uses" class="block text-sm font-medium text-zinc-300">Max Redemptions (Optional)</label>
                        <input type="number" name="max_uses" id="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 border border-zinc-600 rounded-md shadow-sm text-sm font-medium text-zinc-300 hover:bg-zinc-800 focus:outline-none">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Coupon
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
