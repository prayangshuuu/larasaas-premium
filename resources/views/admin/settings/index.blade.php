{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        // Provided by SystemSettingsController@edit
        // $app_name, $app_logo, $app_logo_light, $app_logo_dark, $smtp, $features, $api_tokens

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
        $newTokenPlain       = session('new_token_plain');
        $revealedTokenPlain  = session('revealed_token_plain');
        $revealedTokenId     = session('revealed_token_id');

        // Filename labels
        $lightName = $app_logo_light ? basename($app_logo_light) : ($app_logo ? basename($app_logo) : 'No file chosen');
        $darkName  = $app_logo_dark  ? basename($app_logo_dark)  : ($app_logo ? basename($app_logo)  : 'No file chosen');

        // Initial preview URLs
        $lightInitial = $app_logo_light ? asset($app_logo_light) : ($app_logo ? asset($app_logo) : null);
        $darkInitial  = $app_logo_dark  ? asset($app_logo_dark)  : ($app_logo ? asset($app_logo)  : null);

        // Statuses not shown in global flash
        $tokenCardStatuses = ['settings-api-token-created', 'settings-api-token-revealed', 'settings-api-token-revoked'];
    @endphp

    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header --}}
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white gap-2">System Settings</h1>
            <p class="text-sm text-zinc-400">Manage app identity, SMTP, feature flags, and API keys.</p>
        </div>

        {{-- Global flash --}}
        @if ($status && !in_array($status, $tokenCardStatuses))
            <div class="rounded-md bg-emerald-500/10 p-4 border border-emerald-500/20">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 text-sm text-emerald-400">
                        @switch($status)
                            @case('settings-app-updated') App settings updated. @break
                            @case('settings-app-logo-updated') App logo(s) updated. @break
                            @case('settings-smtp-updated') SMTP settings updated. @break
                            @case('settings-features-updated') Feature flags updated. @break
                            @default {{ $status }}
                        @endswitch
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md bg-red-500/10 p-4 border border-red-500/20">
                 <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 text-sm text-red-400">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        {{-- Application Identity --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-white">Application Identity</h2>
            <p class="text-sm text-zinc-400 mt-1">Upload separate logos for Light &amp; Dark themes.</p>

            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Light Mode Logo --}}
                <form x-data="logoUploader({ initial: @js($lightInitial), submitDelay: 80 })"
                      x-ref="form" method="POST" action="{{ route('admin.settings.app.logo') }}" enctype="multipart/form-data"
                      class="rounded-xl border border-zinc-800 bg-zinc-950/50 p-6">
                    @csrf
                    <div class="flex gap-6">
                        <div class="flex-1">
                            <div class="font-medium text-zinc-200">Light Mode Logo</div>
                            <div class="text-xs text-zinc-500 mt-1">For light themes (black logo recommended).</div>
                            
                            <div class="mt-4">
                                <label for="app_logo_light" class="cursor-pointer inline-flex items-center rounded-md bg-zinc-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700">Choose File</label>
                                <input id="app_logo_light" name="app_logo_light" type="file" class="hidden"
                                       accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                       @change="onChange($event)">
                                <div class="mt-2 text-xs text-zinc-500 font-mono break-all" x-text="fileName || @js($lightName)"></div>
                            </div>
                            @error('app_logo_light') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div class="shrink-0 grid place-items-center">
                            <div class="w-20 h-20 rounded-full border border-zinc-700 bg-white overflow-hidden relative">
                                <img x-show="previewUrl" x-cloak :src="previewUrl" class="absolute inset-0 w-full h-full object-contain p-2">
                                <div x-show="!previewUrl" x-cloak class="absolute inset-0 grid place-items-center text-zinc-300">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Dark Mode Logo --}}
                <form x-data="logoUploader({ initial: @js($darkInitial), submitDelay: 80 })"
                      x-ref="form" method="POST" action="{{ route('admin.settings.app.logo') }}" enctype="multipart/form-data"
                      class="rounded-xl border border-zinc-800 bg-zinc-950/50 p-6">
                    @csrf
                    <div class="flex gap-6">
                        <div class="flex-1">
                            <div class="font-medium text-zinc-200">Dark Mode Logo</div>
                            <div class="text-xs text-zinc-500 mt-1">For dark themes (white logo recommended).</div>
                            
                            <div class="mt-4">
                                <label for="app_logo_dark" class="cursor-pointer inline-flex items-center rounded-md bg-zinc-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700">Choose File</label>
                                <input id="app_logo_dark" name="app_logo_dark" type="file" class="hidden"
                                       accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                       @change="onChange($event)">
                                <div class="mt-2 text-xs text-zinc-500 font-mono break-all" x-text="fileName || @js($darkName)"></div>
                            </div>
                            @error('app_logo_dark') <p class="text-red-400 text-sm mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div class="shrink-0 grid place-items-center">
                            <div class="w-20 h-20 rounded-full border border-zinc-700 bg-zinc-950 overflow-hidden relative">
                                <img x-show="previewUrl" x-cloak :src="previewUrl" class="absolute inset-0 w-full h-full object-contain p-2">
                                <div x-show="!previewUrl" x-cloak class="absolute inset-0 grid place-items-center text-zinc-600">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="my-8 border-t border-zinc-800"></div>

            <form method="POST" action="{{ route('admin.settings.app.update') }}">
                @csrf
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-zinc-300 mb-1">App Name</label>
                    <div class="flex gap-4">
                        <x-ui.input type="text" name="app_name" value="{{ old('app_name', $app_name ?? config('app.name')) }}" required />
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Save</button>
                    </div>
                    @error('app_name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </form>
        </div>

        {{-- SMTP Settings --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-white">SMTP Configuration</h2>
            <p class="text-sm text-zinc-400 mt-1">Mail transport configuration used for notifications.</p>

            <form method="POST" action="{{ route('admin.settings.smtp.update') }}" class="mt-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Host</label>
                        <x-ui.input type="text" name="host" value="{{ old('host', $smtp['host']) }}" />
                        @error('host') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Port</label>
                        <x-ui.input type="number" name="port" value="{{ old('port', $smtp['port']) }}" />
                        @error('port') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Username</label>
                        <x-ui.input type="text" name="username" value="{{ old('username', $smtp['username']) }}" />
                        @error('username') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Password</label>
                        <x-ui.input type="password" name="password" placeholder="••••••••" />
                        <p class="mt-1 text-xs text-zinc-500">Leave blank to keep existing password.</p>
                        @error('password') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Encryption</label>
                        @php $enc = old('encryption', $smtp['encryption']); @endphp
                        <select name="encryption" class="block w-full rounded-md border-0 bg-zinc-950 py-2.5 px-3 text-zinc-300 shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="" {{ $enc === '' ? 'selected' : '' }}>None</option>
                            <option value="tls" {{ $enc === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ $enc === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                        @error('encryption') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">From Address</label>
                        <x-ui.input type="email" name="from_addr" value="{{ old('from_addr', $smtp['from_addr']) }}" />
                        @error('from_addr') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">From Name</label>
                        <x-ui.input type="text" name="from_name" value="{{ old('from_name', $smtp['from_name']) }}" />
                        @error('from_name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Save SMTP Settings</button>
                </div>
            </form>
        </div>

        {{-- Feature Flags --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-white">Features</h2>
            <p class="text-sm text-zinc-400 mt-1">Toggle system capabilities.</p>

            <form method="POST" action="{{ route('admin.settings.features.update') }}" class="mt-8 space-y-6">
                @csrf
                
                <div class="flex items-center justify-between py-4 border-b border-zinc-800/50">
                    <div>
                        <div class="font-medium text-zinc-200">Impersonation</div>
                        <div class="text-sm text-zinc-500">Allow admins to log in as other users.</div>
                    </div>
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="impersonation" value="0">
                        <input type="checkbox" name="impersonation" value="1"
                               class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50 h-5 w-5"
                               {{ old('impersonation', (int)($features['impersonation'])) ? 'checked' : '' }}>
                    </label>
                </div>

                <div class="flex items-center justify-between py-4 border-b border-zinc-800/50">
                    <div>
                        <div class="font-medium text-zinc-200">Editable Usernames</div>
                        <div class="text-sm text-zinc-500">Allow admins to change user handles.</div>
                    </div>
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="allow_username_change" value="0">
                        <input type="checkbox" name="allow_username_change" value="1"
                               class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50 h-5 w-5"
                               {{ old('allow_username_change', (int)($features['allow_username_change'])) ? 'checked' : '' }}>
                    </label>
                </div>

                <div class="flex items-center justify-between py-4">
                    <div>
                        <div class="font-medium text-zinc-200">Require Admin MFA</div>
                        <div class="text-sm text-zinc-500">Admins must have 2FA enabled to access sensitive tools.</div>
                    </div>
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="require_admin_mfa_for_impersonation" value="0">
                        <input type="checkbox" name="require_admin_mfa_for_impersonation" value="1"
                               class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50 h-5 w-5"
                               {{ old('require_admin_mfa_for_impersonation', (int)($features['require_admin_mfa_for_impersonation'])) ? 'checked' : '' }}>
                    </label>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Save Features</button>
                </div>
            </form>
        </div>

        {{-- API Keys --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-white">API Keys</h2>
            <p class="text-sm text-zinc-400 mt-1">Manage <span class="inline-flex items-center rounded-md bg-indigo-400/10 px-2 py-1 text-xs font-medium text-indigo-400 ring-1 ring-inset ring-indigo-400/30">Sanctum</span> personal access tokens.</p>

            {{-- Token Alerts --}}
            @if ($newTokenPlain)
                <div class="mt-6 rounded-md bg-green-500/10 p-4 border border-green-500/20">
                    <div class="font-medium text-green-400">New token created</div>
                    <div class="text-xs text-green-500/80 mb-3">Copy it now — you won’t be able to see it again.</div>
                    <div class="flex gap-2" x-data="{ show: false }">
                        <input id="newTokenPlain" :type="show ? 'text' : 'password'"
                               class="block w-full rounded-md border-0 bg-green-950/30 py-1.5 text-green-400 font-mono text-sm ring-1 ring-inset ring-green-500/30 focus:ring-2 focus:ring-inset focus:ring-green-500"
                               value="{{ $newTokenPlain }}" readonly>
                        <button type="button" class="px-3 py-1.5 text-sm font-semibold text-green-400 bg-green-500/10 rounded-md hover:bg-green-500/20" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                        <button type="button" class="px-3 py-1.5 text-sm font-semibold text-green-400 bg-green-500/10 rounded-md hover:bg-green-500/20" onclick="copyText('newTokenPlain', this)">Copy</button>
                    </div>
                </div>
            @endif

            @if ($revealedTokenPlain)
                <div class="mt-6 rounded-md bg-blue-500/10 p-4 border border-blue-500/20">
                     <div class="font-medium text-blue-400">Token revealed</div>
                    <div class="text-xs text-blue-500/80 mb-3">This is your token value.</div>
                     <div class="flex gap-2" x-data="{ show: false }">
                        <input id="revealedTokenPlain" :type="show ? 'text' : 'password'"
                               class="block w-full rounded-md border-0 bg-blue-950/30 py-1.5 text-blue-400 font-mono text-sm ring-1 ring-inset ring-blue-500/30 focus:ring-2 focus:ring-inset focus:ring-blue-500"
                               value="{{ $revealedTokenPlain }}" readonly>
                        <button type="button" class="px-3 py-1.5 text-sm font-semibold text-blue-400 bg-blue-500/10 rounded-md hover:bg-blue-500/20" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                        <button type="button" class="px-3 py-1.5 text-sm font-semibold text-blue-400 bg-blue-500/10 rounded-md hover:bg-blue-500/20" onclick="copyText('revealedTokenPlain', this)">Copy</button>
                    </div>
                </div>
            @endif

            <div class="my-8 border-t border-zinc-800"></div>

            {{-- Create Token --}}
            <form method="POST" action="{{ route('admin.settings.api.create') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Token Name</label>
                    <x-ui.input type="text" name="token_name" value="{{ old('token_name', 'CLI') }}" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Abilities</label>
                    <x-ui.input type="text" name="abilities" value="{{ old('abilities', '*') }}" placeholder="*, read,write" />
                    <p class="text-xs text-zinc-500 mt-1">Use <code>*</code> for full access.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Expires</label>
                    <select name="expires" class="block w-full rounded-md border-0 bg-zinc-950 py-2.5 px-3 text-zinc-300 shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">Never</option>
                        <option value="7 days"  {{ old('expires')==='7 days'  ? 'selected' : '' }}>7 days</option>
                        <option value="30 days" {{ old('expires')==='30 days' ? 'selected' : '' }}>30 days</option>
                        <option value="90 days" {{ old('expires')==='90 days' ? 'selected' : '' }}>90 days</option>
                    </select>
                </div>
                <div class="lg:col-span-3 flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Generate Token</button>
                </div>
            </form>

            <div class="my-8 border-t border-zinc-800"></div>

            {{-- List Tokens --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-800 text-left">
                    <thead>
                    <tr class="text-zinc-400 text-xs uppercase tracking-wider bg-zinc-900/50">
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Abilities</th>
                        <th class="px-4 py-3 font-medium">Last Used</th>
                        <th class="px-4 py-3 font-medium">Expires</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                    @forelse ($api_tokens as $t)
                        <tr class="hover:bg-zinc-800/50 transition-colors {{ $revealedTokenId && (int)$revealedTokenId === (int)$t->id ? 'bg-indigo-500/5' : '' }}">
                            <td class="px-4 py-4 text-sm font-medium text-white">{{ $t->name }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-400 font-mono text-xs">
                                {{ implode(',', is_array($t->abilities) ? $t->abilities : (array)$t->abilities) ?: '*' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-500">{{ $t->last_used_at ? $t->last_used_at->format('d M Y, H:i') : '—' }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-500">{{ $t->expires_at ? $t->expires_at->format('d M Y, H:i') : 'never' }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-500">{{ $t->created_at ? $t->created_at->format('d M Y, H:i') : '—' }}</td>
                            <td class="px-4 py-4 text-sm text-right">
                                <div class="flex justify-end gap-3">
                                    <form method="POST" action="{{ route('admin.settings.api.reveal', $t->id) }}">
                                        @csrf
                                        <button type="submit" class="text-zinc-400 hover:text-white transition-colors">Reveal</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.settings.api.revoke', $t->id) }}" onsubmit="return confirm('Revoke token {{ $t->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors">Revoke</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-zinc-500 py-8">No API tokens yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

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
            const wasPassword = el.type === 'password';
            if (wasPassword) el.type = 'text';
            const text = (el.value || el.textContent || '').trim();
            if (wasPassword) el.type = 'password';
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const old = btn.innerText;
                btn.innerText = 'Copied';
                setTimeout(() => { btn.innerText = old; }, 900);
            });
        }
    </script>
@endsection
