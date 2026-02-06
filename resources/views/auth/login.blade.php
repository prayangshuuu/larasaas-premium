@extends('layouts.guest')

@section('content')
    {{-- Header --}}
    <div class="mb-10">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Welcome back</h2>
        <p class="text-sm text-slate-600">
            Please enter your details to access your dashboard.
        </p>
    </div>

    {{-- Status --}}
    @if (session('status'))
        <div class="rounded-md bg-blue-50 p-4 mb-6 border border-blue-100">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1 md:flex md:justify-between">
                    <p class="text-sm text-blue-700">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-slate-900">Email address</label>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" viewbox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                        <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                    </svg>
                </div>
                <input type="email" name="email" id="email" autocomplete="username" required autofocus 
                       value="{{ old('email') }}"
                       class="block w-full rounded-lg border-0 py-1.5 pl-10 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" 
                       placeholder="you@example.com">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Password</label>
                @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-semibold text-primary-600 hover:text-primary-500">Forgot password?</a>
                    </div>
                @endif
            </div>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="password" name="password" id="password" autocomplete="current-password" required
                       class="block w-full rounded-lg border-0 py-1.5 pl-10 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                       placeholder="••••••••">
            </div>
        </div>

        {{-- Remember --}}
        <div class="flex items-center">
            <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600">
            <label for="remember-me" class="ml-3 block text-sm leading-6 text-slate-900">Remember me</label>
        </div>

        {{-- Submit --}}
        <div>
            <button type="submit" class="flex w-full justify-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                Sign in
            </button>
        </div>
    </form>

    <div class="mt-10">
        <div class="relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-sm font-medium leading-6">
                <span class="bg-white px-6 text-slate-900">New here?</span>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('register') }}" class="font-semibold leading-6 text-primary-600 hover:text-primary-500">
                Create an account
            </a>
        </div>
    </div>
@endsection
