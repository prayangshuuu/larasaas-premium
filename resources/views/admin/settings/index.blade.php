{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-base-content">System Settings</h1>
                <p class="text-sm text-base-content/70">Manage app identity, SMTP, and features.</p>
            </div>
        </div>

        {{-- Flash messages (status from controller) --}}
        @php $status = session('status'); @endphp
        @if ($status === 'settings-app-updated')
            <div class="alert alert-success rounded-2xl"><span>Application settings updated.</span></div>
        @elseif ($status === 'settings-app-logo-updated')
            <div class="alert alert-success rounded-2xl"><span>Application logo updated.</span></div>
        @elseif ($status === 'settings-smtp-updated')
            <div class="alert alert-success rounded-2xl"><span>SMTP settings updated.</span></div>
        @elseif ($status === 'settings-features-updated')
            <div class="alert alert-success rounded-2xl"><span>Feature flags updated.</span></div>
        @endif

        {{-- =========================
             Application (name + logo)
           ========================= --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <h2 class="card-title text-base-content text-lg">Application</h2>
                        <p class="mt-1 text-sm text-base-content/70">Update name and logo used across the product.</p>
                    </div>

                    {{-- Logo preview (autosave on change) --}}
                    <form method="POST" action="{{ route('admin.settings.app.logo') }}"
                          enctype="multipart/form-data" class="sm:shrink-0">
                        @csrf
                        <div class="relative group">
                            <input id="app_logo" name="app_logo" type="file" class="sr-only"
                                   accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   onchange="this.form.submit()">
                            <div class="avatar">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 mask mask-circle ring ring-primary ring-offset-base-100 ring-offset-2 overflow-hidden">
                                    @if(!empty($app_logo))
                                        {{-- $app_logo is a "storage/..." web path --}}
                                        <img class="object-cover w-full h-full" src="{{ asset($app_logo) }}" alt="App logo">
                                    @else
                                        {{-- Fallback to your app logo component if none set --}}
                                        <div class="grid place-items-center w-full h-full bg-base-200">
                                            <x-application-logo class="w-10 h-10 text-primary" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <label for="app_logo"
                                   class="absolute inset-0 grid place-items-center rounded-full
                                      bg-base-100/70 opacity-0 group-hover:opacity-100
                                      transition cursor-pointer text-xs sm:text-sm font-medium">
                                Change
                            </label>
                        </div>
                        @error('app_logo') <p class="text-error text-sm mt-2">{{ $message }}</p> @enderror
                    </form>
                </div>

                <div class="divider my-6"></div>

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
             SMTP Settings
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
                                   value="{{ old('host', $smtp['host'] ?? '') }}" />
                            @error('host') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Port</span></label>
                            <input type="number" name="port" class="input input-bordered h-12 w-full"
                                   value="{{ old('port', $smtp['port'] ?? '') }}" />
                            @error('port') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Username</span></label>
                            <input type="text" name="username" class="input input-bordered h-12 w-full"
                                   value="{{ old('username', $smtp['username'] ?? '') }}" />
                            @error('username') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Password</span></label>
                            <input type="password" name="password" class="input input-bordered h-12 w-full"
                                   placeholder="••••••••" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">
                                    Leave blank to keep existing password.
                                </span>
                            </label>
                            @error('password') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Encryption</span></label>
                            @php $enc = old('encryption', $smtp['encryption'] ?? ''); @endphp
                            <select name="encryption" class="select select-bordered h-12 w-full">
                                <option value=""   {{ $enc === '' ? 'selected' : '' }}>None</option>
                                <option value="tls"{{ $enc === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl"{{ $enc === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                            @error('encryption') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">From address</span></label>
                            <input type="email" name="from_addr" class="input input-bordered h-12 w-full"
                                   value="{{ old('from_addr', $smtp['from_addr'] ?? '') }}" />
                            @error('from_addr') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">From name</span></label>
                            <input type="text" name="from_name" class="input input-bordered h-12 w-full"
                                   value="{{ old('from_name', $smtp['from_name'] ?? '') }}" />
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

                    {{-- Impersonation --}}
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="font-medium">Impersonation</div>
                            <div class="text-sm text-base-content/70">
                                Allow admins to impersonate users (with MFA, audit log, and consent codes if required).
                            </div>
                        </div>
                        <div class="form-control">
                            <input type="hidden" name="impersonation" value="0">
                            <input type="checkbox" name="impersonation" value="1"
                                   class="toggle toggle-primary"
                                {{ old('impersonation', (int)($features['impersonation'] ?? 0)) ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- Editable usernames --}}
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="font-medium">Editable usernames</div>
                            <div class="text-sm text-base-content/70">
                                Allow admins to change usernames.
                            </div>
                        </div>
                        <div class="form-control">
                            <input type="hidden" name="allow_username_change" value="0">
                            <input type="checkbox" name="allow_username_change" value="1"
                                   class="toggle toggle-primary"
                                {{ old('allow_username_change', (int)($features['allow_username_change'] ?? 1)) ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- MFA requirement for impersonation --}}
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="font-medium">Require admin MFA for impersonation</div>
                            <div class="text-sm text-base-content/70">
                                Extra safety before starting an impersonation session.
                            </div>
                        </div>
                        <div class="form-control">
                            <input type="hidden" name="require_admin_mfa_for_impersonation" value="0">
                            <input type="checkbox" name="require_admin_mfa_for_impersonation" value="1"
                                   class="toggle toggle-primary"
                                {{ old('require_admin_mfa_for_impersonation', (int)($features['require_admin_mfa_for_impersonation'] ?? 1)) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="card-actions justify-end pt-2">
                        <button type="submit" class="btn btn-primary h-12 min-w-40">Save</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
