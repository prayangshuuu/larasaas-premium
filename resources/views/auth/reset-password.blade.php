{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.guest')

@section('content')
    {{-- Header: logo centered inside a circle (matches login/register) --}}
    <div class="flex flex-col items-center text-center space-y-3 mb-6">
        <div class="w-16 h-16 rounded-full bg-base-200 ring-2 ring-primary ring-offset-2 ring-offset-base-100 flex items-center justify-center">
            <x-application-logo class="h-9 w-9 text-primary" />
        </div>
        <h1 class="text-lg font-semibold text-base-content">Reset your password</h1>
        <p class="text-sm text-base-content/70">
            Enter your email and a new password below.
        </p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-error mb-5">
            <span class="font-semibold">Please fix the following:</span>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        {{-- Token from URL --}}
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        {{-- Email --}}
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-base-content">Email</label>
            <input id="email"
                   name="email"
                   type="email"
                   value="{{ request()->email ?? old('email') }}"
                   class="input input-bordered w-full"
                   required
                   autocomplete="email" />
        </div>

        {{-- New Password --}}
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-base-content">New password</label>
            <input id="password"
                   name="password"
                   type="password"
                   class="input input-bordered w-full"
                   required
                   autocomplete="new-password"
                   autofocus />
        </div>

        {{-- Confirm New Password --}}
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-base-content">Confirm new password</label>
            <input id="password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="input input-bordered w-full"
                   required
                   autocomplete="new-password" />
        </div>

        {{-- Primary action: same size as Login/Register --}}
        <div>
            <button type="submit" class="btn btn-primary w-full h-11 rounded-xl font-semibold">
                Reset Password
            </button>
        </div>

        {{-- Secondary links: consistent font & spacing --}}
        <div class="flex items-center justify-between pt-1">
            <a href="{{ route('login') }}"
               class="text-sm font-medium text-base-content/80 hover:text-primary focus:underline">
                Back to login
            </a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="text-sm font-medium text-base-content/80 hover:text-primary focus:underline">
                    Register
                </a>
            @endif
        </div>
    </form>
@endsection
