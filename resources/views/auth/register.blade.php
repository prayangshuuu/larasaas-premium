{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.guest')

@section('content')
    {{-- Header: centered logo inside a circle --}}
    <div class="flex flex-col items-center text-center space-y-3 mb-6">
        <div class="w-16 h-16 rounded-full bg-base-200 ring-2 ring-primary ring-offset-2 ring-offset-base-100 flex items-center justify-center">
            {{-- Logo perfectly centered --}}
            <x-application-logo class="h-9 w-9 text-primary" />
        </div>
        <h1 class="text-lg font-semibold text-base-content">Create your account</h1>
        <p class="text-sm text-base-content/70">It takes less than a minute</p>
    </div>

    {{-- Validation errors (DaisyUI alert) --}}
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

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-base-content">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   class="input input-bordered w-full" autocomplete="name" />
        </div>

        {{-- Email --}}
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-base-content">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   class="input input-bordered w-full" autocomplete="username" />
        </div>

        {{-- Password --}}
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-base-content">Password</label>
            <input id="password" name="password" type="password" required
                   class="input input-bordered w-full" autocomplete="new-password" />
        </div>

        {{-- Confirm Password --}}
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-base-content">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   class="input input-bordered w-full" autocomplete="new-password" />
        </div>

        {{-- Primary action: single Register button filling full width --}}
        <div>
            <button type="submit" class="btn btn-primary w-full h-11 rounded-xl font-semibold">
                Register
            </button>
        </div>

        {{-- Secondary single-line link (no duplicates) --}}
        <div class="flex items-center justify-end pt-1">
            @if (Route::has('login'))
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-base-content/80 hover:text-primary focus:underline">
                    Already registered? Login
                </a>
            @endif
        </div>
    </form>
@endsection
