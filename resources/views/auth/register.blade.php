@extends('layouts.guest')

@section('content')
    {{-- Header --}}
    <div class="mb-10">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">Create an account</h2>
        <p class="text-sm text-slate-600">
            Start your preparation journey with a free account.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Full Name</label>
            <div class="mt-2 relative rounded-md shadow-sm">
                 <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.492a6.002 6.002 0 0113.07 0 1.5 1.5 0 00-2.096 1.408l-9.37-.536a1.5 1.5 0 00-1.604-1.372v.5z" />
                    </svg>
                </div>
                <input type="text" name="name" id="name" autocomplete="name" required autofocus 
                       value="{{ old('name') }}"
                       class="block w-full rounded-lg border-0 py-1.5 pl-10 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" 
                       placeholder="John Doe">
            </div>
        </div>

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
                <input type="email" name="email" id="email" autocomplete="email" required
                       value="{{ old('email') }}"
                       class="block w-full rounded-lg border-0 py-1.5 pl-10 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" 
                       placeholder="you@example.com">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Password</label>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="password" name="password" id="password" autocomplete="new-password" required
                       class="block w-full rounded-lg border-0 py-1.5 pl-10 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                       placeholder="••••••••">
            </div>
            <p class="mt-1 text-xs text-slate-500">Must be at least 8 characters long.</p>
        </div>

            {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-slate-900">Confirm Password</label>
            <div class="mt-2 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required
                       class="block w-full rounded-lg border-0 py-1.5 pl-10 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                       placeholder="••••••••">
            </div>
        </div>

        {{-- Terms --}}
        <div class="flex items-start">
            <div class="flex h-6 items-center">
                <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600">
            </div>
            <div class="ml-3 text-sm leading-6">
                <label for="terms" class="text-slate-600">
                    I agree to the <a href="#" class="font-semibold text-primary-600 hover:text-primary-500">Terms of Service</a> and <a href="#" class="font-semibold text-primary-600 hover:text-primary-500">Privacy Policy</a>
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <div>
            <button type="submit" class="flex w-full justify-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                Create account
            </button>
        </div>
    </form>

    <div class="mt-10">
        <div class="relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-sm font-medium leading-6">
                <span class="bg-white px-6 text-slate-900">Already registered?</span>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="font-semibold leading-6 text-primary-600 hover:text-primary-500">
                Sign in to your account
            </a>
        </div>
    </div>
@endsection
