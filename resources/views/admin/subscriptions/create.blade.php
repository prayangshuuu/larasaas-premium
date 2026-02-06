@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Assign Subscription</h2>
    </div>

    <div class="bg-zinc-900 overflow-hidden shadow-xl sm:rounded-lg border border-zinc-700 p-8">
        <form action="{{ route('admin.subscriptions.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- User Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-zinc-300">User Email</label>
                <div class="mt-1">
                    <input type="email" name="email" id="email" required value="{{ old('email') }}" class="block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="user@example.com">
                    @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <p class="mt-1 text-xs text-zinc-500">The user must already exist in the system.</p>
            </div>

            <!-- Plan -->
            <div>
                 <label for="plan_id" class="block text-sm font-medium text-zinc-300">Plan</label>
                 <select name="plan_id" id="plan_id" required class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                     @foreach($plans as $plan)
                         <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                             {{ $plan->name }} (${{ $plan->price }})
                         </option>
                     @endforeach
                 </select>
            </div>

            <!-- Expiration Override (Optional) -->
            <div>
                <label for="expires_at" class="block text-sm font-medium text-zinc-300">Next Renewal / Expiration (Optional)</label>
                <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <p class="mt-1 text-xs text-zinc-500">Leave blank for default (30 days from now).</p>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 border border-zinc-600 rounded-md shadow-sm text-sm font-medium text-zinc-300 hover:bg-zinc-800 focus:outline-none">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Assign Plan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
