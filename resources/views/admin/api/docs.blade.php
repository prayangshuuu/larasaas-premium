@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-zinc-950 text-zinc-300 font-sans selection:bg-indigo-500/30">

    {{-- Header --}}
    <header class="border-b border-zinc-900 bg-zinc-950/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 bg-indigo-600 rounded-lg grid place-items-center text-white font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <h1 class="text-lg font-medium text-white tracking-tight">Developer Portal <span class="text-zinc-500 font-normal ml-2">v1.0</span></h1>
            </div>
            <div class="flex items-center gap-6">
                 <a href="{{ url('/admin') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition">
                    Dashboard
                </a>
                <a href="{{ url('/') }}" class="text-sm font-medium text-zinc-400 hover:text-white transition">
                    Home
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            {{-- Sidebar --}}
            <aside class="hidden lg:block lg:col-span-3 xl:col-span-2">
                <nav class="sticky top-24 space-y-10">
                    <div class="space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Getting Started</h3>
                        <ul class="space-y-2 border-l border-zinc-800 ml-1">
                            <li><a href="#intro" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Introduction</a></li>
                            <li><a href="#auth" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Authentication</a></li>
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Public</h3>
                        <ul class="space-y-2 border-l border-zinc-800 ml-1">
                            <li><a href="#ping" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Health Check</a></li>
                            <li><a href="#public-plans" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Plans</a></li>
                            <li><a href="#me" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Profile</a></li>
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Billing</h3>
                        <ul class="space-y-2 border-l border-zinc-800 ml-1">
                            <li><a href="#checkout" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Checkout</a></li>
                            <li><a href="#manage-sub" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Manage Subscription</a></li>
                            <li><a href="#invoices" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Invoices</a></li>
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Admin</h3>
                        <ul class="space-y-2 border-l border-zinc-800 ml-1">
                            <li><a href="#admin-users" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">User Management</a></li>
                            <li><a href="#admin-settings" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">System Settings</a></li>
                            <li><a href="#admin-plans" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Plan Management</a></li>
                            <li><a href="#impersonation" class="block pl-4 -ml-px border-l border-transparent hover:border-indigo-500 text-sm text-zinc-400 hover:text-white transition">Impersonation</a></li>
                        </ul>
                    </div>
                </nav>
            </aside>

            {{-- Main Content --}}
            <main class="lg:col-span-9 xl:col-span-10 space-y-24 pb-24">

                {{-- INTRODUCTION --}}
                <section id="intro" class="scroll-mt-32">
                    <div class="max-w-2xl">
                        <h2 class="text-4xl font-bold text-white mb-6">API Reference</h2>
                        <p class="text-lg text-zinc-400 leading-relaxed mb-8">
                            This documentation provides detailed information about the available endpoints, parameters, and responses for our REST API.
                            Requests are authenticated using Bearer tokens, and responses are returned in JSON format.
                        </p>
                        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center gap-4">
                            <div class="h-10 w-10 bg-indigo-500/10 rounded-lg flex items-center justify-center text-indigo-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white font-medium text-sm">Base URL</h4>
                                <p class="text-zinc-500 font-mono text-sm mt-0.5 selection:bg-indigo-500/30">{{ url('/api/v1') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- AUTHENTICATION --}}
                <section id="auth" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-4">Authentication</h3>
                            <div class="prose prose-invert prose-zinc max-w-none text-zinc-400">
                                <p>Authenticate your requests by including your secret API key in the <code class="text-indigo-300">Authorization</code> header.</p>
                                <p class="mt-4">
                                    You can manage your API keys in the <a href="{{ url('/admin/settings') }}" class="text-indigo-400 hover:text-indigo-300 underline">System Settings</a> dashboard.
                                    Never share your keys in client-side code (e.g., browsers, mobile apps).
                                </p>
                            </div>
                        </div>
                        <div class="w-full">
                            {{-- Code Widget --}}
                            <div x-data="{ activeTab: 'curl', copied: false }" class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800 shadow-xl">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-800 bg-zinc-900/50">
                                    <div class="flex items-center gap-1">
                                        <button @click="activeTab = 'curl'" :class="{ 'bg-zinc-800 text-white': activeTab === 'curl', 'text-zinc-400 hover:text-zinc-200': activeTab !== 'curl' }" class="px-3 py-1 rounded-md text-xs font-medium transition-colors">cURL</button>
                                        <button @click="activeTab = 'python'" :class="{ 'bg-zinc-800 text-white': activeTab === 'python', 'text-zinc-400 hover:text-zinc-200': activeTab !== 'python' }" class="px-3 py-1 rounded-md text-xs font-medium transition-colors">Python</button>
                                        <button @click="activeTab = 'js'" :class="{ 'bg-zinc-800 text-white': activeTab === 'js', 'text-zinc-400 hover:text-zinc-200': activeTab !== 'js' }" class="px-3 py-1 rounded-md text-xs font-medium transition-colors">Node.js</button>
                                        <button @click="activeTab = 'php'" :class="{ 'bg-zinc-800 text-white': activeTab === 'php', 'text-zinc-400 hover:text-zinc-200': activeTab !== 'php' }" class="px-3 py-1 rounded-md text-xs font-medium transition-colors">PHP</button>
                                    </div>
                                    <button @click="
                                        navigator.clipboard.writeText($refs[activeTab].innerText);
                                        copied = true;
                                        setTimeout(() => copied = false, 2000);
                                    " class="text-xs text-zinc-500 hover:text-white transition-colors flex items-center gap-1.5">
                                        <span x-show="!copied">Copy</span>
                                        <span x-show="copied" class="text-emerald-400" style="display: none;">Copied!</span>
                                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                        <svg x-show="copied" class="w-3.5 h-3.5 text-emerald-400" style="display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </button>
                                </div>
                                <div class="p-4 overflow-x-auto bg-[#0d1117]">
                                    <pre x-show="activeTab === 'curl'" x-ref="curl" class="text-sm font-mono text-zinc-300 leading-relaxed">curl "{{ url('/api/v1/user') }}" \
  -H "Authorization: Bearer <span class="text-indigo-400">&lt;token&gt;</span>" \
  -H "Accept: application/json"</pre>
                                    <pre x-show="activeTab === 'python'" x-ref="python" style="display: none;" class="text-sm font-mono text-zinc-300 leading-relaxed">import requests

response = requests.get(
    "{{ url('/api/v1/user') }}",
    headers={
        "Authorization": "Bearer <span class="text-indigo-400">&lt;token&gt;</span>",
        "Accept": "application/json"
    }
)</pre>
                                    <pre x-show="activeTab === 'js'" x-ref="js" style="display: none;" class="text-sm font-mono text-zinc-300 leading-relaxed">const response = await fetch("{{ url('/api/v1/user') }}", {
  method: "GET",
  headers: {
    "Authorization": "Bearer <span class="text-indigo-400">&lt;token&gt;</span>",
    "Accept": "application/json"
  }
});</pre>
                                    <pre x-show="activeTab === 'php'" x-ref="php" style="display: none;" class="text-sm font-mono text-zinc-300 leading-relaxed">$response = $client->request('GET', '{{ url('/api/v1/user') }}', [
    'headers' => [
        'Authorization' => 'Bearer <span class="text-indigo-400">&lt;token&gt;</span>',
        'Accept'        => 'application/json',
    ],
]);</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- PUBLIC ENDPOINTS --}}
                <div id="ping" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">GET</span>
                                <span class="font-mono text-sm text-zinc-400">/ping</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Health Check</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Check if the API is operational.</p>
                        </div>
                        <div x-data="{ activeTab: 'curl', copied: false }" class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="flex items-center justify-between px-3 py-2 border-b border-zinc-800 bg-zinc-900/50">
                                <span class="text-xs font-mono text-zinc-500">Request</span>
                                <button @click="navigator.clipboard.writeText($refs.code.innerText); copied=true; setTimeout(()=>copied=false, 2000)" class="text-xs text-zinc-500 hover:text-white">Copy</button>
                            </div>
                            <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre x-ref="code" class="text-xs font-mono text-zinc-300">curl "{{ url('/api/v1/ping') }}"</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="public-plans" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">GET</span>
                                <span class="font-mono text-sm text-zinc-400">/plans</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">List Plans</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Retrieve active subscription plans.</p>
                        </div>
                        <div x-data="{ copied: false }" class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="flex items-center justify-between px-3 py-2 border-b border-zinc-800 bg-zinc-900/50">
                                <span class="text-xs font-mono text-zinc-500">Request</span>
                                <button @click="navigator.clipboard.writeText($refs.code.innerText); copied=true; setTimeout(()=>copied=false, 2000)" class="text-xs text-zinc-500 hover:text-white">Copy</button>
                            </div>
                            <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre x-ref="code" class="text-xs font-mono text-zinc-300">curl "{{ url('/api/v1/plans') }}"</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="me" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    {{-- GET ME --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16 mb-20">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">GET</span>
                                <span class="font-mono text-sm text-zinc-400">/me</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Get Profile</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Retrieve the authenticated user's profile.</p>
                        </div>
                        <div x-data="{ copied: false }" class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                            <div class="flex items-center justify-between px-3 py-2 border-b border-zinc-800 bg-zinc-900/50">
                                <span class="text-xs font-mono text-zinc-500">Request</span>
                                <button @click="navigator.clipboard.writeText($refs.code.innerText); copied=true; setTimeout(()=>copied=false, 2000)" class="text-xs text-zinc-500 hover:text-white">Copy</button>
                            </div>
                            <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre x-ref="code" class="text-xs font-mono text-zinc-300">curl -H "Authorization: Bearer <token>" "{{ url('/api/v1/me') }}"</pre>
                            </div>
                        </div>
                    </div>

                    {{-- PUT ME --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                             <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">PUT</span>
                                <span class="font-mono text-sm text-zinc-400">/me</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Update Profile</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed mb-6">Update user details.</p>
                            
                            <h4 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-3">Attributes</h4>
                            <div class="border-t border-zinc-800 divide-y divide-zinc-800">
                                <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">name</code>
                                    <div class="text-sm text-zinc-400">Full name <span class="text-zinc-600 text-xs ml-1">(String, Required)</span></div>
                                </div>
                                <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">email</code>
                                    <div class="text-sm text-zinc-400">Email address <span class="text-zinc-600 text-xs ml-1">(String, Required)</span></div>
                                </div>
                            </div>
                        </div>
                        <div x-data="{ copied: false }" class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                            <div class="flex items-center justify-between px-3 py-2 border-b border-zinc-800 bg-zinc-900/50">
                                <span class="text-xs font-mono text-zinc-500">Request</span>
                                <button @click="navigator.clipboard.writeText($refs.code.innerText); copied=true; setTimeout(()=>copied=false, 2000)" class="text-xs text-zinc-500 hover:text-white">Copy</button>
                            </div>
                            <div class="p-4 bg-[#0d1117] overflow-x-auto">
<pre x-ref="code" class="text-xs font-mono text-zinc-300">curl -X PUT "{{ url('/api/v1/me') }}" \
  -H "Authorization: Bearer <span class="text-indigo-400">&lt;token&gt;</span>" \
  -H "Content-Type: application/json" \
  -d '{"name": "John Doe"}'</pre>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- BILLING --}}
                <div id="checkout" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                             <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">POST</span>
                                <span class="font-mono text-sm text-zinc-400">/subscriptions/checkout</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Create Checkout Session</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed mb-6">Start a subscription purchase.</p>

                            <h4 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-3">Attributes</h4>
                            <div class="border-t border-zinc-800 divide-y divide-zinc-800">
                                <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">plan_id</code>
                                    <div class="text-sm text-zinc-400">Plan ID <span class="text-zinc-600 text-xs ml-1">(Integer, Required)</span></div>
                                </div>
                            </div>
                        </div>
                        <div x-data="{ copied: false }" class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="flex items-center justify-between px-3 py-2 border-b border-zinc-800 bg-zinc-900/50">
                                <span class="text-xs font-mono text-zinc-500">Request</span>
                                <button @click="navigator.clipboard.writeText($refs.code.innerText); copied=true; setTimeout(()=>copied=false, 2000)" class="text-xs text-zinc-500 hover:text-white">Copy</button>
                            </div>
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
<pre x-ref="code" class="text-xs font-mono text-zinc-300">curl -X POST "{{ url('/api/v1/subscriptions/checkout') }}" \
  -H "Authorization: Bearer <span class="text-indigo-400">&lt;token&gt;</span>" \
  -H "Content-Type: application/json" \
  -d '{"plan_id": 1}'</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="manage-sub" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    {{-- CANCEL --}}
                     <div class="grid grid-cols-1 xl:grid-cols-2 gap-16 mb-20">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">POST</span>
                                <span class="font-mono text-sm text-zinc-400">/subscriptions/cancel</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Cancel Subscription</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Cancel active subscription.</p>
                        </div>
                        <div x-data="{ copied: false }" class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre x-ref="code" class="text-xs font-mono text-zinc-300">curl -X POST -H "Authorization: Bearer <token>" "{{ url('/api/v1/subscriptions/cancel') }}"</pre>
                            </div>
                        </div>
                    </div>
                     {{-- RESUME --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">POST</span>
                                <span class="font-mono text-sm text-zinc-400">/subscriptions/resume</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Resume Subscription</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Resume a subscription that is on grace period.</p>
                        </div>
                        <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre class="text-xs font-mono text-zinc-300">curl -X POST -H "Authorization: Bearer <token>" "{{ url('/api/v1/subscriptions/resume') }}"</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="invoices" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">GET</span>
                                <span class="font-mono text-sm text-zinc-400">/invoices</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">List Invoices</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Retrieve all user invoices.</p>
                        </div>
                         <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre class="text-xs font-mono text-zinc-300">curl -H "Authorization: Bearer <token>" "{{ url('/api/v1/invoices') }}"</pre>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- ADMIN --}}
                <div id="admin-users" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="px-2 py-1 rounded bg-indigo-500/20 text-indigo-400 text-[10px] font-bold uppercase tracking-wider">Admin</div>
                    </div>
                    
                    {{-- List --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16 mb-20">
                         <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">GET</span>
                                <span class="font-mono text-sm text-zinc-400">/admin/users</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">List Users</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed mb-6">Paginated user list.</p>
                             <div class="border-t border-zinc-800 divide-y divide-zinc-800">
                                <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">page</code>
                                    <div class="text-sm text-zinc-400">Page number</div>
                                </div>
                                <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">per_page</code>
                                    <div class="text-sm text-zinc-400">Items per page</div>
                                </div>
                            </div>
                        </div>
                         <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre class="text-xs font-mono text-zinc-300">curl -H "Authorization: Bearer <admin_token>" "{{ url('/api/v1/admin/users') }}"</pre>
                            </div>
                        </div>
                    </div>

                    {{-- Create --}}
                     <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                         <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">POST</span>
                                <span class="font-mono text-sm text-zinc-400">/admin/users</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Create User</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed mb-6">Manually create a user.</p>
                             <div class="border-t border-zinc-800 divide-y divide-zinc-800">
                                <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">name</code>
                                    <div class="text-sm text-zinc-400">Full Name</div>
                                </div>
                                 <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">email</code>
                                    <div class="text-sm text-zinc-400">Email Address</div>
                                </div>
                                 <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">password</code>
                                    <div class="text-sm text-zinc-400">Password</div>
                                </div>
                            </div>
                        </div>
                         <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
<pre class="text-xs font-mono text-zinc-300">curl -X POST "{{ url('/api/v1/admin/users') }}" \
  -H "Authorization: Bearer <admin_token>" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com", ...}'</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="admin-settings" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="px-2 py-1 rounded bg-indigo-500/20 text-indigo-400 text-[10px] font-bold uppercase tracking-wider">Admin</div>
                    </div>
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16 mb-20">
                         <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">GET</span>
                                <span class="font-mono text-sm text-zinc-400">/admin/settings</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">List Settings</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Get all system settings.</p>
                        </div>
                        <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre class="text-xs font-mono text-zinc-300">curl -H "Authorization: Bearer <admin_token>" "{{ url('/api/v1/admin/settings') }}"</pre>
                            </div>
                        </div>
                    </div>

                     <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                         <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">POST</span>
                                <span class="font-mono text-sm text-zinc-400">/admin/settings/logo</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Upload Logo</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Multipart upload for site logo.</p>
                        </div>
                        <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre class="text-xs font-mono text-zinc-300">curl -X POST -H "Authorization: Bearer <token>" -F "logo=@file" ...</pre>
                            </div>
                        </div>
                    </div>
                </div>

                 <div id="admin-plans" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                     <div class="flex items-center gap-3 mb-6">
                        <div class="px-2 py-1 rounded bg-indigo-500/20 text-indigo-400 text-[10px] font-bold uppercase tracking-wider">Admin</div>
                    </div>
                     <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                         <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">POST</span>
                                <span class="font-mono text-sm text-zinc-400">/admin/plans</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Create Plan</h3>
                             <p class="text-zinc-400 text-sm leading-relaxed mb-6">Define a new subscription plan.</p>
                             <div class="border-t border-zinc-800 divide-y divide-zinc-800">
                                <div class="py-3 flex gap-4">
                                    <code class="text-xs text-indigo-400 font-mono w-24 shrink-0">price</code>
                                    <div class="text-sm text-zinc-400">Amount in cents</div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre class="text-xs font-mono text-zinc-300">curl -X POST -H "Authorization: Bearer ..." -d '{"name":"Pro", "price":9900}' ...</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="impersonation" class="scroll-mt-32 pt-12 border-t border-zinc-900">
                     <div class="flex items-center gap-3 mb-6">
                        <div class="px-2 py-1 rounded bg-indigo-500/20 text-indigo-400 text-[10px] font-bold uppercase tracking-wider">Admin / Sensitive</div>
                    </div>
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-16">
                        <div>
                             <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">POST</span>
                                <span class="font-mono text-sm text-zinc-400">/admin/impersonate/start/{user}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Start Impersonation</h3>
                            <p class="text-zinc-400 text-sm leading-relaxed">Log in as another user for troubleshooting.</p>
                        </div>
                        <div class="rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
                             <div class="p-4 bg-[#0d1117] overflow-x-auto">
                                <pre class="text-xs font-mono text-zinc-300">curl -X POST -H "Authorization: Bearer ..." "{{ url('/api/v1/admin/impersonate/start/1') }}"</pre>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>
@endsection
