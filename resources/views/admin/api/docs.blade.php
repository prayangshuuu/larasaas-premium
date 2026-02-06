{{-- resources/views/admin/api/docs.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header Section --}}
        <div class="relative bg-zinc-900 border border-zinc-800 rounded-2xl p-8 mb-8 overflow-hidden group">
             <div class="absolute inset-0 bg-indigo-500/5 opacity-50 blur-3xl rounded-full pointer-events-none -z-10 group-hover:opacity-75 transition duration-700"></div>
             
             <div class="flex flex-col lg:flex-row gap-8 items-center">
                {{-- Icon --}}
                <div class="shrink-0">
                    <div class="w-20 h-20 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shadow-lg relative">
                        <div class="absolute inset-0 bg-indigo-500/20 blur opacity-0 group-hover:opacity-100 transition duration-500 rounded-2xl"></div>
                        <svg class="w-10 h-10 text-indigo-500 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                </div>

                <div class="flex-1 text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-3 mb-2">
                        <h1 class="text-3xl font-bold tracking-tight text-white">API Documentation</h1>
                        <span class="inline-flex items-center rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs font-medium text-indigo-400 border border-indigo-500/20">v1.0</span>
                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-400 border border-emerald-500/20">Stable</span>
                    </div>
                    <p class="text-zinc-400 max-w-2xl">
                        Secure, JSON-based endpoints for your app & admin workflows. Integrate programmatically with our platform.
                    </p>
                    
                    {{-- Base URL --}}
                    <div class="mt-6 flex flex-col sm:flex-row gap-3 max-w-xl mx-auto lg:mx-0">
                        <div class="relative flex-grow">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500 text-xs font-mono">
                                BASE
                            </div>
                            <input type="text" readonly value="{{ url('/api/v1') }}" 
                                   class="block w-full rounded-md border-0 bg-zinc-950/50 py-2 pl-12 pr-4 text-zinc-300 ring-1 ring-inset ring-zinc-800 placeholder:text-zinc-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono">
                        </div>
                        <button onclick="navigator.clipboard.writeText('{{ url('/api/v1') }}')" class="inline-flex items-center justify-center rounded-md bg-zinc-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            Copy URL
                        </button>
                    </div>
                </div>

                {{-- Quick Links --}}
                 <div class="flex flex-col gap-3">
                    <a href="{{ url('/admin/settings') }}" class="inline-flex items-center justify-center rounded-lg bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-700 border border-zinc-700 transition-all hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        API Settings
                    </a>
                </div>
             </div>
        </div>

        {{-- Authentication Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-xl">
                 <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    Authentication
                </h2>
                <div class="text-sm text-zinc-400 space-y-4">
                     <p>
                        API authentication uses <span class="text-white font-medium">Sanctum Personal Access Tokens</span>.
                        Include the token in the `Authorization` header of every request.
                    </p>
                    <div class="bg-zinc-950 rounded-lg border border-zinc-800 p-4 font-mono text-xs text-indigo-300 overflow-x-auto">
                        Authorization: Bearer &lt;your-token&gt;
                        <br>
                        Accept: application/json
                    </div>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3" /></svg>
                    CLI Helpers
                </h2>
                <div class="space-y-4">
                     <div class="relative group">
                        <div class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="copyCode('cli-create', this)" class="text-xs bg-zinc-700 text-white px-2 py-1 rounded hover:bg-zinc-600">Copy</button>
                        </div>
                        <pre id="cli-create" class="bg-black/50 text-zinc-300 font-mono text-xs rounded-lg p-3 border border-zinc-800 overflow-x-auto">php artisan sanctum:token:create "admin@example.com" --name="dev" --abilities="*"</pre>
                    </div>
                    <div class="relative group">
                          <div class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="copyCode('cli-list', this)" class="text-xs bg-zinc-700 text-white px-2 py-1 rounded hover:bg-zinc-600">Copy</button>
                        </div>
                        <pre id="cli-list" class="bg-black/50 text-zinc-300 font-mono text-xs rounded-lg p-3 border border-zinc-800 overflow-x-auto">php artisan sanctum:token:list --active</pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- Endpoints Section --}}
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-white mb-6">Endpoints</h2>

            {{-- Users --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-800 bg-zinc-800/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                         <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Users <span class="bg-zinc-700 text-zinc-300 text-xs px-2 py-0.5 rounded ml-2">Admin Only</span>
                    </h3>
                </div>
                <div class="divide-y divide-zinc-800">
                    {{-- User List --}}
                    <div x-data="{ open: false }" class="bg-zinc-900">
                        <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">GET</span>
                                <code class="text-sm text-zinc-300">/admin/users</code>
                            </div>
                            <svg class="w-5 h-5 text-zinc-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" class="px-6 pb-4 pt-2 border-t border-zinc-800/50 bg-black/20">
                            <p class="text-sm text-zinc-400 mb-2">Retrieve a paginated list of all registered users.</p>
                            <div class="relative group">
                                <button onclick="copyCode('users-index', this)" class="absolute right-2 top-2 text-xs bg-zinc-800 text-zinc-300 px-2 py-1 rounded border border-zinc-700 opacity-0 group-hover:opacity-100 transition-opacity">Copy</button>
                                <pre id="users-index" class="text-xs font-mono text-zinc-400 bg-zinc-950 p-3 rounded border border-zinc-800 overflow-x-auto">curl -H "Authorization: Bearer &lt;token&gt;" {{ url('/api/v1/admin/users') }}</pre>
                            </div>
                        </div>
                    </div>

                    {{-- User Create --}}
                     <div x-data="{ open: false }" class="bg-zinc-900">
                        <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-green-500/10 text-green-400 border border-green-500/20">POST</span>
                                <code class="text-sm text-zinc-300">/admin/users</code>
                            </div>
                             <svg class="w-5 h-5 text-zinc-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" class="px-6 pb-4 pt-2 border-t border-zinc-800/50 bg-black/20">
                             <p class="text-sm text-zinc-400 mb-2">Create a new user account.</p>
                            <div class="relative group">
                                <button onclick="copyCode('users-store', this)" class="absolute right-2 top-2 text-xs bg-zinc-800 text-zinc-300 px-2 py-1 rounded border border-zinc-700 opacity-0 group-hover:opacity-100 transition-opacity">Copy</button>
                                <pre id="users-store" class="text-xs font-mono text-zinc-400 bg-zinc-950 p-3 rounded border border-zinc-800 overflow-x-auto">curl -X POST \
  -H "Authorization: Bearer &lt;token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"name":"Jane Doe","email":"jane@example.com","password":"secret","is_admin":false}' \
  {{ url('/api/v1/admin/users') }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit Logs --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-800 bg-zinc-800/50 flex items-center justify-between">
                     <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        Audit Logs
                    </h3>
                </div>
                 <div class="divide-y divide-zinc-800">
                    <div x-data="{ open: false }" class="bg-zinc-900">
                        <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">GET</span>
                                <code class="text-sm text-zinc-300">/admin/audit</code>
                            </div>
                             <svg class="w-5 h-5 text-zinc-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" class="px-6 pb-4 pt-2 border-t border-zinc-800/50 bg-black/20">
                            <p class="text-sm text-zinc-400 mb-2">Retrieve system audit logs. Supports pagination.</p>
                            <div class="relative group">
                                <button onclick="copyCode('audit-index', this)" class="absolute right-2 top-2 text-xs bg-zinc-800 text-zinc-300 px-2 py-1 rounded border border-zinc-700 opacity-0 group-hover:opacity-100 transition-opacity">Copy</button>
                                <pre id="audit-index" class="text-xs font-mono text-zinc-400 bg-zinc-950 p-3 rounded border border-zinc-800 overflow-x-auto">curl -H "Authorization: Bearer &lt;token&gt;" "{{ url('/api/v1/admin/audit') }}?page=1&per_page=20"</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             {{-- Settings --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-800 bg-zinc-800/50 flex items-center justify-between">
                     <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        System Settings
                    </h3>
                </div>
                 <div class="divide-y divide-zinc-800">
                    <div x-data="{ open: false }" class="bg-zinc-900">
                        <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">GET</span>
                                <code class="text-sm text-zinc-300">/admin/settings</code>
                            </div>
                             <svg class="w-5 h-5 text-zinc-500 transform transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                         <div x-show="open" class="px-6 pb-4 pt-2 border-t border-zinc-800/50 bg-black/20">
                            <p class="text-sm text-zinc-400 mb-2">List all system settings.</p>
                            <div class="relative group">
                                <button onclick="copyCode('settings-index', this)" class="absolute right-2 top-2 text-xs bg-zinc-800 text-zinc-300 px-2 py-1 rounded border border-zinc-700 opacity-0 group-hover:opacity-100 transition-opacity">Copy</button>
                                <pre id="settings-index" class="text-xs font-mono text-zinc-400 bg-zinc-950 p-3 rounded border border-zinc-800 overflow-x-auto">curl -H "Authorization: Bearer &lt;token&gt;" {{ url('/api/v1/admin/settings') }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyCode(codeId, btn) {
            const code = document.getElementById(codeId);
            if (!code) return;
            const text = code.innerText;
            navigator.clipboard.writeText(text).then(() => {
                const oldText = btn.textContent;
                btn.textContent = 'Copied';
                btn.classList.add('text-green-400');
                setTimeout(() => { 
                    btn.textContent = oldText; 
                    btn.classList.remove('text-green-400');
                }, 1500);
            });
        }
    </script>
@endsection

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
