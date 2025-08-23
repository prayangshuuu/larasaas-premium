{{-- resources/views/profile/partials/two-factor-setup-modal.blade.php --}}
@php
    /** @var \App\Models\User|null $user */
    $user = $user ?? auth()->user();

    $twofaEnabled = (bool) ($user?->two_factor_secret ?? false);

    // Guard Fortify helpers in case they’re unavailable
    $qrSvg  = ($twofaEnabled && method_exists($user, 'twoFactorQrCodeSvg')) ? $user->twoFactorQrCodeSvg() : null;
    $codes  = ($twofaEnabled && method_exists($user, 'recoveryCodes')) ? (array) $user->recoveryCodes() : [];

    // Prefer your custom routes; fall back to Fortify endpoints if not registered
    $regenAction = \Illuminate\Support\Facades\Route::has('2fa.codes.regenerate')
        ? route('2fa.codes.regenerate')
        : url('/user/two-factor-recovery-codes'); // Fortify default POST to regenerate

    $downloadHref = \Illuminate\Support\Facades\Route::has('2fa.codes.download')
        ? route('2fa.codes.download')
        : null; // use client-side download when not available

    // If parent includes with ['show' => true], auto-open (we don't reference Blade directive in JS)
    $shouldOpen = isset($show) ? (bool) $show : false;
@endphp

<dialog id="twofa-setup-modal" class="modal">
    <div class="modal-box max-w-2xl bg-base-100">
        <h3 class="font-semibold text-lg">Set up Two-Factor Authentication</h3>
        <p class="mt-1 text-sm text-base-content/70">
            Scan the QR code with Google Authenticator (or any TOTP app), then store your backup codes somewhere safe.
        </p>

        {{-- QR Code --}}
        <div class="mt-5">
            <div class="card bg-base-200 border border-base-300 rounded-xl">
                <div class="card-body items-center">
                    @if($qrSvg)
                        <div class="p-3 bg-base-100 rounded-xl border border-base-300">{!! $qrSvg !!}</div>
                        <p class="text-xs text-base-content/70 mt-2">
                            Scan this code in your authenticator app and enter the 6-digit code below to confirm.
                        </p>
                    @else
                        <p class="text-sm text-base-content/60">QR code unavailable.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Confirm code (Fortify confirm flow) --}}
        <div class="mt-6">
            <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}"
                  class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-end">
                @csrf
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Authenticator 6-digit code</span>
                    </label>
                    <input name="code" inputmode="numeric" autocomplete="one-time-code"
                           placeholder="123456"
                           class="input input-bordered h-12 w-full"
                           required />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            Enter the current code from your authenticator app to finish setup.
                        </span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary h-12">
                    Confirm
                </button>
            </form>
            @error('code') <p class="text-error text-sm mt-2">{{ $message }}</p> @enderror
            @if (session('status') === 'two-factor-authentication-confirmed')
                <div class="alert alert-success mt-3"><span>Two-factor authentication confirmed.</span></div>
            @endif
        </div>

        {{-- Recovery Codes --}}
        <div class="mt-6">
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-base-content">Recovery Codes</h4>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-ghost btn-sm" id="twofa-copy-codes">Copy</button>

                    @if($downloadHref)
                        {{-- Server download (optional route; can be password.confirm protected) --}}
                        <a href="{{ $downloadHref }}" class="btn btn-ghost btn-sm">Download</a>
                    @else
                        {{-- Client-side download fallback --}}
                        <button type="button" class="btn btn-ghost btn-sm" id="twofa-download-codes">Download</button>
                    @endif

                    {{-- Regenerate on server (Fortify or your custom route) --}}
                    <form method="POST" action="{{ $regenAction }}">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Regenerate</button>
                    </form>
                </div>
            </div>

            <div class="mt-3 card bg-base-200 border border-base-300 rounded-xl">
                <div class="card-body">
                    @if(!empty($codes))
                        <ul id="twofa-codes-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2 font-mono text-sm">
                            @foreach($codes as $code)
                                <li class="px-2 py-1 rounded-md bg-base-100 border border-base-300">{{ $code }}</li>
                            @endforeach
                        </ul>
                        <textarea id="twofa-codes-raw" class="sr-only" aria-hidden="true">{{ implode("\n", $codes) }}</textarea>
                        <p class="text-xs text-base-content/70 mt-2">
                            Keep these backup codes somewhere safe. Each code works once if you lose access to your authenticator app.
                        </p>
                    @else
                        <p class="text-sm text-base-content/60">No recovery codes found.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Acknowledge saving --}}
        <div class="form-control mt-6">
            <label class="label cursor-pointer justify-start gap-3">
                <input id="twofa-ack" type="checkbox" class="checkbox checkbox-primary">
                <span class="label-text">I’ve saved my recovery codes.</span>
            </label>
        </div>

        <div class="modal-action">
            <form method="dialog">
                <button id="twofa-close" class="btn btn-primary" disabled>Done</button>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

{{-- Modal JS (DaisyUI-friendly, vanilla).
     IMPORTANT: Do not write Blade directives in JS comments; Blade parses before the browser sees it. --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('twofa-setup-modal');

        // Auto-open when parent included this partial with show=true
        @if($shouldOpen)
        modal?.showModal?.();
        @endif

        // Require acknowledgement before closing
        const ack   = document.getElementById('twofa-ack');
        const close = document.getElementById('twofa-close');
        if (ack && close) {
            ack.addEventListener('change', () => { close.disabled = !ack.checked; });
        }

        // Copy codes to clipboard
        const copyBtn = document.getElementById('twofa-copy-codes');
        const raw     = document.getElementById('twofa-codes-raw');
        copyBtn?.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(raw?.value || '');
                copyBtn.classList.add('btn-success');
                setTimeout(() => copyBtn.classList.remove('btn-success'), 900);
            } catch {}
        });

        // Client-side download (fallback when no server route)
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
