@extends('layouts.guest')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-white mb-2">Forgot password?</h2>
        <p class="text-sm text-zinc-400">
            No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </p>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-green-500/10 p-4 mb-6 border border-green-500/20">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="ml-3 text-sm text-green-400">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Email Address --}}
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">Email Address</label>
            <x-ui.input type="email" name="email" id="email" required autofocus :value="old('email')" placeholder="name@example.com" />
             @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex items-center justify-end">
             <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">
                Email Password Reset Link
            </button>
        </div>
    </form>
@endsection
