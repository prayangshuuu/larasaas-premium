{{-- resources/views/profile/two-factor-recovery-codes.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="card bg-base-100 border border-base-300 shadow rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <h1 class="text-2xl font-semibold text-base-content">Recovery Codes</h1>
                <p class="text-sm text-base-content/70 mt-1">
                    Store these one-time codes in a safe place. Each code can be used once if you lose access to your authenticator.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                    @forelse($codes as $code)
                        <div class="kbd text-base">{{ $code }}</div>
                    @empty
                        <div class="text-base-content/70">No codes available. Regenerate to create new ones.</div>
                    @endforelse
                </div>

                <div class="alert alert-warning mt-6">
                    <span>Regenerating will invalidate the old codes immediately.</span>
                </div>

                <div class="card-actions justify-between mt-6">
                    <a href="{{ route('profile.edit') }}" class="btn btn-ghost rounded-xl">
                        Back to Profile
                    </a>

                    <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}"
                          onsubmit="return confirm('Regenerate recovery codes? Old codes will stop working.');"
                          class="inline-flex">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-xl">
                            Regenerate Codes
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
