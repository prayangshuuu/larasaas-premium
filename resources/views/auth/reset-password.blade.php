@extends('layouts.guest')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-white mb-2">Reset Password</h2>
        <p class="text-sm text-zinc-400">
            Create a new password for your account.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">Email Address</label>
            <x-ui.input type="email" name="email" id="email" required autofocus :value="old('email', $request->email)" />
             @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-300 mb-1">Password</label>
            <x-ui.input type="password" name="password" id="password" required autocomplete="new-password" placeholder="••••••••" />
             @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-1">Confirm Password</label>
             <x-ui.input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">
                Reset Password
            </button>
        </div>
    </form>
@endsection
