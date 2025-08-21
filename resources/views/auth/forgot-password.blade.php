{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.guest')

@section('content')
    {{-- Header: centered logo inside a circle (same as login/register) --}}
    <div class="flex flex-col items-center text-center space-y-3 mb-6">
        <div class="w-16 h-16 rounded-full bg-base-200 ring-2 ring-primary ring-offset-2 ring-offset-base-100 flex items-center justify-center">
            {{-- Logo perfectly centered --}}
            <x-application-logo class="h-9 w-9 text-primary" />
        </div>
        <h1 class="text-lg font-semibold text-base-content">Forgot your password?</h1>
        <p class="text-sm text-base-content/70">Enter your email and we’ll send you a reset link.</p>
    </div>

    {{-- Success status --}}
    @if (session('status'))
        <div class="alert alert-success mb-5">
            <span class="text-sm">{{ session('status') }}</span>
        </div>
    @endif

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

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-base-content">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="input input-bordered w-full" autocomplete="username" />
        </div>

        {{-- Primary action: full-width button (same size as Login/Register) --}}
        <div>
            <button type="submit" class="btn btn-primary w-full h-11 rounded-xl font-semibold">
                Email Password Reset Link
            </button>
        </div>

        {{-- Secondary links (single-line, consistent font/spacing) --}}
        <div class="flex items-center justify-between pt-1">
            @if (Route::has('login'))
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-base-content/80 hover:text-primary focus:underline">
                    Back to login
                </a>
            @endif

            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="text-sm font-medium text-base-content/80 hover:text-primary focus:underline">
                    Create an account
                </a>
            @endif
        </div>
    </form>
@endsection
