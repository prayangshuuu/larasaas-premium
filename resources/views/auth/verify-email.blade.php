{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.guest')

@section('content')
    {{-- Header: logo centered inside a circle (same as login/register) --}}
    <div class="flex flex-col items-center text-center space-y-3 mb-6">
        <div class="w-16 h-16 rounded-full bg-base-200 ring-2 ring-primary ring-offset-2 ring-offset-base-100 flex items-center justify-center">
            <x-application-logo class="h-9 w-9 text-primary" />
        </div>
        <h1 class="text-lg font-semibold text-base-content">Verify your email</h1>
        <p class="text-sm text-base-content/70">
            We’ve sent a verification link to your email. Click the link to finish setting up your account.
            If you didn’t receive it, you can request another one below.
        </p>
    </div>

    {{-- Success notice when a new link was sent --}}
    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success mb-5">
            <span>A new verification link has been sent to the email address you provided during registration.</span>
        </div>
    @endif

    {{-- Resend verification link --}}
    <form method="POST" action="{{ route('verification.send') }}" class="space-y-5">
        @csrf

        <button type="submit" class="btn btn-primary w-full h-11 rounded-xl font-semibold">
            Resend Verification Email
        </button>

        {{-- Secondary actions: consistent fonts & spacing --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline w-full h-11 rounded-xl font-semibold">
                Edit Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost w-full h-11 rounded-xl font-semibold border border-base-300">
                    Log Out
                </button>
            </form>
        </div>
    </form>
@endsection
