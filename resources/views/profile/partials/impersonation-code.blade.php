@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $hasCode = $user && $user->impersonation_code && $user->impersonation_code_expires_at;
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-xl mx-auto space-y-6">
                
                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Support Access Code</h1>
                        <p class="mt-1 text-sm text-slate-600">Generate a temporary code for support access.</p>
                    </div>
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $hasCode ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-slate-50 text-slate-600 ring-slate-500/10' }}">
                        {{ $hasCode ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                @if(session('status'))
                    <div class="rounded-md bg-green-50 p-4 border border-green-200">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="bg-white shadow-sm sm:rounded-xl border border-slate-200">
                    <div class="px-4 py-5 sm:p-6">
                         @if($hasCode)
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-slate-500">Expires {{ $user->impersonation_code_expires_at->diffForHumans() }}</span>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">
                                    {{ $user->impersonation_code_expires_at->format('d M Y, H:i') }}
                                </span>
                            </div>

                            <div class="flex rounded-md shadow-sm">
                                <div class="relative flex flex-grow items-stretch focus-within:z-10">
                                    <input type="text" readonly value="{{ $user->impersonation_code }}" 
                                            class="block w-full rounded-none rounded-l-md border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 font-mono text-center tracking-widest bg-slate-50">
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $user->impersonation_code }}')"
                                        class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                    Copy
                                </button>
                            </div>

                            <div class="mt-6 flex justify-between items-center pt-6 border-t border-slate-100">
                                <form method="POST" action="{{ route('support.code.revoke') }}">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-semibold text-red-600 hover:text-red-500">Revoke code</button>
                                </form>

                                <form method="POST" action="{{ route('support.code.generate') }}">
                                    @csrf
                                    <button class="text-sm font-semibold text-primary-600 hover:text-primary-500">Regenerate</button>
                                </form>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-slate-900">No active access code</h3>
                                <p class="mt-1 text-sm text-slate-500">Generate a code to grant temporary access to support.</p>
                                <div class="mt-6">
                                     <form method="POST" action="{{ route('support.code.generate') }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                                            Generate new code
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
