@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-zinc-950 text-zinc-300 font-sans selection:bg-indigo-500/30">
    <div class="flex max-w-8xl mx-auto">
        
        <!-- Sidebar Navigation -->
        <nav class="hidden lg:block w-64 h-screen sticky top-0 overflow-y-auto border-r border-zinc-800 bg-zinc-950 px-6 py-8 scrolled-sidebar">
            <div class="mb-8">
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    API Reference
                </h1>
                <p class="mt-2 text-xs text-zinc-500 uppercase tracking-wider font-semibold">Version 1.0</p>
            </div>

            <div class="space-y-8" x-data="{ activeSection: window.location.hash || '#introduction' }">
                <!-- Group: Introduction -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Getting Started</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="#introduction" 
                               @click="activeSection = '#introduction'"
                               :class="activeSection === '#introduction' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'"
                               class="block py-1">
                                Introduction
                            </a>
                        </li>
                        <li>
                            <a href="#auth" 
                               @click="activeSection = '#auth'"
                               :class="activeSection === '#auth' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'"
                               class="block py-1">
                                Authentication
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group: Public -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Public</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#get-ping" @click="activeSection = '#get-ping'" :class="activeSection === '#get-ping' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Health Check</a></li>
                        <li><a href="#get-plans" @click="activeSection = '#get-plans'" :class="activeSection === '#get-plans' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">List Plans</a></li>
                    </ul>
                </div>

                <!-- Group: User -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">User Profile</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#get-me" @click="activeSection = '#get-me'" :class="activeSection === '#get-me' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Get Profile</a></li>
                        <li><a href="#put-me" @click="activeSection = '#put-me'" :class="activeSection === '#put-me' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Update Profile</a></li>
                    </ul>
                </div>

                <!-- Group: Billing -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Billing</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#post-checkout" @click="activeSection = '#post-checkout'" :class="activeSection === '#post-checkout' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Create Checkout</a></li>
                        <li><a href="#post-cancel" @click="activeSection = '#post-cancel'" :class="activeSection === '#post-cancel' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Cancel Subscription</a></li>
                        <li><a href="#post-resume" @click="activeSection = '#post-resume'" :class="activeSection === '#post-resume' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Resume Subscription</a></li>
                        <li><a href="#get-invoices" @click="activeSection = '#get-invoices'" :class="activeSection === '#get-invoices' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">List Invoices</a></li>
                    </ul>
                </div>

                <!-- Group: Admin Users -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Admin: Users</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#admin-get-users" @click="activeSection = '#admin-get-users'" :class="activeSection === '#admin-get-users' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">List Users</a></li>
                        <li><a href="#admin-post-users" @click="activeSection = '#admin-post-users'" :class="activeSection === '#admin-post-users' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Create User</a></li>
                        <li><a href="#admin-get-user" @click="activeSection = '#admin-get-user'" :class="activeSection === '#admin-get-user' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Get User</a></li>
                    </ul>
                </div>
                 <!-- Group: Admin Plans -->
                 <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Admin: Plans</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#admin-get-plans" @click="activeSection = '#admin-get-plans'" :class="activeSection === '#admin-get-plans' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">List Plans</a></li>
                        <li><a href="#admin-post-plans" @click="activeSection = '#admin-post-plans'" :class="activeSection === '#admin-post-plans' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Create Plan</a></li>
                    </ul>
                </div>

                 <!-- Group: Admin System -->
                 <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Admin: System</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#admin-get-settings" @click="activeSection = '#admin-get-settings'" :class="activeSection === '#admin-get-settings' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">List Settings</a></li>
                        <li><a href="#admin-post-logo" @click="activeSection = '#admin-post-logo'" :class="activeSection === '#admin-post-logo' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Upload Logo</a></li>
                        <li><a href="#admin-impersonate" @click="activeSection = '#admin-impersonate'" :class="activeSection === '#admin-impersonate' ? 'text-indigo-400 border-l-2 border-indigo-400 pl-3 -ml-3' : 'text-zinc-400 hover:text-white hover:pl-1 transition-all'" class="block py-1">Impersonate</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 w-0">
            <div class="max-w-5xl mx-auto px-8 py-12 space-y-24">

                <!-- INTRO -->
                <section id="introduction" class="scroll-mt-24">
                    <h1 class="text-4xl font-extrabold text-white tracking-tight mb-6">API Documentation</h1>
                    <p class="text-lg text-zinc-400 leading-relaxed max-w-3xl">
                        Welcome to the official API reference. Our API is organized around <span class="text-white font-medium">REST</span>. Our API has predictable resource-oriented URLs, accepts form-encoded request bodies, returns <span class="text-white font-medium">JSON-encoded</span> responses, and uses standard HTTP response codes, authentication, and verbs.
                    </p>
                </section>

                <!-- AUTHENTICATION -->
                <section id="auth" class="scroll-mt-24">
                    <div class="border-b border-zinc-800 pb-8 mb-8">
                        <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                            <span class="p-2 bg-zinc-900 rounded-lg text-indigo-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            Authentication
                        </h2>
                        <p class="text-zinc-400 mb-6">
                            The API uses <span class="text-indigo-400 font-mono text-sm px-1.5 py-0.5 bg-indigo-500/10 rounded">Bearer tokens</span> to authenticate requests. You can view and manage your API keys in the dashboard.
                        </p>
                        
                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-2">Authorization Header</h4>
                            <div class="flex items-center gap-3 font-mono text-sm">
                                <span class="text-zinc-500 select-none">Authorization:</span>
                                <span class="text-indigo-400">Bearer 1|your_api_token_here...</span>
                            </div>
                        </div>
                    </div>
                </section>

                @php
                    $endpoints = [
                        // PUBLIC
                        [
                            'id' => 'get-ping',
                            'title' => 'Health Check',
                            'method' => 'GET',
                            'url' => '/api/v1/ping',
                            'desc' => 'Check if the API is up and running. Returns the current server time.',
                            'auth' => false,
                            'params' => [],
                            'response' => [
                                "ok" => true,
                                "time" => "2023-10-27T10:00:00.000000Z"
                            ]
                        ],
                        [
                            'id' => 'get-plans',
                            'title' => 'List Public Plans',
                            'method' => 'GET',
                            'url' => '/api/v1/plans',
                            'desc' => 'Retrieve a list of all active subscription plans available for purchase.',
                            'auth' => false,
                            'params' => [],
                            'response' => [
                                "data" => [
                                    [
                                        "id" => 1,
                                        "name" => "Pro Plan",
                                        "price" => "29.00",
                                        "currency" => "USD",
                                        "features" => ["Feature A", "Feature B"]
                                    ]
                                ]
                            ]
                        ],
                        // USER
                        [
                            'id' => 'get-me',
                            'title' => 'Get Profile',
                            'method' => 'GET',
                            'url' => '/api/v1/me',
                            'desc' => 'Retrieve the currently authenticated user\'s profile information.',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "id" => 1,
                                "name" => "John Doe",
                                "email" => "john@example.com",
                                "created_at" => "2023-01-01T00:00:00.000000Z"
                            ]
                        ],
                        [
                            'id' => 'put-me',
                            'title' => 'Update Profile',
                            'method' => 'PUT',
                            'url' => '/api/v1/me',
                            'desc' => 'Update the authenticated user\'s profile details.',
                            'auth' => true,
                            'params' => [
                                'name' => 'string, required',
                                'email' => 'string, email, required'
                            ],
                            'response' => [
                                "message" => "Profile updated successfully.",
                                "user" => ["id" => 1, "name" => "Jane Doe", "email" => "jane@example.com"]
                            ]
                        ],
                        // BILLING
                        [
                            'id' => 'post-checkout',
                            'title' => 'Create Checkout Session',
                            'method' => 'POST',
                            'url' => '/api/v1/subscriptions/checkout',
                            'desc' => 'Initiate a Stripe checkout session for a specific plan.',
                            'auth' => true,
                            'params' => [
                                'plan_id' => 'integer, required (The ID of the plan to subscribe to)'
                            ],
                            'response' => [
                                "checkout_url" => "https://checkout.stripe.com/c/pay/..."
                            ]
                        ],
                        [
                            'id' => 'post-cancel',
                            'title' => 'Cancel Subscription',
                            'method' => 'POST',
                            'url' => '/api/v1/subscriptions/cancel',
                            'desc' => 'Cancel the user\'s current subscription at the end of the billing period.',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "message" => "Subscription cancelled successfully."
                            ]
                        ],
                        [
                            'id' => 'post-resume',
                            'title' => 'Resume Subscription',
                            'method' => 'POST',
                            'url' => '/api/v1/subscriptions/resume',
                            'desc' => 'Resume a subscription that is set to cancel.',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "message" => "Subscription resumed successfully."
                            ]
                        ],
                        [
                            'id' => 'get-invoices',
                            'title' => 'List Invoices',
                            'method' => 'GET',
                            'url' => '/api/v1/invoices',
                            'desc' => 'Retrieve a list of the user\'s past invoices.',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "data" => [
                                    [
                                        "id" => "in_123xyz",
                                        "total" => "$29.00",
                                        "status" => "paid",
                                        "date" => "2023-10-01"
                                    ]
                                ]
                            ]
                        ],
                        // ADMIN USERS
                        [
                            'id' => 'admin-get-users',
                            'title' => 'List Users (Admin)',
                            'method' => 'GET',
                            'url' => '/api/v1/admin/users',
                            'desc' => 'Retrieve a paginated list of all users in the system.',
                            'auth' => true,
                            'params' => ['page' => 'integer, optional'],
                            'response' => [
                                "data" => [ ["id" => 1, "name" => "User 1"] ],
                                "meta" => ["current_page" => 1, "total" => 50]
                            ]
                        ],
                         [
                            'id' => 'admin-post-users',
                            'title' => 'Create User (Admin)',
                            'method' => 'POST',
                            'url' => '/api/v1/admin/users',
                            'desc' => 'Manually create a new user account.',
                            'auth' => true,
                            'params' => [
                                'name' => 'string, required',
                                'email' => 'email, required',
                                'password' => 'string, required, min:8'
                            ],
                            'response' => [
                                "message" => "User created successfully.",
                                "user" => ["id" => 52, "email" => "new@example.com"]
                            ]
                        ],
                         [
                            'id' => 'admin-get-user',
                            'title' => 'Get User Details (Admin)',
                            'method' => 'GET',
                            'url' => '/api/v1/admin/users/{id}',
                            'desc' => 'Get full details of a specific user.',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "id" => 52, "name" => "Test User", "roles" => ["user"]
                            ]
                        ],
                         // ADMIN PLANS
                        [
                            'id' => 'admin-get-plans',
                            'title' => 'List Plans (Admin)',
                            'method' => 'GET',
                            'url' => '/api/v1/admin/plans',
                            'desc' => 'List all subscription plans (admin view).',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "data" => [ ["id" => 1, "name" => "Pro"] ]
                            ]
                        ],
                        [
                            'id' => 'admin-post-plans',
                            'title' => 'Create Plan (Admin)',
                            'method' => 'POST',
                            'url' => '/api/v1/admin/plans',
                            'desc' => 'Create a new subscription plan.',
                            'auth' => true,
                            'params' => [
                                'name' => 'string, required',
                                'price' => 'numeric, required',
                                'currency' => 'string, required (e.g. USD)',
                                'interval' => 'string, required (month/year)'
                            ],
                            'response' => [
                                "message" => "Plan created successfully.",
                                "plan" => ["id" => 3, "name" => "Enterprise"]
                            ]
                        ],
                         // ADMIN SYSTEM
                        [
                            'id' => 'admin-get-settings',
                            'title' => 'List Settings (Admin)',
                            'method' => 'GET',
                            'url' => '/api/v1/admin/settings',
                            'desc' => 'Retrieve all system configuration settings.',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "site_name" => "My SaaS",
                                "maintenance_mode" => false
                            ]
                        ],
                         [
                            'id' => 'admin-post-logo',
                            'title' => 'Upload Logo (Admin)',
                            'method' => 'POST',
                            'url' => '/api/v1/admin/settings/logo',
                            'desc' => 'Update the application logo.',
                            'auth' => true,
                            'params' => [
                                'logo' => 'file, required (image/png, image/jpeg)'
                            ],
                            'response' => [
                                "message" => "Logo updated successfully.",
                                "url" => "https://site.com/storage/logo.png"
                            ]
                        ],
                         [
                            'id' => 'admin-impersonate',
                            'title' => 'Start Impersonation',
                            'method' => 'POST',
                            'url' => '/api/v1/admin/impersonate/start/{user}',
                            'desc' => 'Log in as another user for troubleshooting. Requires MFA if enabled.',
                            'auth' => true,
                            'params' => [],
                            'response' => [
                                "message" => "Impersonating user..."
                            ]
                        ],
                    ];
                @endphp

                @foreach ($endpoints as $endpoint)
                    <section id="{{ $endpoint['id'] }}" class="scroll-mt-32">
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-12">
                            
                            <!-- Left: Documentation -->
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide
                                        @if($endpoint['method'] === 'GET') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                        @elseif($endpoint['method'] === 'POST') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                        @elseif($endpoint['method'] === 'PUT') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                        @elseif($endpoint['method'] === 'DELETE') bg-red-500/10 text-red-400 border border-red-500/20
                                        @endif">
                                        {{ $endpoint['method'] }}
                                    </span>
                                    <h2 class="text-xl font-bold text-white">{{ $endpoint['title'] }}</h2>
                                </div>
                                
                                <div class="flex items-center gap-2 font-mono text-sm text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-lg px-3 py-2 mb-6">
                                    <span class="select-none text-zinc-600">{{ config('app.url') }}</span>
                                    <span class="text-indigo-300">{{ $endpoint['url'] }}</span>
                                </div>

                                <p class="text-zinc-400 text-sm leading-relaxed mb-8">
                                    {{ $endpoint['desc'] }}
                                </p>

                                @if(!empty($endpoint['params']))
                                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Parameters</h4>
                                    <div class="border border-zinc-800 rounded-lg divide-y divide-zinc-800 bg-zinc-900/30 overflow-hidden mb-8">
                                        @foreach($endpoint['params'] as $key => $desc)
                                            <div class="flex p-3 text-sm">
                                                <div class="w-1/3 text-indigo-400 font-mono">{{ $key }}</div>
                                                <div class="w-2/3 text-zinc-400">{{ $desc }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Code Widget -->
                            <div class="relative group" 
                                 x-data="{ 
                                    tab: 'curl',
                                    copied: false,
                                    copyCode() {
                                        let code = '';
                                        if (this.tab === 'curl') code = $refs.curl.innerText;
                                        if (this.tab === 'python') code = $refs.python.innerText;
                                        if (this.tab === 'node') code = $refs.node.innerText;
                                        if (this.tab === 'php') code = $refs.php.innerText;
                                        
                                        navigator.clipboard.writeText(code);
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    }
                                 }">
                                
                                <!-- Code Header -->
                                <div class="flex items-center justify-between bg-zinc-900 border border-zinc-800 border-b-0 rounded-t-xl px-4 py-2">
                                    <div class="flex gap-4">
                                        <button @click="tab = 'curl'" :class="tab === 'curl' ? 'text-white border-b-2 border-indigo-500' : 'text-zinc-500 hover:text-zinc-300'" class="pb-2 text-xs font-medium uppercase tracking-wider transition-colors">cURL</button>
                                        <button @click="tab = 'python'" :class="tab === 'python' ? 'text-white border-b-2 border-indigo-500' : 'text-zinc-500 hover:text-zinc-300'" class="pb-2 text-xs font-medium uppercase tracking-wider transition-colors">Python</button>
                                        <button @click="tab = 'node'" :class="tab === 'node' ? 'text-white border-b-2 border-indigo-500' : 'text-zinc-500 hover:text-zinc-300'" class="pb-2 text-xs font-medium uppercase tracking-wider transition-colors">Node.js</button>
                                        <button @click="tab = 'php'" :class="tab === 'php' ? 'text-white border-b-2 border-indigo-500' : 'text-zinc-500 hover:text-zinc-300'" class="pb-2 text-xs font-medium uppercase tracking-wider transition-colors">PHP</button>
                                    </div>
                                    <button @click="copyCode()" class="text-zinc-500 hover:text-white transition-colors" title="Copy to clipboard">
                                        <template x-if="!copied">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </template>
                                        <template x-if="copied">
                                            <span class="flex items-center gap-1 text-emerald-400 text-xs font-bold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Copied!
                                            </span>
                                        </template>
                                    </button>
                                </div>

                                <!-- Code Body -->
                                <div class="bg-black/50 border border-zinc-800 rounded-b-xl p-4 overflow-x-auto text-sm font-mono leading-relaxed h-[300px] scrollbar-thin scrollbar-thumb-zinc-700 scrollbar-track-zinc-900">
                                    
                                    <!-- cURL -->
                                    <div x-show="tab === 'curl'" x-ref="curl" class="text-zinc-300 whitespace-pre">curl -X {{ $endpoint['method'] }} "{{ config('app.url') }}{{ $endpoint['url'] }}" \
@if($endpoint['auth'])  -H "Authorization: Bearer <token>" \
@endif
  -H "Content-Type: application/json" \
  -H "Accept: application/json"</div>

                                    <!-- Python -->
                                    <div x-show="tab === 'python'" x-ref="python" class="text-zinc-300 whitespace-pre">import requests

url = "{{ config('app.url') }}{{ $endpoint['url'] }}"

headers = {
    "Accept": "application/json",
    "Content-Type": "application/json"
@if($endpoint['auth'])    "Authorization": "Bearer <token>"
@endif
}

response = requests.request("{{ $endpoint['method'] }}", url, headers=headers)

print(response.text)</div>

                                    <!-- Node.js -->
                                    <div x-show="tab === 'node'" x-ref="node" class="text-zinc-300 whitespace-pre">const axios = require('axios');

let config = {
  method: '{{ strtolower($endpoint['method']) }}',
  maxBodyLength: Infinity,
  url: '{{ config('app.url') }}{{ $endpoint['url'] }}',
  headers: { 
    'Accept': 'application/json', 
    'Content-Type': 'application/json'@if($endpoint['auth']), 
    'Authorization': 'Bearer <token>'@endif

  }
};

axios.request(config)
.then((response) => {
  console.log(JSON.stringify(response.data));
})
.catch((error) => {
  console.log(error);
});</div>

                                    <!-- PHP -->
                                    <div x-show="tab === 'php'" x-ref="php" class="text-zinc-300 whitespace-pre">$client = new \GuzzleHttp\Client();

$response = $client->request('{{ $endpoint['method'] }}', '{{ config('app.url') }}{{ $endpoint['url'] }}', [
  'headers' => [
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
@if($endpoint['auth'])    'Authorization' => 'Bearer <token>',
@endif
  ],
]);

echo $response->getBody();</div>
                                </div>
                                
                                <!-- Response Preview -->
                                <div class="mt-4">
                                     <h4 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Example Response</h4>
                                     <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-3 overflow-x-auto">
                                         <pre class="text-xs text-indigo-300 font-mono">{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                     </div>
                                </div>

                            </div>
                        </div>
                    </section>
                @endforeach
            </div>
        </main>
    </div>
</div>
@endsection
