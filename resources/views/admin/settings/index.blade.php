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
                            @case('settings-social-updated') Social authentication settings updated. @break
                            @case('settings-support-updated') Support settings updated. @break
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

        {{-- Platform Features & Modules --}}
        @php
            $socialEnabled = old('social_login_enabled', (int)($social['enabled'] ?? 0));
            $googleEnabled = old('google_login_enabled', (int)($social['google_enabled'] ?? 0));
            $facebookEnabled = old('facebook_login_enabled', (int)($social['facebook_enabled'] ?? 0));
            $twitterEnabled = old('twitter_login_enabled', (int)($social['twitter_enabled'] ?? 0));
        @endphp
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8"
             x-data="{
                stripeEnabled: {{ old('stripe_payment_enabled', (int)$features['stripe_payment_enabled']) ? 'true' : 'false' }},
                bkashEnabled: {{ old('bkash_enabled', (int)($features['bkash_enabled'] ?? 0)) ? 'true' : 'false' }},
                subscriptionEnabled: {{ old('subscription_module_enabled', (int)$features['subscription_module_enabled']) ? 'true' : 'false' }},
                impersonationEnabled: {{ old('impersonation', (int)$features['impersonation']) ? 'true' : 'false' }},
                usernameChangeEnabled: {{ old('allow_username_change', (int)$features['allow_username_change']) ? 'true' : 'false' }},
                adminMfaEnabled: {{ old('require_admin_mfa_for_impersonation', (int)$features['require_admin_mfa_for_impersonation']) ? 'true' : 'false' }},
                supportEnabled: {{ old('support_enabled', (int)($support['enabled'] ?? 0)) ? 'true' : 'false' }},
                autoReplyEnabled: {{ old('support_auto_reply_enabled', (int)($support['auto_reply_enabled'] ?? 0)) ? 'true' : 'false' }},
                socialLoginEnabled: {{ $socialEnabled ? 'true' : 'false' }},
                googleEnabled: {{ $googleEnabled ? 'true' : 'false' }},
                facebookEnabled: {{ $facebookEnabled ? 'true' : 'false' }},
                twitterEnabled: {{ $twitterEnabled ? 'true' : 'false' }}
             }">
            <h2 class="text-xl font-semibold text-white">Platform Features & Modules</h2>
            <p class="text-sm text-zinc-400 mt-1">Configure global system modules and feature flags.</p>

            <form method="POST" action="{{ route('admin.settings.features.update') }}" enctype="multipart/form-data" class="mt-8 space-y-0 divide-y divide-zinc-800/50">
                @csrf

                {{-- 0. Announcement System --}}
                <div class="flex items-center justify-between py-4"
                     x-data="{ enabled: {{ old('announcement_enabled', (int)($features['announcement_enabled'] ?? 1)) ? 'true' : 'false' }} }">
                    <div>
                        <div class="font-medium text-zinc-200">Enable Announcements & Changelog</div>
                        <div class="text-sm text-zinc-500">Enable the public changelog and user notification banner.</div>
                    </div>
                    <input type="hidden" name="announcement_enabled" :value="enabled ? 1 : 0">
                    <button type="button" 
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900" 
                            :class="{ 'bg-indigo-600': enabled, 'bg-zinc-700': !enabled }"
                            @click="enabled = !enabled">
                        <span class="sr-only">Use setting</span>
                        <span aria-hidden="true" 
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="{ 'translate-x-5': enabled, 'translate-x-0': !enabled }"></span>
                    </button>
                </div>

                {{-- 0.5. Team Management (Multi-tenancy) --}}
                <div class="flex items-center justify-between py-4"
                     x-data="{ enabled: {{ old('team_management_enabled', (int)($features['team_management_enabled'] ?? 0)) ? 'true' : 'false' }} }">
                    <div>
                        <div class="font-medium text-zinc-200">Enable Team Management</div>
                        <div class="text-sm text-zinc-500">Allow users to create and manage teams (multi-tenancy).</div>
                    </div>
                    <input type="hidden" name="team_management_enabled" :value="enabled ? 1 : 0">
                    <button type="button" 
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900" 
                            :class="{ 'bg-indigo-600': enabled, 'bg-zinc-700': !enabled }"
                            @click="enabled = !enabled">
                        <span class="sr-only">Use setting</span>
                        <span aria-hidden="true" 
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="{ 'translate-x-5': enabled, 'translate-x-0': !enabled }"></span>
                    </button>
                </div>

                {{-- 1. Subscription Module --}}
                <div class="flex items-center justify-between py-4">
                    <div>
                        <div class="font-medium text-zinc-200">Enable Subscription Module</div>
                        <div class="text-sm text-zinc-500">Enable or disable the entire subscription & billing system.</div>
                    </div>
                    <input type="hidden" name="subscription_module_enabled" :value="subscriptionEnabled ? 1 : 0">
                    <button type="button" 
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900" 
                            :class="{ 'bg-indigo-600': subscriptionEnabled, 'bg-zinc-700': !subscriptionEnabled }"
                            @click="subscriptionEnabled = !subscriptionEnabled">
                        <span class="sr-only">Use setting</span>
                        <span aria-hidden="true" 
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="{ 'translate-x-5': subscriptionEnabled, 'translate-x-0': !subscriptionEnabled }"></span>
                    </button>
                </div>





                {{-- 2. Payment Gateways --}}
                @php
                    $paymentGatewaysEnabled = old('payment_gateways_enabled', (int)($features['payment_gateways_enabled'] ?? 0));
                    $stripeLogoName = isset($features['stripe_logo']) ? basename($features['stripe_logo']) : 'Default';
                    $bkashLogoName  = isset($features['bkash_logo'])  ? basename($features['bkash_logo'])  : 'Default';
                    $stripeLogoUrl  = isset($features['stripe_logo']) ? asset($features['stripe_logo']) : null;
                    $bkashLogoUrl   = isset($features['bkash_logo'])  ? asset($features['bkash_logo'])  : null;
                @endphp
                <div class="py-4" x-data="{
                        paymentGatewaysEnabled: {{ $paymentGatewaysEnabled ? 'true' : 'false' }}
                     }">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-zinc-200">Enable Payment Gateways</div>
                            <div class="text-sm text-zinc-500">Configure accepted payment methods and gateways.</div>
                        </div>
                        <input type="hidden" name="payment_gateways_enabled" :value="paymentGatewaysEnabled ? 1 : 0">
                        <button type="button"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                                :class="{ 'bg-indigo-600': paymentGatewaysEnabled, 'bg-zinc-700': !paymentGatewaysEnabled }"
                                @click="paymentGatewaysEnabled = !paymentGatewaysEnabled">
                            <span class="sr-only">Use setting</span>
                            <span aria-hidden="true"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                  :class="{ 'translate-x-5': paymentGatewaysEnabled, 'translate-x-0': !paymentGatewaysEnabled }"></span>
                        </button>
                    </div>

                    {{-- Gateways (visible when enabled) --}}
                    <div x-show="paymentGatewaysEnabled" x-transition.opacity.duration.300ms class="mt-6 pl-4 border-l-2 border-indigo-600/50 space-y-6">

                        {{-- Stripe --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                     <svg class="h-6 w-6 text-[#635BFF]" viewBox="0 0 32 32" fill="currentColor"><path d="M13.9 16.2c0 1.9 1.4 2.6 3.9 2.6 4.6 0 5.4-1.9 5.4-1.9l2.8 1.8s-1.8 3.5-8.2 3.5c-5.4 0-8.6-2.7-8.6-7.2 0-4.9 3.5-7.7 8.5-7.7 7.7 0 7.8 6.5 7.8 6.8H14c-0.1 1.2 0.3 2.1 -0.1 2.1zM17.7 9.8c-2.3 0-3.3 1.2-3.6 2.3h6.8c0-1.2-1.2-2.3-3.2-2.3z"/></svg>
                                     <span class="font-medium text-zinc-200">Enable Stripe</span>
                                </div>
                                <input type="hidden" name="stripe_payment_enabled" :value="stripeEnabled ? 1 : 0">
                                <button type="button"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                                        :class="{ 'bg-indigo-600': stripeEnabled, 'bg-zinc-700': !stripeEnabled }"
                                        @click="stripeEnabled = !stripeEnabled">
                                    <span class="sr-only">Use setting</span>
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="{ 'translate-x-5': stripeEnabled, 'translate-x-0': !stripeEnabled }"></span>
                                </button>
                            </div>

                            <div x-show="stripeEnabled" x-transition.opacity.duration.300ms class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Logo Upload --}}
                                <div x-data="imagePreview(@js($stripeLogoUrl))" class="md:col-span-2 flex gap-6 items-start p-4 rounded-md bg-zinc-950/30 border border-zinc-800/50">
                                     <div class="flex-1">
                                        <label class="block text-sm font-medium text-zinc-300 mb-1">Custom Stripe Logo</label>
                                        <div class="flex items-center gap-3">
                                            <label for="stripe_logo" class="cursor-pointer inline-flex items-center rounded-md bg-zinc-800 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700">Change Logo</label>
                                            <input id="stripe_logo" name="stripe_logo" type="file" class="hidden" accept="image/*" @change="onChange($event)">
                                            <span class="text-xs text-zinc-500 font-mono" x-text="fileName || '{{ $stripeLogoName }}'"></span>
                                        </div>
                                        <p class="text-xs text-zinc-500 mt-1">Displayed on checkout page instead of default.</p>
                                     </div>
                                     <div class="shrink-0 w-16 h-16 rounded border border-zinc-700 bg-white grid place-items-center overflow-hidden">
                                          <img x-show="previewUrl" :src="previewUrl" class="w-full h-full object-contain p-1">
                                          <svg x-show="!previewUrl" class="w-8 h-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                     </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Stripe Public Key</label>
                                    <x-ui.input type="text" name="stripe_key" value="{{ old('stripe_key', $features['stripe_key'] ?? '') }}" placeholder="pk_test_..." />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Stripe Secret Key</label>
                                    <x-ui.input type="password" name="stripe_secret" value="{{ old('stripe_secret', $features['stripe_secret'] ?? '') }}" placeholder="sk_test_..." />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Webhook Secret</label>
                                    <x-ui.input type="password" name="stripe_webhook_secret" value="{{ old('stripe_webhook_secret', $features['stripe_webhook_secret'] ?? '') }}" placeholder="whsec_..." />
                                </div>
                            </div>
                        </div>

                        {{-- Bkash (Manual) --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                     <svg class="h-6 w-6 text-[#E2136E]" viewBox="0 0 24 24" fill="currentColor"><path d="M12.9 2L15.6 9.4H22L16.2 13.8L18.7 21.2L11 16L3.3 21.2L5.8 13.8L0 9.4H6.4L9.1 2H12.9Z"/></svg>
                                     <span class="font-medium text-zinc-200">Enable Bkash (Manual)</span>
                                </div>
                                <input type="hidden" name="bkash_enabled" :value="bkashEnabled ? 1 : 0">
                                <button type="button"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                                        :class="{ 'bg-indigo-600': bkashEnabled, 'bg-zinc-700': !bkashEnabled }"
                                        @click="bkashEnabled = !bkashEnabled">
                                    <span class="sr-only">Use setting</span>
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="{ 'translate-x-5': bkashEnabled, 'translate-x-0': !bkashEnabled }"></span>
                                </button>
                            </div>

                            <div x-show="bkashEnabled" x-transition.opacity.duration.300ms class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Logo Upload --}}
                                <div x-data="imagePreview(@js($bkashLogoUrl))" class="md:col-span-2 flex gap-6 items-start p-4 rounded-md bg-zinc-950/30 border border-zinc-800/50">
                                     <div class="flex-1">
                                        <label class="block text-sm font-medium text-zinc-300 mb-1">Custom Bkash Logo</label>
                                        <div class="flex items-center gap-3">
                                            <label for="bkash_logo" class="cursor-pointer inline-flex items-center rounded-md bg-zinc-800 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700">Change Logo</label>
                                            <input id="bkash_logo" name="bkash_logo" type="file" class="hidden" accept="image/*" @change="onChange($event)">
                                            <span class="text-xs text-zinc-500 font-mono" x-text="fileName || '{{ $bkashLogoName }}'"></span>
                                        </div>
                                     </div>
                                     <div class="shrink-0 w-16 h-16 rounded border border-zinc-700 bg-white grid place-items-center overflow-hidden">
                                          <img x-show="previewUrl" :src="previewUrl" class="w-full h-full object-contain p-1">
                                          <svg x-show="!previewUrl" class="w-8 h-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                     </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Admin Bkash Number</label>
                                    <x-ui.input type="text" name="bkash_admin_number" value="{{ old('bkash_admin_number', $features['bkash_admin_number'] ?? '') }}" placeholder="01XXXXXXXXX" />
                                    <p class="text-xs text-zinc-500 mt-1">Users will send money to this number.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Payment Instructions</label>
                                    <textarea name="bkash_instruction" rows="3" class="block w-full rounded-md border-0 bg-zinc-950 py-1.5 text-zinc-300 shadow-sm ring-1 ring-inset ring-zinc-800 placeholder:text-zinc-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('bkash_instruction', $features['bkash_instruction'] ?? '') }}</textarea>
                                    <p class="text-xs text-zinc-500 mt-1">Check *247#... etc.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                {{-- 3. Impersonation --}}
                <div class="flex items-center justify-between py-4">
                    <div>
                        <div class="font-medium text-zinc-200">Impersonation</div>
                        <div class="text-sm text-zinc-500">Allow admins to log in as other users.</div>
                    </div>
                    <input type="hidden" name="impersonation" :value="impersonationEnabled ? 1 : 0">
                    <button type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                            :class="{ 'bg-indigo-600': impersonationEnabled, 'bg-zinc-700': !impersonationEnabled }"
                            @click="impersonationEnabled = !impersonationEnabled">
                        <span class="sr-only">Use setting</span>
                        <span aria-hidden="true"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="{ 'translate-x-5': impersonationEnabled, 'translate-x-0': !impersonationEnabled }"></span>
                    </button>
                </div>

                {{-- 4. Editable Usernames --}}
                <div class="flex items-center justify-between py-4">
                    <div>
                        <div class="font-medium text-zinc-200">Editable Usernames</div>
                        <div class="text-sm text-zinc-500">Allow admins to change user handles.</div>
                    </div>
                    <input type="hidden" name="allow_username_change" :value="usernameChangeEnabled ? 1 : 0">
                    <button type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                            :class="{ 'bg-indigo-600': usernameChangeEnabled, 'bg-zinc-700': !usernameChangeEnabled }"
                            @click="usernameChangeEnabled = !usernameChangeEnabled">
                        <span class="sr-only">Use setting</span>
                        <span aria-hidden="true"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="{ 'translate-x-5': usernameChangeEnabled, 'translate-x-0': !usernameChangeEnabled }"></span>
                    </button>
                </div>

                {{-- 5. Require Admin MFA --}}
                <div class="flex items-center justify-between py-4">
                    <div>
                        <div class="font-medium text-zinc-200">Require Admin MFA</div>
                        <div class="text-sm text-zinc-500">Admins must have 2FA enabled to access sensitive tools.</div>
                    </div>
                    <input type="hidden" name="require_admin_mfa_for_impersonation" :value="adminMfaEnabled ? 1 : 0">
                    <button type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                            :class="{ 'bg-indigo-600': adminMfaEnabled, 'bg-zinc-700': !adminMfaEnabled }"
                            @click="adminMfaEnabled = !adminMfaEnabled">
                        <span class="sr-only">Use setting</span>
                        <span aria-hidden="true"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="{ 'translate-x-5': adminMfaEnabled, 'translate-x-0': !adminMfaEnabled }"></span>
                    </button>
                </div>

                {{-- 6. Enable Support Desk --}}
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-zinc-200">Enable Support Desk</div>
                            <div class="text-sm text-zinc-500">Allow users to view and create support tickets.</div>
                        </div>
                        <input type="hidden" name="support_enabled" :value="supportEnabled ? 1 : 0">
                        <button type="button"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                                :class="{ 'bg-indigo-600': supportEnabled, 'bg-zinc-700': !supportEnabled }"
                                @click="supportEnabled = !supportEnabled">
                            <span class="sr-only">Use setting</span>
                            <span aria-hidden="true"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                  :class="{ 'translate-x-5': supportEnabled, 'translate-x-0': !supportEnabled }"></span>
                        </button>
                    </div>

                    {{-- Auto-Reply sub-toggle (visible when Support Desk enabled) --}}
                    <div x-show="supportEnabled" x-transition.opacity.duration.300ms class="mt-6 pl-4 border-l-2 border-indigo-600/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-zinc-200">Auto-Reply to New Tickets</div>
                                <div class="text-sm text-zinc-500">Automatically post a system message when a user creates a ticket.</div>
                            </div>
                            <input type="hidden" name="support_auto_reply_enabled" :value="autoReplyEnabled ? 1 : 0">
                            <button type="button"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900"
                                    :class="{ 'bg-indigo-600': autoReplyEnabled, 'bg-zinc-700': !autoReplyEnabled }"
                                    @click="autoReplyEnabled = !autoReplyEnabled">
                                <span class="sr-only">Use setting</span>
                                <span aria-hidden="true"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                      :class="{ 'translate-x-5': autoReplyEnabled, 'translate-x-0': !autoReplyEnabled }"></span>
                            </button>
                        </div>
                    </div>
                </div>



                {{-- 7. Enable Social Login (LAST) --}}
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-zinc-200">Enable Social Login</div>
                            <div class="text-sm text-zinc-500">Allow users to sign in with their social accounts.</div>
                        </div>
                        <input type="hidden" name="social_login_enabled" :value="socialLoginEnabled ? 1 : 0">
                        <button type="button" 
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900" 
                                :class="{ 'bg-indigo-600': socialLoginEnabled, 'bg-zinc-700': !socialLoginEnabled }"
                                @click="socialLoginEnabled = !socialLoginEnabled">
                            <span class="sr-only">Use setting</span>
                            <span aria-hidden="true" 
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                  :class="{ 'translate-x-5': socialLoginEnabled, 'translate-x-0': !socialLoginEnabled }"></span>
                        </button>
                    </div>
                    
                    {{-- Social Login Providers (visible when enabled) --}}
                    <div x-show="socialLoginEnabled" x-transition.opacity.duration.300ms class="mt-6 pl-4 border-l-2 border-indigo-600/50 space-y-6">
                        
                        {{-- Google --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                    <span class="font-medium text-zinc-200">Google</span>
                                </div>
                                <input type="hidden" name="google_login_enabled" :value="googleEnabled ? 1 : 0">
                                <button type="button" 
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900" 
                                        :class="{ 'bg-indigo-600': googleEnabled, 'bg-zinc-700': !googleEnabled }"
                                        @click="googleEnabled = !googleEnabled">
                                    <span class="sr-only">Use setting</span>
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="{ 'translate-x-5': googleEnabled, 'translate-x-0': !googleEnabled }"></span>
                                </button>
                            </div>
                            <div x-show="googleEnabled" x-transition.opacity.duration.300ms class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Client ID</label>
                                    <x-ui.input type="text" name="google_client_id" value="{{ old('google_client_id', $social['google_client_id'] ?? '') }}" placeholder="123456789.apps.googleusercontent.com" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Client Secret</label>
                                    <x-ui.input type="password" name="google_client_secret" value="{{ old('google_client_secret', $social['google_client_secret'] ?? '') }}" placeholder="GOCSPX-..." />
                                </div>
                                <div class="md:col-span-2 rounded-md bg-zinc-950/50 p-3 border border-zinc-800">
                                    <div class="text-xs text-zinc-500 mb-1">Callback URL</div>
                                    <div class="flex items-center gap-2">
                                        <code id="googleCallbackUrl" class="text-xs text-indigo-400 font-mono break-all">{{ url('/auth/google/callback') }}</code>
                                        <button type="button" onclick="copyToClipboard('googleCallbackUrl', this)" class="shrink-0 text-xs text-zinc-400 hover:text-white">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Facebook --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#1877F2]" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    <span class="font-medium text-zinc-200">Facebook</span>
                                </div>
                                <input type="hidden" name="facebook_login_enabled" :value="facebookEnabled ? 1 : 0">
                                <button type="button" 
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900" 
                                        :class="{ 'bg-indigo-600': facebookEnabled, 'bg-zinc-700': !facebookEnabled }"
                                        @click="facebookEnabled = !facebookEnabled">
                                    <span class="sr-only">Use setting</span>
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="{ 'translate-x-5': facebookEnabled, 'translate-x-0': !facebookEnabled }"></span>
                                </button>
                            </div>
                            <div x-show="facebookEnabled" x-transition.opacity.duration.300ms class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">App ID</label>
                                    <x-ui.input type="text" name="facebook_client_id" value="{{ old('facebook_client_id', $social['facebook_client_id'] ?? '') }}" placeholder="123456789012345" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">App Secret</label>
                                    <x-ui.input type="password" name="facebook_client_secret" value="{{ old('facebook_client_secret', $social['facebook_client_secret'] ?? '') }}" placeholder="abc123..." />
                                </div>
                                <div class="md:col-span-2 rounded-md bg-zinc-950/50 p-3 border border-zinc-800">
                                    <div class="text-xs text-zinc-500 mb-1">Callback URL</div>
                                    <div class="flex items-center gap-2">
                                        <code id="facebookCallbackUrl" class="text-xs text-blue-400 font-mono break-all">{{ url('/auth/facebook/callback') }}</code>
                                        <button type="button" onclick="copyToClipboard('facebookCallbackUrl', this)" class="shrink-0 text-xs text-zinc-400 hover:text-white">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Twitter/X --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    <span class="font-medium text-zinc-200">Twitter / X</span>
                                </div>
                                <input type="hidden" name="twitter_login_enabled" :value="twitterEnabled ? 1 : 0">
                                <button type="button" 
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-zinc-900" 
                                        :class="{ 'bg-indigo-600': twitterEnabled, 'bg-zinc-700': !twitterEnabled }"
                                        @click="twitterEnabled = !twitterEnabled">
                                    <span class="sr-only">Use setting</span>
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="{ 'translate-x-5': twitterEnabled, 'translate-x-0': !twitterEnabled }"></span>
                                </button>
                            </div>
                            <div x-show="twitterEnabled" x-transition.opacity.duration.300ms class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-300 mb-1">Client ID</label>
                                        <x-ui.input type="text" name="twitter_client_id" value="{{ old('twitter_client_id', $social['twitter_client_id'] ?? '') }}" placeholder="Your Twitter OAuth 2.0 Client ID" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-300 mb-1">Client Secret</label>
                                        <x-ui.input type="password" name="twitter_client_secret" value="{{ old('twitter_client_secret', $social['twitter_client_secret'] ?? '') }}" placeholder="Your Twitter OAuth 2.0 Client Secret" />
                                    </div>
                                </div>
                                <div class="rounded-md bg-zinc-950/50 p-3 border border-zinc-800">
                                    <div class="text-xs text-zinc-500 mb-1">Callback URL</div>
                                    <div class="flex items-center gap-2">
                                        <code id="twitterCallbackUrl" class="text-xs text-zinc-400 font-mono break-all">{{ url('/auth/twitter/callback') }}</code>
                                        <button type="button" onclick="copyToClipboard('twitterCallbackUrl', this)" class="shrink-0 text-xs text-zinc-400 hover:text-white">Copy</button>
                                    </div>
                                </div>
                                <div class="rounded-md bg-amber-500/10 p-3 border border-amber-500/20">
                                    <div class="text-xs text-amber-400">
                                        <strong>Note:</strong> Twitter OAuth 2.0 requires enabling "Request email from users" in your app settings.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Save Platform Features</button>
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
                    
                    if (submitDelay !== false && this.$refs.form) {
                        setTimeout(() => this.$refs.form.submit(), submitDelay);
                    }
                }
            }
        }
        
        function imagePreview(initial = null) {
            return {
                fileName: '',
                previewUrl: initial || null,
                onChange(e) {
                     const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                     if (!file) return;
                     this.fileName = file.name;
                     if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                     this.previewUrl = URL.createObjectURL(file);
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
        function copyToClipboard(elementId, btn) {
            const el = document.getElementById(elementId);
            if (!el) return;
            const text = el.textContent.trim();
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const old = btn.innerText;
                btn.innerText = 'Copied!';
                setTimeout(() => { btn.innerText = old; }, 1200);
            });
        }
    </script>
@endsection
