<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden relative group">
                 {{-- Glow effect --}}
                 <div class="absolute inset-0 bg-indigo-500/5 opacity-0 group-hover:opacity-100 transition duration-700 pointer-events-none"></div>

                <div class="px-6 py-8 sm:p-10 relative z-10">
                    <header class="mb-8">
                        <h2 class="text-2xl font-bold tracking-tight text-white mb-2">Recovery Codes</h2>
                        <p class="text-zinc-400">
                            Store these one-time codes in a safe place. Each code can be used once if you lose access to your authenticator.
                        </p>
                    </header>
                    
                    {{-- Codes Grid --}}
                    <div class="bg-black/50 border border-zinc-800 rounded-xl p-6 relative">
                         @if(!empty($codes))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($codes as $code)
                                    <div class="flex items-center justify-center rounded-lg border border-zinc-800 bg-zinc-900/50 px-4 py-3 font-mono text-sm font-medium text-indigo-300 select-all hover:bg-zinc-800 transition-colors cursor-copy group/code">
                                        {{ $code }}
                                    </div>
                                @endforeach
                            </div>
                            {{-- Raw codes for download --}}
                            <textarea id="recovery-codes-raw" class="sr-only" aria-hidden="true">{{ implode("\n", $codes) }}</textarea>
                        @else
                            <div class="text-center py-6">
                                <p class="text-zinc-500">No codes available. Please regenerate to create new ones.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Warning Box --}}
                     <div class="mt-8 rounded-xl bg-orange-500/10 p-4 border border-orange-500/20 flex items-start gap-3">
                        <svg class="h-5 w-5 text-orange-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-orange-400">Security Warning</p>
                            <p class="text-sm text-orange-300/70 mt-1">Regenerating codes will immediately invalidate any previously generated codes.</p>
                        </div>
                    </div>

                    {{-- Action Footer --}}
                    <div class="mt-8 pt-6 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('profile.edit') }}" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            Back to Profile
                        </a>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            @if(!empty($codes))
                                <button type="button" id="download-codes-btn" class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-lg bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 border border-zinc-700 transition-all">
                                    <svg class="w-4 h-4 mr-2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    Download Codes
                                </button>
                            @endif

                             <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}"
                                  onsubmit="return confirm('Regenerate recovery codes? Old codes will stop working.');" class="flex-1 sm:flex-none">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 hover:shadow-[0_0_20px_rgba(79,70,229,0.3)] transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Regenerate Codes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Client-side Download Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             const dlBtn = document.getElementById('download-codes-btn');
             const raw   = document.getElementById('recovery-codes-raw');

             if (dlBtn && raw) {
                 dlBtn.addEventListener('click', () => {
                    const blob = new Blob([raw.value], { type: 'text/plain' });
                    const url  = URL.createObjectURL(blob);
                    const a    = document.createElement('a');
                    a.href = url;
                    a.download = 'ielts-recovery-codes.txt';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });
             }
        });
    </script>
</x-app-layout>
