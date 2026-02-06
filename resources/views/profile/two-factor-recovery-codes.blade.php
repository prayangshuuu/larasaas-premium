<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                 <div class="bg-white shadow-sm sm:rounded-xl border border-slate-200">
                    <div class="px-4 py-5 sm:p-6">
                        <header>
                            <h2 class="text-base font-semibold leading-7 text-slate-900">Recovery Codes</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Store these one-time codes in a safe place. Each code can be used once if you lose access to your authenticator.</p>
                        </header>
                        
                        <div class="mt-6 border-t border-slate-100 pt-6">
                             <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @forelse($codes as $code)
                                    <div class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-sm font-medium text-slate-700 select-all">
                                        {{ $code }}
                                    </div>
                                @empty
                                    <div class="text-sm text-slate-500">No codes available. Regenerate to create new ones.</div>
                                @endforelse
                             </div>
                        </div>

                         <div class="mt-6 rounded-md bg-yellow-50 p-4 border border-yellow-200">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">Regenerating will invalidate the old codes immediately.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-6">
                            <a href="{{ route('profile.edit') }}" class="text-sm font-semibold leading-6 text-slate-900 hover:text-primary-600">
                                <span aria-hidden="true">&larr;</span> Back to Profile
                            </a>

                             <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}"
                                  onsubmit="return confirm('Regenerate recovery codes? Old codes will stop working.');">
                                @csrf
                                <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                                    Regenerate Codes
                                </button>
                            </form>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</x-app-layout>
