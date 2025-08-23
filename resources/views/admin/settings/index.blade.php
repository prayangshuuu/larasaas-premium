{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        // Provided by SystemSettingsController@edit:
        // $app_name, $app_logo (legacy), $app_logo_light, $app_logo_dark
        // $smtp (array), $features (array), $api_tokens (current admin's Sanctum tokens)

        $status = session('status');

        // Defensive defaults
        $smtp = array_merge([
            'host' => '', 'port' => '', 'username' => '', 'encryption' => '',
            'from_addr' => '', 'from_name' => '', 'password' => null,
        ], $smtp ?? []);
        $features = array_merge([
            'impersonation' => 0,
            'allow_username_change' => 1,
            'require_admin_mfa_for_impersonation' => 1,
        ], $features ?? []);

        /** @var \Illuminate\Support\Collection $api_tokens */
        $api_tokens = $api_tokens ?? collect();

        // Token flashes
        $newTokenPlain       = session('new_token_plain');        // after create
        $revealedTokenPlain  = session('revealed_token_plain');   // after reveal (anytime)
        $revealedTokenId     = session('revealed_token_id');      // which one got revealed (for UI focus)

        // Filename labels
        $lightName = $app_logo_light ? basename($app_logo_light) : ($app_logo ? basename($app_logo) : 'No file chosen');
        $darkName  = $app_logo_dark  ? basename($app_logo_dark)  : ($app_logo ? basename($app_logo)  : 'No file chosen');

        // Initial preview URLs (with legacy fallback)
        $lightInitial = $app_logo_light ? asset($app_logo_light) : ($app_logo ? asset($app_logo) : null);
        $darkInitial  = $app_logo_dark  ? asset($app_logo_dark)  : ($app_logo ? asset($app_logo)  : null);

        // statuses we will NOT show in the global flash (they render inside API Keys card)
        $tokenCardStatuses = ['settings-api-token-created', 'settings-api-token-revealed', 'settings-api-token-revoked'];
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-base-content">System Settings</h1>
                <p class="text-sm text-base-content/70">Manage app identity, SMTP, feature flags, and API keys.</p>
            </div>
        </div>

        {{-- Global flash (skip token events; those render in the API Keys card) --}}
        @if ($status && !in_array($status, $tokenCardStatuses))
            <div class="alert alert-success rounded-2xl">
                <span>
                    @switch($status)
                        @case('settings-app-updated') App settings updated. @break
                        @case('settings-app-logo-updated') App logo(s) updated. @break
                        @case('settings-smtp-updated') SMTP settings updated. @break
                        @case('settings-features-updated') Feature flags updated. @break
                        @default {{ $status }}
                    @endswitch
                </span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error rounded-2xl">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- =========================
             Application (dual logos)
           ========================= --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">

                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title text-base-content text-lg">Application Identity</h2>
                        <p class="mt-1 text-sm text-base-content/70">
                            Upload separate logos for Light &amp; Dark themes. Logos are shown centered inside a circle.
                        </p>
                    </div>

                    <div class="hidden sm:flex items-center gap-2 text-xs text-base-content/60">
                        <span>Live preview</span>
                        <span class="badge badge-ghost">
                            theme:
                            <span x-data="{t:(localStorage.getItem('theme')||'nord')}"
                                  x-init="setInterval(()=>t=localStorage.getItem('theme')||'nord',400)"
                                  x-text="t">nord</span>
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Light mode (black logo) --}}
                    <form
                        x-data="logoUploader({ initial: @js($lightInitial), submitDelay: 80 })"
                        x-ref="form"
                        method="POST"
                        action="{{ route('admin.settings.app.logo') }}"
                        enctype="multipart/form-data"
                        class="rounded-2xl border border-base-300 bg-base-200/40 p-5"
                    >
                        @csrf
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <div class="font-medium">Light Mode (Black Logo)</div>
                                <div class="text-xs text-base-content/60">Shown on light theme (e.g., “nord”).</div>

                                {{-- DaisyUI file input + live filename --}}
                                <div class="mt-3 join w-full">
                                    <label for="app_logo_light" class="btn btn-ghost join-item">Choose File</label>
                                    <input id="app_logo_light" name="app_logo_light" type="file" class="sr-only"
                                           accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                           @change="onChange($event)">
                                    <input type="text"
                                           class="input input-bordered join-item w-full"
                                           :value="fileName || @js($lightName)"
                                           readonly>
                                </div>
                                @error('app_logo_light') <p class="text-error text-sm mt-2">{{ $message }}</p> @enderror
                            </div>

                            {{-- Circle preview (perfectly centered) --}}
                            <div class="shrink-0">
                                <div class="avatar">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border border-base-300 bg-base-200 overflow-hidden relative">
                                        <img x-show="previewUrl" x-cloak :src="previewUrl" alt="Light logo preview"
                                             class="absolute inset-0 w-full h-full object-contain pointer-events-none select-none">
                                        <div x-show="!previewUrl" x-cloak class="absolute inset-0 grid place-items-center">
                                            <x-application-logo class="w-10 h-10 opacity-60" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Dark mode (white logo) --}}
                    <form
                        x-data="logoUploader({ initial: @js($darkInitial), submitDelay: 80 })"
                        x-ref="form"
                        method="POST"
                        action="{{ route('admin.settings.app.logo') }}"
                        enctype="multipart/form-data"
                        class="rounded-2xl border border-base-300 bg-base-200/40 p-5"
                    >
                        @csrf
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <div class="font-medium">Dark Mode (White Logo)</div>
                                <div class="text-xs text-base-content/60">Shown on dark theme (e.g., “dim”).</div>

                                <div class="mt-3 join w-full">
                                    <label for="app_logo_dark" class="btn btn-ghost join-item">Choose File</label>
                                    <input id="app_logo_dark" name="app_logo_dark" type="file" class="sr-only"
                                           accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                           @change="onChange($event)">
                                    <input type="text"
                                           class="input input-bordered join-item w-full"
                                           :value="fileName || @js($darkName)"
                                           readonly>
                                </div>
                                @error('app_logo_dark') <p class="text-error text-sm mt-2">{{ $message }}</p> @enderror
                            </div>

                            <div class="shrink-0">
                                <div class="avatar">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border border-base-300 bg-base-200 overflow-hidden relative">
                                        <img x-show="previewUrl" x-cloak :src="previewUrl" alt="Dark logo preview"
                                             class="absolute inset-0 w-full h-full object-contain pointer-events-none select-none">
                                        <div x-show="!previewUrl" x-cloak class="absolute inset-0 grid place-items-center">
                                            <x-application-logo class="w-10 h-10 opacity-60" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="divider my-8"></div>

                {{-- App name --}}
                <form method="POST" action="{{ route('admin.settings.app.update') }}" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label class="label"><span class="label-text">App name</span></label>
                        <input type="text" name="app_name" class="input input-bordered h-12 w-full"
                               value="{{ old('app_name', $app_name ?? config('app.name')) }}" required />
                        @error('app_name') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="card-actions justify-end pt-2">
                        <button type="submit" class="btn btn-primary h-12 min-w-40">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- =========================
             SMTP
           ========================= --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <h2 class="card-title text-base-content text-lg">SMTP</h2>
                <p class="mt-1 text-sm text-base-content/70">Mail transport configuration used for notifications.</p>

                <div class="divider my-6"></div>

                <form method="POST" action="{{ route('admin.settings.smtp.update') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Host</span></label>
                            <input type="text" name="host" class="input input-bordered h-12 w-full"
                                   value="{{ old('host', $smtp['host']) }}" />
                            @error('host') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Port</span></label>
                            <input type="number" name="port" class="input input-bordered h-12 w-full"
                                   value="{{ old('port', $smtp['port']) }}" />
                            @error('port') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Username</span></label>
                            <input type="text" name="username" class="input input-bordered h-12 w-full"
                                   value="{{ old('username', $smtp['username']) }}" />
                            @error('username') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Password</span></label>
                            <input type="password" name="password" class="input input-bordered h-12 w-full"
                                   placeholder="••••••••" />
                            <label class="label"><span class="label-text-alt text-base-content/60">Leave blank to keep existing password.</span></label>
                            @error('password') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Encryption</span></label>
                            @php $enc = old('encryption', $smtp['encryption']); @endphp
                            <select name="encryption" class="select select-bordered h-12 w-full">
                                <option value="" {{ $enc === '' ? 'selected' : '' }}>None</option>
                                <option value="tls" {{ $enc === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ $enc === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                            @error('encryption') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">From address</span></label>
                            <input type="email" name="from_addr" class="input input-bordered h-12 w-full"
                                   value="{{ old('from_addr', $smtp['from_addr']) }}" />
                            @error('from_addr') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">From name</span></label>
                            <input type="text" name="from_name" class="input input-bordered h-12 w-full"
                                   value="{{ old('from_name', $smtp['from_name']) }}" />
                            @error('from_name') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="card-actions justify-end pt-2">
                        <button type="submit" class="btn btn-primary h-12 min-w-40">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- =========================
             Feature Flags
           ========================= --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <h2 class="card-title text-base-content text-lg">Features</h2>
                <p class="mt-1 text-sm text-base-content/70">Toggle guarded capabilities.</p>

                <div class="divider my-6"></div>

                <form method="POST" action="{{ route('admin.settings.features.update') }}" class="space-y-5">
                    @csrf
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="font-medium">Impersonation</div>
                            <div class="text-sm text-base-content/70">Allow admins to impersonate users.</div>
                        </div>
                        <div class="form-control">
                            <input type="hidden" name="impersonation" value="0">
                            <input type="checkbox" name="impersonation" value="1"
                                   class="toggle toggle-primary"
                                {{ old('impersonation', (int)($features['impersonation'])) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="font-medium">Editable usernames</div>
                            <div class="text-sm text-base-content/70">Allow admins to change usernames.</div>
                        </div>
                        <div class="form-control">
                            <input type="hidden" name="allow_username_change" value="0">
                            <input type="checkbox" name="allow_username_change" value="1"
                                   class="toggle toggle-primary"
                                {{ old('allow_username_change', (int)($features['allow_username_change'])) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="font-medium">Require admin MFA for impersonation</div>
                            <div class="text-sm text-base-content/70">Admin must have 2FA enabled to impersonate.</div>
                        </div>
                        <div class="form-control">
                            <input type="hidden" name="require_admin_mfa_for_impersonation" value="0">
                            <input type="checkbox" name="require_admin_mfa_for_impersonation" value="1"
                                   class="toggle toggle-primary"
                                {{ old('require_admin_mfa_for_impersonation', (int)($features['require_admin_mfa_for_impersonation'])) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="card-actions justify-end pt-2">
                        <button type="submit" class="btn btn-primary h-12 min-w-40">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- =========================
             API Keys (Sanctum)
           ========================= --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title text-base-content text-lg">API Keys</h2>
                        <p class="mt-1 text-sm text-base-content/70">
                            Generate and manage <span class="badge badge-primary badge-sm">Sanctum</span> personal access tokens.
                        </p>
                    </div>
                </div>

                {{-- Token alerts --}}
                @if ($newTokenPlain)
                    <div class="alert alert-success rounded-xl mt-4">
                        <div class="flex-1">
                            <div class="font-medium">New token created</div>
                            <div class="text-xs text-base-content/70">Copy it now — you won’t be able to see it again unless you enabled reveal storage.</div>
                            <div class="mt-3 join w-full lg:max-w-2xl" x-data="{ show: false }">
                                <span class="join-item btn btn-ghost btn-xs">Token</span>
                                <input id="newTokenPlain" :type="show ? 'text' : 'password'"
                                       class="join-item input input-bordered input-xs w-full font-mono"
                                       value="{{ $newTokenPlain }}" readonly>
                                <button type="button" class="join-item btn btn-xs" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                                <button type="button" class="join-item btn btn-xs" onclick="copyText('newTokenPlain', this)">Copy</button>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($revealedTokenPlain)
                    <div class="alert alert-info rounded-xl mt-4">
                        <div class="flex-1">
                            <div class="font-medium">Token revealed</div>
                            <div class="text-xs text-base-content/70">This is your token value. Keep it secret.</div>
                            <div class="mt-3 join w-full lg:max-w-2xl" x-data="{ show: false }">
                                <span class="join-item btn btn-ghost btn-xs">Token</span>
                                <input id="revealedTokenPlain" :type="show ? 'text' : 'password'"
                                       class="join-item input input-bordered input-xs w-full font-mono"
                                       value="{{ $revealedTokenPlain }}" readonly>
                                <button type="button" class="join-item btn btn-xs" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                                <button type="button" class="join-item btn btn-xs" onclick="copyText('revealedTokenPlain', this)">Copy</button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="divider my-6"></div>

                {{-- Create token --}}
                <form method="POST" action="{{ route('admin.settings.api.create') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    @csrf
                    <div class="form-control">
                        <label class="label"><span class="label-text">Token name</span></label>
                        <input name="token_name" type="text" class="input input-bordered h-12 w-full"
                               value="{{ old('token_name', 'CLI') }}" required>
                        @error('token_name') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Abilities</span></label>
                        <input name="abilities" type="text" class="input input-bordered h-12 w-full font-mono"
                               value="{{ old('abilities', '*') }}" placeholder="*, read,write">
                        <label class="label"><span class="label-text-alt text-base-content/60">CSV. Use <code>*</code> for full access.</span></label>
                        @error('abilities') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Expires</span></label>
                        <select name="expires" class="select select-bordered h-12 w-full">
                            <option value="">Never</option>
                            <option value="7 days"  {{ old('expires')==='7 days'  ? 'selected' : '' }}>7 days</option>
                            <option value="30 days" {{ old('expires')==='30 days' ? 'selected' : '' }}>30 days</option>
                            <option value="90 days" {{ old('expires')==='90 days' ? 'selected' : '' }}>90 days</option>
                        </select>
                        @error('expires') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="lg:col-span-3 flex justify-end">
                        <button type="submit" class="btn btn-primary h-11 min-w-40">Generate Token</button>
                    </div>
                </form>

                <div class="divider my-6"></div>

                {{-- List tokens --}}
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-base-content/70">Name</th>
                            <th class="text-base-content/70">Abilities</th>
                            <th class="text-base-content/70">Last used</th>
                            <th class="text-base-content/70">Expires</th>
                            <th class="text-base-content/70">Created</th>
                            <th class="text-base-content/70 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($api_tokens as $t)
                            <tr @class([
                                    'bg-base-200/40' => $revealedTokenId && (int)$revealedTokenId === (int)$t->id, // highlight the revealed one
                                ])>
                                <td class="font-medium">{{ $t->name }}</td>
                                <td class="font-mono text-sm">
                                    @php
                                        $abilities = is_array($t->abilities) ? $t->abilities : (array) $t->abilities;
                                    @endphp
                                    {{ implode(',', $abilities) ?: '*' }}
                                </td>
                                <td class="text-base-content/70">
                                    {{ $t->last_used_at ? $t->last_used_at->format('d M Y, H:i') : '—' }}
                                </td>
                                <td class="text-base-content/70">
                                    {{ $t->expires_at ? $t->expires_at->format('d M Y, H:i') : 'never' }}
                                </td>
                                <td class="text-base-content/70">
                                    {{ $t->created_at ? $t->created_at->format('d M Y, H:i') : '—' }}
                                </td>
                                <td class="text-right">
                                    <div class="join">
                                        {{-- Reveal (always available; controller will authorize/guard) --}}
                                        <form method="POST" action="{{ route('admin.settings.api.reveal', $t->id) }}" class="join-item">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-ghost">Reveal</button>
                                        </form>

                                        {{-- Revoke --}}
                                        <form method="POST" action="{{ route('admin.settings.api.revoke', $t->id) }}"
                                              class="join-item"
                                              onsubmit="return confirm('Revoke token {{ $t->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-error">Revoke</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-base-content/60 py-6">
                                    No API tokens yet.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    {{-- Alpine helper for the upload widgets + tiny copy util (no external libs) --}}
    <script>
        function logoUploader({ initial = null, submitDelay = 50 } = {}) {
            return {
                fileName: '',
                previewUrl: initial || null,
                onChange(e) {
                    const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                    if (!file) return;
                    this.fileName = file.name;

                    if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                    this.previewUrl = URL.createObjectURL(file);

                    setTimeout(() => this.$refs.form.submit(), submitDelay);
                }
            }
        }
        function copyText(inputId, btn) {
            const el = document.getElementById(inputId);
            if (!el) return;
            // If it's a password input and hidden, temporarily switch to text to copy reliable value
            const wasPassword = el.type === 'password';
            if (wasPassword) el.type = 'text';
            const text = (el.value || el.textContent || '').trim();
            if (wasPassword) el.type = 'password';

            if (!text) return;

            navigator.clipboard.writeText(text).then(() => {
                btn.classList.add('btn-success');
                const old = btn.textContent;
                btn.textContent = 'Copied';
                setTimeout(() => { btn.classList.remove('btn-success'); btn.textContent = old; }, 900);
            });
        }
    </script>
@endsection
