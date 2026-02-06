@extends('layouts.guest')

@section('content')
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-base-content mb-2">Welcome back</h1>
        <p class="text-base-content/70">Log in to continue your IELTS preparation journey</p>
    </div>

    {{-- Status Message --}}
    @if (session('status'))
        <div class="alert alert-info mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email Input --}}
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium text-base-content">Email Address</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                <input type="email" name="email" class="grow" placeholder="email@example.com" value="{{ old('email') }}" required autofocus autocomplete="username" />
            </label>
        </div>

        {{-- Password Input --}}
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium text-base-content">Password</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                <input type="password" name="password" class="grow" placeholder="Enter password" required autocomplete="current-password" />
            </label>
        </div>

        {{-- Remember & Forgot Password --}}
        <div class="flex items-center justify-between">
            <label class="cursor-pointer label justify-start gap-2">
                <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm" />
                <span class="label-text text-base-content">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link link-primary link-hover text-sm">
                    Forgot password?
                </a>
            @endif
        </div>

        {{-- Login Button --}}
        <button type="submit" class="btn btn-primary w-full btn-lg shadow-lg shadow-primary/20">
            Log in to your account
        </button>

        {{-- Divider --}}
        <div class="divider">OR</div>

        {{-- Register Link --}}
        @if (Route::has('register'))
            <div class="text-center">
                <p class="text-sm text-base-content/70">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="link link-primary font-medium">
                        Create one now
                    </a>
                </p>
            </div>
        @endif
    </form>

    {{-- Footer Message --}}
    <div class="mt-8 text-center text-xs text-base-content/50 italic">
        "Your journey to achieving your target IELTS band score starts here."
    </div>
@endsection
