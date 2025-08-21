{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.guest')

@section('content')
    {{-- Header: centered logo in a circle --}}
    <div class="flex flex-col items-center text-center space-y-3 mb-6">
        <div class="w-16 h-16 rounded-full bg-base-200 ring-2 ring-primary ring-offset-2 ring-offset-base-100 flex items-center justify-center">
            {{-- Logo perfectly centered --}}
            <x-application-logo class="h-9 w-9 text-primary" />
        </div>
        <h1 class="text-lg font-semibold text-base-content">Welcome back</h1>
        <p class="text-sm text-base-content/70">Log in to continue</p>
    </div>

    {{-- Status flash (optional) --}}
    @if (session('status'))
        <div class="alert alert-info text-sm mb-5">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-base-content">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="input input-bordered w-full" autocomplete="username" />
        </div>

        {{-- Password --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-medium text-base-content">Password</label>
            </div>
            <input id="password" type="password" name="password" required
                   class="input input-bordered w-full" autocomplete="current-password" />
        </div>

        {{-- Remember + Forgot (same font) --}}
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none text-sm font-medium text-base-content">
                <input id="remember_me" name="remember" type="checkbox" class="checkbox checkbox-sm" />
                <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium text-base-content hover:text-primary focus:outline-none focus:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        {{-- Buttons: exactly the same size --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button type="submit"
                    class="btn btn-primary w-full h-11 rounded-xl font-semibold">
                Login
            </button>

            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="btn w-full h-11 rounded-xl font-semibold bg-neutral text-neutral-content hover:opacity-95 dark:bg-neutral-content dark:text-neutral dark:border-base-300 text-center">
                    Register
                </a>
            @endif
        </div>
        {{-- Note: Removed the duplicate "Forgot your password? / Register" row (the last one). --}}
    </form>
@endsection
