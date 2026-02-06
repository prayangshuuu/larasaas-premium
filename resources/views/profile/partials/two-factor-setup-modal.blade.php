@php
    /** @var \App\Models\User|null $user */
    $user = $user ?? auth()->user();
    $twofaEnabled = (bool) ($user?->two_factor_secret ?? false);

    $qrSvg  = ($twofaEnabled && method_exists($user, 'twoFactorQrCodeSvg')) ? $user->twoFactorQrCodeSvg() : null;
    $codes  = ($twofaEnabled && method_exists($user, 'recoveryCodes')) ? (array) $user->recoveryCodes() : [];

    $regenAction = \Illuminate\Support\Facades\Route::has('2fa.codes.regenerate')
        ? route('2fa.codes.regenerate')
        : url('/user/two-factor-recovery-codes');

    $downloadHref = \Illuminate\Support\Facades\Route::has('2fa.codes.download')
        ? route('2fa.codes.download')
        : null;

    $shouldOpen = isset($show) ? (bool) $show : false;
@endphp

<div x-data="{ show: @json($shouldOpen), ack: false }"
     x-on:open-modal.window="show = true"
     x-on:close-modal.window="show = false"
     x-on:keydown.escape.window="show = false"
     class="relative z-50">

    {{-- Backdrop --}}
    <div x-show="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display: none;"></div>

    <div x-show="show" class="fixed inset-0 z-10 w-screen overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-zinc-900 border border-zinc-800 px-4 pb-4 pt-5 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl sm:p-8"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.outside="show = false">
                
                {{-- Glow --}}
                <div class="absolute inset-0 bg-indigo-500/5 pointer-events-none"></div>

                <div class="relative z-10">
                    <div>
                        <h3 class="text-xl font-bold leading-6 text-white">Set up Two-Factor Authentication</h3>
                        <div class="mt-2">
                             <p class="text-sm text-zinc-400">
                                Scan the QR code with Google Authenticator (or any TOTP app), then store your backup codes somewhere safe.
                            </p>
                        </div>
                    </div>

                    {{-- QR Code --}}
                    <div class="mt-6">
                        <div class="rounded-xl bg-black/50 border border-zinc-800 p-6 flex flex-col items-center justify-center text-center">
                             @if($qrSvg)
                                <div class="p-3 bg-white rounded-lg shadow-sm border border-zinc-200 mb-4">{!! $qrSvg !!}</div>
                                <p class="text-xs text-zinc-500 max-w-xs mx-auto">Scan this code in your authenticator app and enter the 6-digit code below to confirm.</p>
                            @else
                                <p class="text-sm text-zinc-500">QR code unavailable.</p>
                            @endif
                        </div>
                    </div>

                     {{-- Confirm Code --}}
                    <div class="mt-6">
                         <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}">
                            @csrf
                            <label class="block text-sm font-medium leading-6 text-zinc-300">Authenticator 6-digit code</label>
                            <div class="mt-2 flex gap-3">
                                <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" placeholder="123456" required
                                       class="block w-full rounded-lg border-0 bg-zinc-950/50 py-2.5 text-white shadow-sm ring-1 ring-inset ring-zinc-700 placeholder:text-zinc-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono tracking-widest text-center">
                                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:scale-105 active:scale-95">Confirm</button>
                            </div>
                             @error('code') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                            @if (session('status') === 'two-factor-authentication-confirmed')
                                 <p class="mt-2 text-sm text-emerald-400">Two-factor authentication confirmed.</p>
                            @endif
                         </form>
                    </div>

                    {{-- Recovery Codes --}}
                    <div class="mt-8 border-t border-zinc-800 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-medium text-white">Recovery Codes</h4>
                            <div class="flex gap-3 text-sm">
                                 <button type="button" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors" id="twofa-copy-codes">Copy</button>
                                 <span class="text-zinc-700">|</span>
                                 @if($downloadHref)
                                    <a href="{{ $downloadHref }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Download</a>
                                 @else
                                    <button type="button" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors" id="twofa-download-codes">Download</button>
                                 @endif
                            </div>
                        </div>

                        <div class="bg-black/50 rounded-xl border border-zinc-800 p-4">
                             @if(!empty($codes))
                                 <ul class="grid grid-cols-2 gap-2 font-mono text-xs text-zinc-400">
                                    @foreach($codes as $code)
                                        <li class="bg-zinc-900/50 px-2 py-1.5 rounded border border-zinc-800 text-center select-all hover:text-white transition-colors cursor-copy">{{ $code }}</li>
                                    @endforeach
                                </ul>
                                <textarea id="twofa-codes-raw" class="sr-only" aria-hidden="true">{{ implode("\n", $codes) }}</textarea>
                                 <p class="mt-3 text-xs text-zinc-500 text-center">Keep these codes safe. Each one can be used once.</p>
                            @else
                                <p class="text-sm text-zinc-500 text-center">No recovery codes found.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex items-center">
                        <input id="twofa-ack" type="checkbox" x-model="ack" class="h-4 w-4 rounded border-zinc-700 bg-zinc-900/50 text-indigo-600 focus:ring-indigo-600 focus:ring-offset-zinc-900">
                        <label for="twofa-ack" class="ml-2 block text-sm text-zinc-300">I have saved my recovery codes.</label>
                    </div>

                    <div class="mt-6 sm:mt-8">
                        <button type="button" @click="show = false" :disabled="!ack" class="inline-flex w-full justify-center rounded-lg bg-zinc-800 px-3 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-zinc-700 hover:bg-zinc-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">Done</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Copy codes logic
        const copyBtn = document.getElementById('twofa-copy-codes');
        const raw     = document.getElementById('twofa-codes-raw');
        
        copyBtn?.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(raw?.value || '');
                const originalText = copyBtn.innerText;
                copyBtn.innerText = 'Copied!';
                setTimeout(() => copyBtn.innerText = originalText, 2000);
            } catch (err) {
                console.error('Failed to copy', err);
            }
        });

         // Client-side download logic
        const dlBtn = document.getElementById('twofa-download-codes');
        dlBtn?.addEventListener('click', () => {
            const blob = new Blob([raw?.value || ''], { type: 'text/plain' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href = url;
            a.download = 'recovery-codes.txt';
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
        });
    });
</script>
