@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white tracking-tight">Create Team</h1>
            <p class="mt-1 text-sm text-zinc-400">Create a new team and assign an owner.</p>
        </div>

        {{-- Form --}}
        <div class="max-w-2xl bg-zinc-900 border border-zinc-800/50 rounded-2xl shadow-xl overflow-hidden backdrop-blur-xl">
            <form action="{{ route('admin.teams.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-300 mb-2">Team Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                           class="block w-full rounded-lg border-zinc-700 bg-zinc-800/50 text-white placeholder-zinc-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-zinc-300 mb-2">Owner</label>
                    <select name="user_id" id="user_id" required
                            class="block w-full rounded-lg border-zinc-700 bg-zinc-800/50 text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select an owner...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800/50">
                    <a href="{{ route('admin.teams.index') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition-colors">Cancel</a>
                    <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-colors">
                        Create Team
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
