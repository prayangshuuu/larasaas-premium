@extends('layouts.guest')

@section('content')
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-base-content mb-2">Create your account</h1>
        <p class="text-base-content/70">Start your IELTS preparation journey today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Name Input --}}
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium text-base-content">Full Name</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 0 0-11.215 0c-.22.578.254 1.139.872 1.139h9.47Z" /></svg>
                <input type="text" name="name" class="grow" placeholder="Enter your full name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            </label>
        </div>

        {{-- Email Input --}}
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium text-base-content">Email Address</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                <input type="email" name="email" class="grow" placeholder="email@example.com" value="{{ old('email') }}" required autocomplete="username" />
            </label>
        </div>

        {{-- Password Input --}}
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium text-base-content">Password</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                <input type="password" name="password" class="grow" placeholder="Create a strong password" required autocomplete="new-password" />
            </label>
            <label class="label">
                <span class="label-text-alt text-base-content/50">Must be at least 8 characters long</span>
            </label>
        </div>

        {{-- Confirm Password Input --}}
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-medium text-base-content">Confirm Password</span>
            </label>
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                <input type="password" name="password_confirmation" class="grow" placeholder="Re-enter your password" required autocomplete="new-password" />
            </label>
        </div>

        {{-- Terms Checkbox --}}
        <div class="form-control">
            <label class="cursor-pointer label justify-start gap-2 items-start">
                <input type="checkbox" name="terms" required class="checkbox checkbox-primary checkbox-sm mt-1" />
                <span class="label-text text-base-content text-left">
                    I agree to the <a href="#" class="link link-primary font-medium">Terms of Service</a> and <a href="#" class="link link-primary font-medium">Privacy Policy</a>
                </span>
            </label>
        </div>

        {{-- Register Button --}}
        <button type="submit" class="btn btn-primary w-full btn-lg shadow-lg shadow-primary/20">
            Create your account
        </button>

        {{-- Divider --}}
        <div class="divider">OR</div>

        {{-- Login Link --}}
        @if (Route::has('login'))
            <div class="text-center">
                <p class="text-sm text-base-content/70">
                    Already have an account?
                    <a href="{{ route('login') }}" class="link link-primary font-medium">
                        Log in here
                    </a>
                </p>
            </div>
        @endif
    </form>

    {{-- Footer Message --}}
    <div class="mt-8 text-center text-xs text-base-content/50 italic">
        "Join thousands of students achieving their IELTS goals."
    </div>
@endsection
