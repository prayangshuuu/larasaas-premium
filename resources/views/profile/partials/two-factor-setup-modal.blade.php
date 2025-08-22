@php
    $status = session('status');
    $justEnabled = $status === 'two-factor-authentication-enabled';
@endphp

<div x-data="{ open: {{ $justEnabled ? 'true' : 'false' }} }">
    <dialog x-ref="dlg" class="modal" :class="open ? 'modal-open' : ''">
        <div class="modal-box max-w-xl">
            <h3 class="font-bold text-lg">Two-Factor Authentication</h3>
            <p class="text-sm text-base-content/70 mt-1">Scan the QR code in Google Authenticator (or Authy). Save your recovery codes.</p>

            <div class="mt-4">
                {{-- QR Code --}}
                <div class="p-4 rounded-xl bg-base-200 grid place-items-center">
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                </div>

                {{-- Recovery codes --}}
                <div class="mt-4">
                    <h4 class="font-medium mb-2">Recovery Codes</h4>
                    <div class="grid sm:grid-cols-2 gap-2">
                        @foreach (auth()->user()->recoveryCodes() as $code)
                            <div class="kbd w-full justify-center">{{ $code }}</div>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('two-factor.recovery-codes') }}" class="mt-3">
                        @csrf
                        <button class="btn btn-outline btn-primary btn-sm">Regenerate Recovery Codes</button>
                    </form>
                </div>
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Done</button>
                </form>
            </div>
        </div>
    </dialog>
</div>
