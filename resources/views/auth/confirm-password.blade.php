{{-- resources/views/auth/confirm-password.blade.php --}}
@extends('layouts.guest')

@section('content')
    {{-- Header: centered logo inside a circle (matches login/register) --}}
    <div class="flex flex-col items-center text-center space-y-3 mb-6">
        <div class="w-16 h-16 rounded-full bg-base-200 ring-2 ring-primary ring-offset-2 ring-offset-base-100 flex items-center justify-center">
            {{-- Logo perfectly centered --}}
            <x-application-logo class="h-9 w-9 text-primary" />
        </div>
        <h1 class="text-lg font-semibold text-base-content">Confirm your password</h1>
        <p class="text-sm text-base-content/70">
            This is a secure area. Please confirm your password to continue.
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

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        {{-- Password --}}
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-base-content">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="input input-bordered w-full" />
        </div>

        {{-- Primary action: full-width button (same size as Login/Register) --}}
        <div>
            <button type="submit" class="btn btn-primary w-full h-11 rounded-xl font-semibold">
                Confirm
            </button>
        </div>

        {{-- Secondary link row (consistent font/spacing) --}}
        <div class="flex items-center justify-between pt-1">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium text-base-content/80 hover:text-primary focus:underline">
                    Forgot your password?
                </a>
            @endif>

            {{-- Optional: a gentle back link (safe default to dashboard) --}}
            <a href="{{ url()->previous() ?: route('dashboard') }}"
               class="text-sm font-medium text-base-content/80 hover:text-primary focus:underline">
                Go back
            </a>
        </div>
    </form>
@endsection
