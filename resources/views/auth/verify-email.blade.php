@extends('layouts.guest')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-white mb-2">Verify your email</h2>
        <p class="text-sm text-zinc-400">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="rounded-lg bg-green-500/10 p-4 mb-6 border border-green-500/20">
             <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="ml-3 text-sm text-green-400">
                    A new verification link has been sent to the email address you provided during registration.
                </p>
            </div>
        </div>
    @endif

    <div class="mt-8 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">
                Resend Verification Email
            </button>
        </form>
    </div>
    
    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-zinc-500 hover:text-zinc-300 transition-colors">
                Log Out
            </button>
        </form>
    </div>
@endsection
