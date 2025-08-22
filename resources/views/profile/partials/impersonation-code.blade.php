@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $hasCode = $user && $user->impersonation_code && $user->impersonation_code_expires_at;
    @endphp

    <div class="max-w-xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-base-content">Support Access Code</h1>
                <p class="text-sm text-base-content/70">
                    Generate a temporary code and share it with support for time‑boxed, policy‑compliant access.
                </p>
            </div>
            <span class="badge {{ $hasCode ? 'badge-success' : 'badge-ghost' }} badge-outline">
                {{ $hasCode ? 'Active' : 'Inactive' }}
            </span>
        </div>

        @if(session('status'))
            <div class="alert alert-success">
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Card --}}
        <div class="card bg-base-100 border border-base-300 shadow rounded-2xl">
            <div class="card-body p-6 sm:p-8 space-y-5">

                @if($hasCode)
                    {{-- Expiry --}}
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-base-content/70">
                            Expires
                            <span class="font-medium text-base-content">
                                {{ $user->impersonation_code_expires_at->diffForHumans() }}
                            </span>
                        </div>
                        <span class="badge badge-outline">{{ $user->impersonation_code_expires_at->format('d M Y, H:i') }}</span>
                    </div>

                    {{-- Code + Copy --}}
                    <div class="join w-full">
                        <input class="join-item input input-bordered h-12 w-full"
                               readonly
                               value="{{ $user->impersonation_code }}">
                        <button type="button"
                                class="join-item btn btn-ghost h-12"
                                onclick="navigator.clipboard.writeText('{{ $user->impersonation_code }}')">
                            Copy
                        </button>
                    </div>

                    {{-- Actions --}}
                    <div class="card-actions justify-between">
                        <form method="POST" action="{{ route('support.code.revoke') }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline btn-error rounded-xl h-11">Revoke code</button>
                        </form>
                        <form method="POST" action="{{ route('support.code.generate') }}">
                            @csrf
                            <button class="btn btn-primary rounded-xl h-11">Regenerate</button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-base-content/70">
                        No active access code. Create one when you need to grant support temporary access.
                    </p>

                    <form method="POST" action="{{ route('support.code.generate') }}">
                        @csrf
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary rounded-xl h-12 min-w-40">Generate new code</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
