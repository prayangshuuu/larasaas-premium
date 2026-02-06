@extends('layouts.guest')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Reset Password</h2>
        <p class="text-sm text-slate-600">
            Create a new password for your account.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-slate-900">Email Address</label>
            <div class="mt-2">
                 <input type="email" name="email" id="email" required autofocus
                       value="{{ old('email', $request->email) }}"
                       class="block w-full rounded-lg border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
            </div>
             @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Password</label>
            <div class="mt-2">
                 <input type="password" name="password" id="password" required autocomplete="new-password"
                       class="block w-full rounded-lg border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
            </div>
             @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-slate-900">Confirm Password</label>
            <div class="mt-2">
                 <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="block w-full rounded-lg border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="flex w-full justify-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                Reset Password
            </button>
        </div>
    </form>
@endsection
