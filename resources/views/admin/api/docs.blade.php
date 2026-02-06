{{-- resources/views/admin/api/docs.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header Section --}}
        <div class="relative bg-zinc-900 border border-zinc-800 rounded-2xl p-8 mb-8 overflow-hidden group">
             <div class="absolute inset-0 bg-indigo-500/5 opacity-50 blur-3xl rounded-full pointer-events-none -z-10 group-hover:opacity-75 transition duration-700"></div>
             
             <div class="flex flex-col lg:flex-row gap-8 items-center">
                {{-- API tile (centered) --}}
                <div class="shrink-0">
                    <div class="w-20 h-20 rounded-xl bg-primary text-primary-content grid place-items-center">
                        <span class="text-3xl font-bold tracking-tight">API</span>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-semibold">API v1 Documentation</h1>
                        <div class="badge badge-primary">Sanctum</div>
                        <div class="badge badge-ghost">JSON</div>
                        <div class="badge badge-success">Stable</div>
                    </div>
                    <p class="mt-2 text-base-content/70">
                        Secure, JSON-based endpoints for your app &amp; admin workflows.
                    </p>

                    {{-- Base URL + quick copy --}}
                    <div class="mt-4 join w-full lg:max-w-xl">
                        <span class="join-item btn btn-ghost btn-sm">BASE_URL</span>
                        <input id="baseUrl" class="join-item input input-bordered input-sm w-full"
                               value="{{ url('/api/v1') }}" readonly>
                        <button class="join-item btn btn-sm" onclick="copyText('baseUrl', this)">Copy</button>
                    </div>

                    {{-- Auth header (high contrast + copy) --}}
                    <div class="mt-3 space-y-2">
                        <div class="join w-full lg:max-w-xl">
                            <span class="join-item btn btn-ghost btn-xs">Header</span>
                            <input id="authHeader" class="join-item input input-bordered input-xs w-full font-mono"
                                   value="Authorization: Bearer <token>" readonly>
                            <button class="join-item btn btn-xs" onclick="copyText('authHeader', this)">Copy</button>
                        </div>
                        <div class="text-xs text-base-content/70">
                            Also set <kbd class="kbd kbd-xs">Accept</kbd>: <kbd class="kbd kbd-xs">application/json</kbd>
                        </div>
                    </div>
                </div>

                {{-- Quick tools --}}
                <div class="w-full lg:w-auto grid grid-cols-2 lg:grid-cols-1 gap-2">
                    <a href="{{ url('/admin/settings') }}" class="btn btn-secondary btn-sm">System Settings</a>
                    <a href="{{ url('/admin') }}" class="btn btn-outline btn-sm">Admin Dashboard</a>
                </div>
            </div>
        </div>

        {{-- Toolbar: search + legend --}}
        <div class="mt-6 flex flex-col lg:flex-row items-start lg:items-center gap-3">
            <label class="input input-bordered flex items-center gap-2 w-full lg:max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35m1.1-5.4a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"/>
                </svg>
                <input id="endpointSearch" type="text" class="grow" placeholder="Filter endpoints (e.g. users, settings, audit, impersonate, me, ping)" oninput="filterEndpoints()"/>
            </label>

            <div class="flex items-center gap-2 text-xs text-base-content/60">
                <span class="badge badge-outline">GET</span>
                <span class="badge badge-info">POST</span>
                <span class="badge badge-warning">PUT</span>
                <span class="badge badge-error">DELETE</span>
                <span class="badge badge-ghost">Admin</span>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="mt-6">
            <div role="tablist" class="tabs tabs-lifted">
                <input type="radio" name="apiTabs" role="tab" class="tab" aria-label="Overview" checked />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-2xl p-6">

                    {{-- Overview & Auth --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="card bg-base-100 border border-base-300 rounded-2xl">
                            <div class="card-body">
                                <h2 class="card-title text-lg">Quick Start</h2>
                                <p class="text-sm text-base-content/70">
                                    Simple health check and profile endpoints to validate your token.
                                </p>

                                {{-- /ping --}}
                                <div class="collapse collapse-arrow border border-base-300 rounded-xl my-2" data-endpoint="ping get">
                                    <input type="checkbox" />
                                    <div class="collapse-title text-md font-medium">
                                        <span class="badge badge-outline mr-2">GET</span> /ping
                                    </div>
                                    <div class="collapse-content">
                                        <div class="relative group">
                                            <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                                    onclick="copyCode('curl-ping', this)">Copy</button>
                                            <pre id="curl-ping"
                                                 class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all"
                                                 aria-label="curl ping">
curl {{ url('/api/v1/ping') }}</pre>
                                        </div>
                                    </div>
                                </div>

                                {{-- /me --}}
                                <div class="collapse collapse-arrow border border-base-300 rounded-xl my-2" data-endpoint="me get put profile">
                                    <input type="checkbox" />
                                    <div class="collapse-title text-md font-medium">
                                        <span class="badge badge-outline mr-2">GET</span>
                                        <span class="badge badge-warning mr-2">PUT</span>
                                        /me
                                    </div>
                                    <div class="collapse-content space-y-3">
                                        <div class="relative group">
                                            <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                                    onclick="copyCode('curl-me-get', this)">Copy</button>
                                            <pre id="curl-me-get"
                                                 class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;token&gt;" {{ url('/api/v1/me') }}</pre>
                                        </div>

                                        <div class="relative group">
                                            <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                                    onclick="copyCode('curl-me-put', this)">Copy</button>
                                            <pre id="curl-me-put"
                                                 class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X PUT \
  -H "Authorization: Bearer &lt;token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"name":"New Name"}' {{ url('/api/v1/me') }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Auth notes --}}
                        <div class="card bg-base-100 border border-base-300 rounded-2xl">
                            <div class="card-body">
                                <h2 class="card-title text-lg">Auth</h2>
                                <div class="space-y-3 text-sm">
                                    <p>
                                        API authentication uses <span class="badge badge-primary">Sanctum Personal Access Tokens</span>.
                                        Create tokens from <a class="link" href="{{ url('/admin/settings') }}">System Settings → API Keys</a>
                                        or via CLI commands you installed.
                                    </p>
                                    <div class="alert rounded-xl">
                                        <span>Send the header above with every request.</span>
                                    </div>
                                    <div class="collapse collapse-arrow border border-base-300 rounded-xl">
                                        <input type="checkbox" />
                                        <div class="collapse-title text-md font-medium">CLI helpers (optional)</div>
                                        <div class="collapse-content space-y-2">
                                            <div class="relative group">
                                                <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                                        onclick="copyCode('cli-create', this)">Copy</button>
                                                <pre id="cli-create" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
php artisan sanctum:token:create "admin@example.com" --name="dev" --abilities="*"</pre>
                                            </div>
                                            <div class="relative group">
                                                <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                                        onclick="copyCode('cli-list', this)">Copy</button>
                                                <pre id="cli-list" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
php artisan sanctum:token:list --active</pre>
                                            </div>
                                            <div class="relative group">
                                                <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                                        onclick="copyCode('cli-revoke', this)">Copy</button>
                                                <pre id="cli-revoke" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
php artisan sanctum:token:revoke user:admin@example.com --all --force</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- USERS --}}
                <input type="radio" name="apiTabs" role="tab" class="tab" aria-label="Users" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-2xl p-6">
                    <div class="alert rounded-xl mb-4">
                        <div><span class="badge badge-ghost mr-2">Admin</span> Users CRUD requires an admin token.</div>
                    </div>

                    <div class="space-y-3">
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="users get list index">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /admin/users
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('users-index', this)">Copy</button>
                                    <pre id="users-index" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;admin_token&gt;" {{ url('/api/v1/admin/users') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="users post create store">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-info mr-2">POST</span> /admin/users
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('users-store', this)">Copy</button>
                                    <pre id="users-store" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST \
  -H "Authorization: Bearer &lt;admin_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"name":"Jane","email":"jane@example.com","password":"secret","is_admin":false}' \
  {{ url('/api/v1/admin/users') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="users get show">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /admin/users/{id}
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('users-show', this)">Copy</button>
                                    <pre id="users-show" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;admin_token&gt;" {{ url('/api/v1/admin/users/1') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="users put update">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-warning mr-2">PUT</span> /admin/users/{id}
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('users-update', this)">Copy</button>
                                    <pre id="users-update" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X PUT \
  -H "Authorization: Bearer &lt;admin_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"name":"Updated Name"}' \
  {{ url('/api/v1/admin/users/1') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="users delete destroy">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-error mr-2">DELETE</span> /admin/users/{id}
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('users-destroy', this)">Copy</button>
                                    <pre id="users-destroy" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X DELETE -H "Authorization: Bearer &lt;admin_token&gt;" {{ url('/api/v1/admin/users/1') }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SETTINGS --}}
                <input type="radio" name="apiTabs" role="tab" class="tab" aria-label="Settings" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-2xl p-6">
                    <div class="alert rounded-xl mb-4">
                        <div><span class="badge badge-ghost mr-2">Admin</span> Read/Write app settings. Logo uploads require multipart form-data.</div>
                    </div>

                    <div class="space-y-3">
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="settings get list">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /admin/settings
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('settings-index', this)">Copy</button>
                                    <pre id="settings-index" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;admin_token&gt;" {{ url('/api/v1/admin/settings') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="settings get key">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /admin/settings/{key}
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('settings-key', this)">Copy</button>
                                    <pre id="settings-key" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;admin_token&gt;" {{ url('/api/v1/admin/settings/app.name') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="settings put key update">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-warning mr-2">PUT</span> /admin/settings/{key}
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('settings-put', this)">Copy</button>
                                    <pre id="settings-put" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X PUT \
  -H "Authorization: Bearer &lt;admin_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"value":"New App Name"}' \
  {{ url('/api/v1/admin/settings/app.name') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="settings post logo upload multipart">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-info mr-2">POST</span> /admin/settings/logo
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('settings-logo', this)">Copy</button>
                                    <pre id="settings-logo" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST \
  -H "Authorization: Bearer &lt;admin_token&gt;" \
  -F "app_logo_light=@/path/light.png" \
  -F "app_logo_dark=@/path/dark.png" \
  {{ url('/api/v1/admin/settings/logo') }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- AUDIT --}}
                <input type="radio" name="apiTabs" role="tab" class="tab" aria-label="Audit" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-2xl p-6">
                    <div class="alert rounded-xl mb-4">
                        <div><span class="badge badge-ghost mr-2">Admin</span> Read audit entries with pagination &amp; filtering.</div>
                    </div>

                    <div class="space-y-3">
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="audit get list">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /admin/audit
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('audit-index', this)">Copy</button>
                                    <pre id="audit-index" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;admin_token&gt;" "{{ url('/api/v1/admin/audit') }}?page=1&amp;per_page=20"</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="audit get show">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /admin/audit/{id}
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('audit-show', this)">Copy</button>
                                    <pre id="audit-show" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;admin_token&gt;" {{ url('/api/v1/admin/audit/1') }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- IMPERSONATION --}}
                <input type="radio" name="apiTabs" role="tab" class="tab" aria-label="Impersonation" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-2xl p-6">
                    <div class="alert rounded-xl mb-4">
                        <div><span class="badge badge-ghost mr-2">Admin</span> Feature-flagged. Requires Admin MFA to start. Stop is always available to exit.</div>
                    </div>

                    <div class="space-y-3">
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="impersonate post start">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-info mr-2">POST</span> /admin/impersonate/start/{user}
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('imp-start', this)">Copy</button>
                                    <pre id="imp-start" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST \
  -H "Authorization: Bearer &lt;admin_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"code":"123456","mode":"readonly"}' \
  {{ url('/api/v1/admin/impersonate/start/42') }}</pre>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="impersonate post stop">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-info mr-2">POST</span> /admin/impersonate/stop
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('imp-stop', this)">Copy</button>
                                    <pre id="imp-stop" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST -H "Authorization: Bearer &lt;admin_token&gt;" {{ url('/api/v1/admin/impersonate/stop') }}</pre>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-base-content/60">
                            While impersonating, all <span class="badge badge-ghost mx-1">admin.*</span> endpoints are blocked.
                            Non-admin writes are blocked unless <kbd class="kbd kbd-xs">mode=full</kbd>.
                        </p>
                    </div>
                </div>

                {{-- SUBSCRIPTIONS & BILLING --}}
                <input type="radio" name="apiTabs" role="tab" class="tab" aria-label="Billing" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-2xl p-6">
                     <div class="alert rounded-xl mb-4">
                        <div><span class="badge badge-ghost mr-2">Public/Auth</span> Manage plans, subscriptions, and invoices.</div>
                    </div>

                    <div class="space-y-3">
                         {{-- Plans (Public) --}}
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="plans get list public">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /plans (Public)
                            </div>
                            <div class="collapse-content">
                                <p class="text-sm text-base-content/70 mb-2">List all active subscription plans.</p>
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('plans-index', this)">Copy</button>
                                    <pre id="plans-index" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl {{ url('/api/v1/plans') }}</pre>
                                </div>
                            </div>
                        </div>

                        {{-- Checkout --}}
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="subscriptions checkout post">
                            <input type="checkbox" />
                             <div class="collapse-title text-md font-medium">
                                <span class="badge badge-info mr-2">POST</span> /subscriptions/checkout
                            </div>
                            <div class="collapse-content">
                                 <p class="text-sm text-base-content/70 mb-2">Initiate checkout. Returns a Stripe Checkout URL.</p>
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('sub-checkout', this)">Copy</button>
                                    <pre id="sub-checkout" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST \
  -H "Authorization: Bearer &lt;token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"plan_id": 1}' \
  {{ url('/api/v1/subscriptions/checkout') }}</pre>
                                </div>
                            </div>
                        </div>

                        {{-- Cancel --}}
                         <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="subscriptions cancel post">
                            <input type="checkbox" />
                             <div class="collapse-title text-md font-medium">
                                <span class="badge badge-info mr-2">POST</span> /subscriptions/cancel
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('sub-cancel', this)">Copy</button>
                                    <pre id="sub-cancel" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST -H "Authorization: Bearer &lt;token&gt;" {{ url('/api/v1/subscriptions/cancel') }}</pre>
                                </div>
                            </div>
                        </div>

                         {{-- Invoices --}}
                         <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="invoices get list">
                            <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">GET</span> /invoices
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('invoices-index', this)">Copy</button>
                                    <pre id="invoices-index" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -H "Authorization: Bearer &lt;token&gt;" {{ url('/api/v1/invoices') }}</pre>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ADMIN SUBSCRIPTION MANAGEMENT --}}
                <input type="radio" name="apiTabs" role="tab" class="tab" aria-label="Admin Billing" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-2xl p-6">
                     <div class="alert rounded-xl mb-4">
                        <div><span class="badge badge-ghost mr-2">Admin</span> Manage plans and subscription settings.</div>
                    </div>
                     <div class="space-y-3">
                         {{-- Admin Plans --}}
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="admin plans crud">
                             <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-outline mr-2">CRUD</span> /admin/plans
                            </div>
                            <div class="collapse-content">
                                <p class="text-sm text-base-content/70 mb-2">Standard Resource: GET, POST, PUT, DELETE.</p>
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('admin-plans', this)">Copy</button>
                                    <pre id="admin-plans" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST \
  -H "Authorization: Bearer &lt;admin_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"name": "Pro", "price": 99, "currency": "USD", "stripe_id": "price_123"}' \
  {{ url('/api/v1/admin/plans') }}</pre>
                                </div>
                            </div>
                        </div>
                        {{-- Subscription Settings --}}
                        <div class="collapse collapse-arrow border border-base-300 rounded-xl" data-endpoint="admin settings subscription">
                             <input type="checkbox" />
                            <div class="collapse-title text-md font-medium">
                                <span class="badge badge-info mr-2">POST</span> /admin/settings/subscription
                            </div>
                            <div class="collapse-content">
                                <div class="relative group">
                                    <button class="btn btn-xs btn-ghost absolute right-2 top-2 opacity-0 group-hover:opacity-100"
                                            onclick="copyCode('admin-sub-settings', this)">Copy</button>
                                    <pre id="admin-sub-settings" class="bg-base-200 text-base-content/90 font-mono text-sm rounded-xl p-4 overflow-x-auto select-all">
curl -X POST \
  -H "Authorization: Bearer &lt;admin_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"subscription_module_enabled": true}' \
  {{ url('/api/v1/admin/settings/subscription') }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer card --}}
        <div class="mt-8 card bg-base-100 border border-base-300 rounded-2xl">
            <div class="card-body">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3">
                    <div class="text-sm text-base-content/70">
                        Need a Postman collection? Use the CLI helper or export from your app’s API docs endpoint if enabled.
                    </div>
                    <div class="join">
                        <a href="{{ url('/admin/settings') }}" class="join-item btn btn-sm btn-ghost">API Keys</a>
                        <a href="{{ url('/admin') }}" class="join-item btn btn-sm btn-outline">Back to Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Minimal helpers (no external libs) --}}
    <script>
        function copyText(inputId, btn) {
            const el = document.getElementById(inputId);
            if (!el) return;
            navigator.clipboard.writeText(el.value || el.textContent || '').then(() => {
                btn.classList.add('btn-success');
                const old = btn.textContent;
                btn.textContent = 'Copied';
                setTimeout(() => { btn.classList.remove('btn-success'); btn.textContent = old; }, 900);
            });
        }
        function copyCode(codeId, btn) {
            const code = document.getElementById(codeId);
            if (!code) return;
            const text = code.innerText;
            navigator.clipboard.writeText(text).then(() => {
                btn.classList.add('btn-success');
                const old = btn.textContent;
                btn.textContent = 'Copied';
                setTimeout(() => { btn.classList.remove('btn-success'); btn.textContent = old; }, 900);
            });
        }
        function filterEndpoints() {
            const q = (document.getElementById('endpointSearch').value || '').toLowerCase().trim();
            const nodes = document.querySelectorAll('[data-endpoint]');
            nodes.forEach(n => {
                const tags = (n.getAttribute('data-endpoint') || '').toLowerCase();
                const text = n.textContent.toLowerCase();
                const hit = !q || tags.includes(q) || text.includes(q);
                n.style.display = hit ? '' : 'none';
            });
        }
    </script>
@endsection
