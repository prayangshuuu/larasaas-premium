@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $hasCode = $user && $user->impersonation_code && $user->impersonation_code_expires_at;
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-xl mx-auto space-y-8">
                
                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-white">Support Access Code</h1>
                        <p class="mt-1 text-sm text-zinc-400">Generate a temporary code for support access.</p>
                    </div>
                    @if($hasCode)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-400 ring-1 ring-inset ring-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-zinc-500/10 px-3 py-1 text-xs font-medium text-zinc-400 ring-1 ring-inset ring-zinc-500/20">
                            Inactive
                        </span>
                    @endif
                </div>

                @if(session('status'))
                    <div class="rounded-lg bg-emerald-500/10 p-4 border border-emerald-500/20">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-emerald-400">{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8">
                     @if($hasCode)
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-xs font-mono text-zinc-500 uppercase tracking-widest">Expiration</span>
                            <div class="text-right">
                                <div class="text-sm text-zinc-300 font-medium">{{ $user->impersonation_code_expires_at->diffForHumans() }}</div>
                                <div class="text-xs text-zinc-500">{{ $user->impersonation_code_expires_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>

                        <div class="relative group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative flex rounded-lg shadow-sm">
                                <div class="relative flex flex-grow items-stretch focus-within:z-10">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </div>
                                    <input type="text" readonly value="{{ $user->impersonation_code }}" 
                                            class="block w-full rounded-l-md border-0 bg-zinc-950 py-3 pl-10 text-white shadow-sm ring-1 ring-inset ring-zinc-800 placeholder:text-zinc-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-lg font-mono text-center tracking-[0.5em]">
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $user->impersonation_code }}')"
                                        class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-4 py-2 text-sm font-semibold text-white bg-zinc-800 ring-1 ring-inset ring-zinc-800 hover:bg-zinc-700 transition-colors">
                                    <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between items-center pt-6 border-t border-zinc-800/50">
                            <form method="POST" action="{{ route('support.code.revoke') }}">
                                @csrf @method('DELETE')
                                <button class="text-sm font-medium text-red-400 hover:text-red-300 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Revoke code
                                </button>
                            </form>

                            <form method="POST" action="{{ route('support.code.generate') }}">
                                @csrf
                                <button class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Regenerate
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto h-16 w-16 rounded-full bg-zinc-800 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">No active access code</h3>
                            <p class="mt-2 text-sm text-zinc-400 max-w-sm mx-auto">Generate a temporary high-entropy key to securely grant support staff access to your account.</p>
                            <div class="mt-8">
                                 <form method="POST" action="{{ route('support.code.generate') }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        Generate Access Code
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
