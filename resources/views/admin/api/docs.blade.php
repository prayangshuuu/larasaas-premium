@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-zinc-950 text-zinc-300 font-sans selection:bg-indigo-500/30" x-data="apiDocs()">
    <div class="flex max-w-8xl mx-auto">

        @php
            $endpoints = [
                // ── Public & Authentication ──
                [
                    'group' => 'Public & Authentication',
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
                    'group' => 'Public & Authentication',
                    'id' => 'get-plans',
                    'title' => 'List Public Plans',
                    'method' => 'GET',
                    'url' => '/api/v1/plans',
                    'desc' => 'Retrieve a list of all active subscription plans available for purchase. Returns plan id, name, price, and features.',
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

                // ── User ──
                [
                    'group' => 'User',
                    'id' => 'get-me',
                    'title' => 'Get User Profile',
                    'method' => 'GET',
                    'url' => '/api/v1/me',
                    'desc' => 'Return the authenticated user\'s details — id, name, email, and timestamps.',
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
                    'group' => 'User',
                    'id' => 'put-me',
                    'title' => 'Update Profile',
                    'method' => 'PUT',
                    'url' => '/api/v1/me',
                    'desc' => 'Update the authenticated user\'s profile details (name and email).',
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

                // ── Plans ──
                [
                    'group' => 'Plans',
                    'id' => 'admin-get-plans',
                    'title' => 'List All Plans (Admin)',
                    'method' => 'GET',
                    'url' => '/api/v1/admin/plans',
                    'desc' => 'Admin-only. List all subscription plans, including inactive ones.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "data" => [
                            ["id" => 1, "name" => "Starter", "price" => "9.00", "is_active" => true],
                            ["id" => 2, "name" => "Pro", "price" => "29.00", "is_active" => true],
                        ]
                    ]
                ],
                [
                    'group' => 'Plans',
                    'id' => 'admin-post-plans',
                    'title' => 'Create Plan (Admin)',
                    'method' => 'POST',
                    'url' => '/api/v1/admin/plans',
                    'desc' => 'Admin-only. Create a new subscription plan.',
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
                [
                    'group' => 'Plans',
                    'id' => 'admin-put-plans',
                    'title' => 'Update Plan (Admin)',
                    'method' => 'PUT',
                    'url' => '/api/v1/admin/plans/{id}',
                    'desc' => 'Admin-only. Update an existing subscription plan.',
                    'auth' => true,
                    'params' => ['name' => 'string', 'price' => 'numeric'],
                    'response' => [
                        "message" => "Plan updated successfully.",
                        "plan" => ["id" => 3, "price" => 99.00]
                    ]
                ],
                [
                    'group' => 'Plans',
                    'id' => 'admin-delete-plans',
                    'title' => 'Delete Plan (Admin)',
                    'method' => 'DELETE',
                    'url' => '/api/v1/admin/plans/{id}',
                    'desc' => 'Admin-only. Delete a subscription plan.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "message" => "Plan deleted successfully."
                    ]
                ],

                // ── Subscriptions ──
                [
                    'group' => 'Subscriptions',
                    'id' => 'get-subscription-current',
                    'title' => 'Current Subscription',
                    'method' => 'GET',
                    'url' => '/api/v1/subscriptions/current',
                    'desc' => 'Returns the authenticated user\'s current active subscription details, including plan info, status, and billing dates.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "subscription" => [
                            "id" => 1,
                            "plan" => "Pro Plan",
                            "status" => "active",
                            "current_period_end" => "2024-02-01T00:00:00.000000Z"
                        ]
                    ]
                ],
                [
                    'group' => 'Subscriptions',
                    'id' => 'post-checkout',
                    'title' => 'Create Checkout Session',
                    'method' => 'POST',
                    'url' => '/api/v1/subscriptions/checkout',
                    'desc' => 'Initiate a Stripe checkout session for a specific plan. Returns a checkout URL to redirect the user.',
                    'auth' => true,
                    'params' => [
                        'plan_id' => 'integer, required (The ID of the plan)'
                    ],
                    'response' => [
                        "checkout_url" => "https://checkout.stripe.com/c/pay/..."
                    ]
                ],
                [
                    'group' => 'Subscriptions',
                    'id' => 'post-cancel',
                    'title' => 'Cancel Subscription',
                    'method' => 'POST',
                    'url' => '/api/v1/subscriptions/cancel',
                    'desc' => 'Cancel the user\'s current subscription. It will remain active until the end of the billing period.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "message" => "Subscription cancelled successfully."
                    ]
                ],
                [
                    'group' => 'Subscriptions',
                    'id' => 'post-resume',
                    'title' => 'Resume Subscription',
                    'method' => 'POST',
                    'url' => '/api/v1/subscriptions/resume',
                    'desc' => 'Resume a subscription that has been cancelled but is still on grace period.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "message" => "Subscription resumed successfully."
                    ]
                ],
                [
                    'group' => 'Subscriptions',
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
                                "total" => "\$29.00",
                                "status" => "paid",
                                "date" => "2023-10-01"
                            ]
                        ]
                    ]
                ],
                [
                    'group' => 'Subscriptions',
                    'id' => 'get-invoice-detail',
                    'title' => 'Get Invoice Details',
                    'method' => 'GET',
                    'url' => '/api/v1/invoices/{invoice}',
                    'desc' => 'Retrieve details for a specific invoice by ID.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "id" => "in_123xyz",
                        "total" => "\$29.00",
                        "pdf_url" => "https://stripe.com/invoices/in_123xyz.pdf"
                    ]
                ],

                // ── Admin: User Management ──
                [
                    'group' => 'Admin: User Management',
                    'id' => 'admin-get-users',
                    'title' => 'List Users',
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
                    'group' => 'Admin: User Management',
                    'id' => 'admin-post-users',
                    'title' => 'Create User',
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
                    'group' => 'Admin: User Management',
                    'id' => 'admin-get-user',
                    'title' => 'Get User',
                    'method' => 'GET',
                    'url' => '/api/v1/admin/users/{id}',
                    'desc' => 'Get full details of a specific user.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "id" => 52, "name" => "Test User", "roles" => ["user"]
                    ]
                ],
                [
                    'group' => 'Admin: User Management',
                    'id' => 'admin-put-user',
                    'title' => 'Update User',
                    'method' => 'PUT',
                    'url' => '/api/v1/admin/users/{id}',
                    'desc' => 'Update an existing user\'s details.',
                    'auth' => true,
                    'params' => ['name' => 'string', 'email' => 'email'],
                    'response' => [
                        "message" => "User updated successfully.",
                        "user" => ["id" => 52, "name" => "Updated Name"]
                    ]
                ],
                [
                    'group' => 'Admin: User Management',
                    'id' => 'admin-delete-user',
                    'title' => 'Delete User',
                    'method' => 'DELETE',
                    'url' => '/api/v1/admin/users/{id}',
                    'desc' => 'Permanently delete a user account.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "message" => "User deleted successfully."
                    ]
                ],

                // ── Admin: System & Settings ──
                [
                    'group' => 'Admin: System & Settings',
                    'id' => 'admin-get-settings',
                    'title' => 'List All Settings',
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
                    'group' => 'Admin: System & Settings',
                    'id' => 'admin-put-settings',
                    'title' => 'Update Setting',
                    'method' => 'PUT',
                    'url' => '/api/v1/admin/settings/{key}',
                    'desc' => 'Update a specific system setting value.',
                    'auth' => true,
                    'params' => ['value' => 'mixed, required'],
                    'response' => [
                        "message" => "Setting updated."
                    ]
                ],
                [
                    'group' => 'Admin: System & Settings',
                    'id' => 'admin-post-logo',
                    'title' => 'Upload Logo',
                    'method' => 'POST',
                    'url' => '/api/v1/admin/settings/logo',
                    'desc' => 'Update the application logo via multipart upload.',
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
                    'group' => 'Admin: System & Settings',
                    'id' => 'admin-get-audit',
                    'title' => 'View Audit Logs',
                    'method' => 'GET',
                    'url' => '/api/v1/admin/audit',
                    'desc' => 'Retrieve a paginated log of admin actions for auditing purposes.',
                    'auth' => true,
                    'params' => ['page' => 'integer, optional'],
                    'response' => [
                        "data" => [
                            ["action" => "user_deleted", "admin_id" => 1, "target_id" => 55]
                        ]
                    ]
                ],

                // ── Admin: Impersonation ──
                [
                    'group' => 'Admin: Impersonation',
                    'id' => 'admin-impersonate-start',
                    'title' => 'Start Impersonation',
                    'method' => 'POST',
                    'url' => '/api/v1/admin/impersonate/start/{user}',
                    'desc' => 'Log in as another user for troubleshooting. Requires MFA validation.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "message" => "Impersonating user..."
                    ]
                ],
                [
                    'group' => 'Admin: Impersonation',
                    'id' => 'admin-impersonate-stop',
                    'title' => 'Stop Impersonation',
                    'method' => 'POST',
                    'url' => '/api/v1/admin/impersonate/stop',
                    'desc' => 'Stop impersonating and return to the original admin session.',
                    'auth' => true,
                    'params' => [],
                    'response' => [
                        "message" => "Welcome back."
                    ]
                ],
            ];

            $groupedEndpoints = collect($endpoints)->groupBy('group');
        @endphp

        {{-- ═══════════════════════════════════════════
             Sidebar Navigation (sticky)
        ═══════════════════════════════════════════ --}}
        <nav class="hidden lg:block w-72 h-screen sticky top-0 overflow-y-auto border-r border-zinc-800 bg-zinc-950 px-6 py-8 scrollbar-thin scrollbar-thumb-zinc-800 scrollbar-track-transparent">
            <div class="mb-10">
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    API Reference
                </h1>
                <p class="mt-2 text-xs text-zinc-500 uppercase tracking-wider font-semibold">Version 1.0</p>
            </div>

            <div class="space-y-8" x-data="{ activeSection: window.location.hash || '#introduction' }">
                {{-- Static intro links --}}
                <div>
                    <h3 class="flex items-center gap-2 text-xs font-bold text-white uppercase tracking-wider mb-3">
                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Overview
                    </h3>
                    <ul class="space-y-1 text-sm border-l border-zinc-800 ml-2">
                        <li>
                            <a href="#introduction"
                               @click="activeSection = '#introduction'"
                               :class="activeSection === '#introduction' ? 'text-indigo-400 border-l border-indigo-500 -ml-px pl-4 font-medium' : 'text-zinc-400 hover:text-white pl-4 border-l border-transparent hover:border-zinc-700 transition-all'"
                               class="block py-1">Introduction</a>
                        </li>
                        <li>
                            <a href="#auth"
                               @click="activeSection = '#auth'"
                               :class="activeSection === '#auth' ? 'text-indigo-400 border-l border-indigo-500 -ml-px pl-4 font-medium' : 'text-zinc-400 hover:text-white pl-4 border-l border-transparent hover:border-zinc-700 transition-all'"
                               class="block py-1">Authentication</a>
                        </li>
                    </ul>
                </div>

                {{-- Dynamic endpoint groups --}}
                @foreach($groupedEndpoints as $group => $items)
                <div>
                    <h3 class="flex items-center gap-2 text-xs font-bold text-white uppercase tracking-wider mb-3">
                        @if(Str::contains($group, 'Public'))
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif(Str::contains($group, 'User'))
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @elseif(Str::contains($group, 'Plan'))
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        @elseif(Str::contains($group, 'Subscription') || Str::contains($group, 'Billing'))
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        @elseif(Str::contains($group, 'Impersonation'))
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @else
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @endif
                        {{ $group }}
                    </h3>
                    <ul class="space-y-1 text-sm border-l border-zinc-800 ml-2">
                        @foreach($items as $item)
                        <li>
                            <a href="#{{ $item['id'] }}"
                               @click="activeSection = '#{{ $item['id'] }}'"
                               :class="activeSection === '#{{ $item['id'] }}' ? 'text-indigo-400 border-l border-indigo-500 -ml-px pl-4 font-medium' : 'text-zinc-400 hover:text-white pl-4 border-l border-transparent hover:border-zinc-700 transition-all'"
                               class="block py-1 truncate pr-4" title="{{ $item['title'] }}">
                                {{ $item['title'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </nav>

        {{-- ═══════════════════════════════════════════
             Main Content Area
        ═══════════════════════════════════════════ --}}
        <main class="flex-1 w-0">
            <div class="max-w-6xl mx-auto px-6 sm:px-12 py-16 space-y-20">

                {{-- ──────────────────────────
                     Global API Key Input
                ────────────────────────── --}}
                <div class="sticky top-0 z-50 -mx-6 sm:-mx-12 px-6 sm:px-12 pt-4 pb-4 bg-zinc-950/95 backdrop-blur-xl border-b border-zinc-800/60">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-500/10 border border-indigo-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                            <div>
                                <label for="global-api-key" class="text-sm font-semibold text-white">API Key</label>
                                <p class="text-xs text-zinc-500">8-character token (e.g. <code class="text-indigo-400/80">1RIPEZGX</code>)</p>
                            </div>
                        </div>
                        <div class="flex-1 w-full sm:max-w-md">
                            <div class="relative">
                                <input
                                    id="global-api-key"
                                    type="text"
                                    x-model="apiKey"
                                    @input="persistKey()"
                                    placeholder="Paste your API key here..."
                                    maxlength="64"
                                    class="w-full bg-zinc-900 border border-zinc-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 rounded-lg px-4 py-2.5 text-sm font-mono text-white placeholder:text-zinc-600 transition-all"
                                />
                                <div x-show="apiKey.length > 0" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <span class="flex items-center gap-1.5 text-xs text-emerald-400">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                        Ready
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ──────────────────────────
                     Introduction Section
                ────────────────────────── --}}
                <section id="introduction" class="scroll-mt-32 border-b border-zinc-800 pb-16">
                    <h1 class="text-5xl font-extrabold text-white tracking-tight mb-8">API Documentation</h1>
                    <p class="text-xl text-zinc-400 leading-relaxed max-w-4xl">
                        Welcome to the official API reference. Our API is organized around <span class="text-white font-medium">REST</span>.
                        It has predictable resource-oriented URLs, accepts form-encoded request bodies,
                        returns <span class="text-white font-medium">JSON-encoded</span> responses, and uses standard
                        HTTP response codes, authentication, and verbs.
                    </p>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl">
                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-white mb-1">Base URL</h4>
                            <code class="text-xs text-indigo-400 font-mono">{{ config('app.url') }}/api/v1</code>
                        </div>
                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-white mb-1">Format</h4>
                            <span class="text-xs text-zinc-400">JSON (application/json)</span>
                        </div>
                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-white mb-1">Auth</h4>
                            <span class="text-xs text-zinc-400">Bearer Token (Sanctum)</span>
                        </div>
                    </div>
                </section>

                {{-- ──────────────────────────
                     Authentication Section
                ────────────────────────── --}}
                <section id="auth" class="scroll-mt-32 border-b border-zinc-800 pb-16">
                    <h2 class="text-3xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="p-2 bg-zinc-900 rounded-lg text-indigo-400 shadow-lg shadow-indigo-900/10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        Authentication
                    </h2>
                    <p class="text-lg text-zinc-400 mb-6 max-w-4xl">
                        The API uses <span class="text-indigo-400 font-mono text-base px-2 py-1 bg-indigo-500/10 rounded-md">Bearer tokens</span>
                        (Laravel Sanctum) to authenticate requests. Your API key is a short <strong class="text-white">8-character</strong> string
                        (e.g. <code class="text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded text-sm">1RIPEZGX</code>).
                        Manage your keys from the <a href="{{ route('admin.settings.index') }}#api-keys" class="text-indigo-400 hover:text-indigo-300 underline underline-offset-4 decoration-indigo-500/30 transition-colors">Admin Settings</a> page.
                    </p>

                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-8 max-w-3xl">
                        <h4 class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-4">Example Request Header</h4>
                        <div class="bg-zinc-950 rounded-lg p-4 font-mono text-sm">
                            <span class="text-zinc-500">Authorization:</span>
                            <span class="text-indigo-400"> Bearer </span><span class="text-emerald-400">1RIPEZGX</span>
                        </div>
                    </div>

                    <div class="mt-8 bg-amber-500/5 border border-amber-500/20 rounded-xl p-6 max-w-3xl">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <div>
                                <h5 class="text-sm font-semibold text-amber-400 mb-1">Keep your keys safe</h5>
                                <p class="text-sm text-zinc-400">Never share your API key in public repositories or client-side code. Treat it like a password.</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ──────────────────────────
                     Endpoint Sections
                ────────────────────────── --}}
                @foreach ($endpoints as $idx => $endpoint)
                    <section id="{{ $endpoint['id'] }}" class="scroll-mt-32 pt-10 border-t border-zinc-900/50">
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-12 xl:gap-16">

                            {{-- Left Column: Documentation --}}
                            <div class="flex flex-col">
                                <div class="flex items-center gap-4 mb-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide shadow-sm
                                        @if($endpoint['method'] === 'GET') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-emerald-900/10
                                        @elseif($endpoint['method'] === 'POST') bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-blue-900/10
                                        @elseif($endpoint['method'] === 'PUT') bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-amber-900/10
                                        @elseif($endpoint['method'] === 'DELETE') bg-red-500/10 text-red-400 border border-red-500/20 shadow-red-900/10
                                        @endif">
                                        {{ $endpoint['method'] }}
                                    </span>
                                    <h2 class="text-2xl font-bold text-white tracking-tight">{{ $endpoint['title'] }}</h2>
                                </div>

                                {{-- URL bar --}}
                                <div class="group flex items-center gap-3 font-mono text-sm text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-lg px-4 py-3 mb-8 hover:border-zinc-700 transition-colors">
                                    <span class="select-none text-zinc-600 font-bold">$</span>
                                    <span class="text-zinc-300">{{ config('app.url') }}</span><span class="text-indigo-400 font-medium">{{ $endpoint['url'] }}</span>
                                </div>

                                <p class="text-zinc-400 text-base leading-relaxed mb-8">
                                    {{ $endpoint['desc'] }}
                                </p>

                                @if($endpoint['auth'])
                                    <div class="flex items-center gap-2 mb-6 text-xs">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span class="text-amber-400 font-semibold uppercase tracking-wider">Requires Authentication</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 mb-6 text-xs">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-emerald-400 font-semibold uppercase tracking-wider">Public — No Auth Required</span>
                                    </div>
                                @endif

                                @if(!empty($endpoint['params']))
                                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-zinc-800 pb-2">Parameters</h4>
                                    <div class="space-y-4 mb-8">
                                        @foreach($endpoint['params'] as $key => $desc)
                                            <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-4 text-sm">
                                                <div class="w-full sm:w-1/3 shrink-0">
                                                    <span class="font-mono text-indigo-400 bg-indigo-500/5 px-2 py-1 rounded">{{ $key }}</span>
                                                </div>
                                                <div class="w-full sm:w-2/3 text-zinc-400 leading-relaxed">{{ $desc }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Right Column: Code + Try-It Console --}}
                            <div class="xl:sticky xl:top-32 self-start">
                                <div class="rounded-xl overflow-hidden bg-zinc-950 border border-zinc-800 shadow-2xl shadow-black/50"
                                    x-data="{
                                        tab: 'curl',
                                        copied: false,
                                        copyCode() {
                                            let code = '';
                                            if (this.tab === 'curl') code = $refs.curl_{{ $idx }}.innerText;
                                            if (this.tab === 'python') code = $refs.python_{{ $idx }}.innerText;
                                            if (this.tab === 'node') code = $refs.node_{{ $idx }}.innerText;
                                            if (this.tab === 'php') code = $refs.php_{{ $idx }}.innerText;
                                            navigator.clipboard.writeText(code);
                                            this.copied = true;
                                            setTimeout(() => this.copied = false, 2000);
                                        }
                                    }">

                                    {{-- Tab bar --}}
                                    <div class="flex items-center justify-between bg-zinc-900/80 backdrop-blur border-b border-zinc-800 px-2">
                                        <div class="flex">
                                            <button @click="tab = 'curl'" :class="tab === 'curl' ? 'bg-zinc-800 text-white' : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/50'" class="px-4 py-3 text-xs font-bold uppercase tracking-wider transition-all rounded-t-lg mt-1 h-10">cURL</button>
                                            <button @click="tab = 'python'" :class="tab === 'python' ? 'bg-zinc-800 text-white' : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/50'" class="px-4 py-3 text-xs font-bold uppercase tracking-wider transition-all rounded-t-lg mt-1 h-10">Python</button>
                                            <button @click="tab = 'node'" :class="tab === 'node' ? 'bg-zinc-800 text-white' : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/50'" class="px-4 py-3 text-xs font-bold uppercase tracking-wider transition-all rounded-t-lg mt-1 h-10">Node.js</button>
                                            <button @click="tab = 'php'" :class="tab === 'php' ? 'bg-zinc-800 text-white' : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/50'" class="px-4 py-3 text-xs font-bold uppercase tracking-wider transition-all rounded-t-lg mt-1 h-10">PHP</button>
                                        </div>
                                        <button @click="copyCode()" class="flex items-center gap-2 px-3 py-1.5 rounded-md text-zinc-500 hover:text-white hover:bg-zinc-800 transition-all mr-2" title="Copy to clipboard">
                                            <template x-if="!copied">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </template>
                                            <template x-if="copied">
                                                <div class="flex items-center gap-1.5 text-emerald-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span class="text-xs font-bold">Copied</span>
                                                </div>
                                            </template>
                                        </button>
                                    </div>

                                    {{-- Code body --}}
                                    <div class="bg-[#0D0D0D] p-6 overflow-x-auto text-[13px] font-mono leading-relaxed max-h-[280px] scrollbar-thin scrollbar-thumb-zinc-800 scrollbar-track-transparent">
                                        {{-- cURL --}}
                                        <div x-show="tab === 'curl'" x-ref="curl_{{ $idx }}" class="text-zinc-300 whitespace-pre"><span class="text-indigo-400">curl</span> -X {{ $endpoint['method'] }} \
  "{{ config('app.url') }}{{ $endpoint['url'] }}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" @if($endpoint['auth'])\
  -H "Authorization: Bearer &lt;token&gt;"@endif</div>

                                        {{-- Python --}}
                                        <div x-show="tab === 'python'" x-ref="python_{{ $idx }}" class="text-zinc-300 whitespace-pre"><span class="text-purple-400">import</span> requests

url = "{{ config('app.url') }}{{ $endpoint['url'] }}"

headers = {
    "Accept": "application/json",
    "Content-Type": "application/json"@if($endpoint['auth']),
    "Authorization": "Bearer &lt;token&gt;"@endif

}

response = requests.request("{{ $endpoint['method'] }}", url, headers=headers)

print(response.text)</div>

                                        {{-- Node.js --}}
                                        <div x-show="tab === 'node'" x-ref="node_{{ $idx }}" class="text-zinc-300 whitespace-pre"><span class="text-purple-400">const</span> axios = require('axios');

<span class="text-purple-400">let</span> config = {
  method: '{{ strtolower($endpoint['method']) }}',
  url: '{{ config('app.url') }}{{ $endpoint['url'] }}',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'@if($endpoint['auth']),
    'Authorization': 'Bearer &lt;token&gt;'@endif

  }
};

axios.request(config)
.then((response) => {
  console.log(JSON.stringify(response.data));
})
.catch((error) => {
  console.log(error);
});</div>

                                        {{-- PHP --}}
                                        <div x-show="tab === 'php'" x-ref="php_{{ $idx }}" class="text-zinc-300 whitespace-pre">$client = <span class="text-purple-400">new</span> \GuzzleHttp\Client();

$response = $client->request('{{ $endpoint['method'] }}', '{{ config('app.url') }}{{ $endpoint['url'] }}', [
  'headers' => [
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
@if($endpoint['auth'])    'Authorization' => 'Bearer &lt;token&gt;',
@endif  ],
]);

echo $response->getBody();</div>
                                    </div>

                                    {{-- Example response --}}
                                    <div class="border-t border-zinc-800">
                                        <div class="flex items-center justify-between px-4 py-2 bg-zinc-900/50 border-b border-zinc-800/50">
                                            <h4 class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Example Response</h4>
                                            <span class="text-[10px] font-mono text-zinc-600">JSON</span>
                                        </div>
                                        <div class="bg-[#111] p-4 overflow-x-auto max-h-[200px] scrollbar-thin scrollbar-thumb-zinc-800 scrollbar-track-transparent">
                                            <pre class="text-xs text-emerald-400 font-mono">{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </div>
                                    </div>
                                </div>

                                {{-- ─── Interactive Try-It Console ─── --}}
                                <div class="mt-4 rounded-xl overflow-hidden border border-zinc-800 bg-zinc-900/30"
                                     x-data="{
                                        loading: false,
                                        statusCode: null,
                                        responseBody: null,
                                        hasRun: false,
                                        async sendRequest() {
                                            this.loading = true;
                                            this.statusCode = null;
                                            this.responseBody = null;
                                            this.hasRun = true;

                                            const method = '{{ $endpoint['method'] }}';
                                            const url = '{{ config('app.url') }}{{ $endpoint['url'] }}';
                                            const key = $root.apiKey;
                                            const headers = {
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json',
                                            };
                                            @if($endpoint['auth'])
                                            if (key) headers['Authorization'] = 'Bearer ' + key;
                                            @endif

                                            try {
                                                const res = await fetch(url, {
                                                    method: method,
                                                    headers: headers,
                                                });
                                                this.statusCode = res.status;
                                                const text = await res.text();
                                                try { this.responseBody = JSON.stringify(JSON.parse(text), null, 2); }
                                                catch { this.responseBody = text; }
                                            } catch (err) {
                                                this.statusCode = 0;
                                                this.responseBody = 'Network Error: ' + err.message;
                                            }
                                            this.loading = false;
                                        }
                                     }">
                                    <div class="flex items-center justify-between px-4 py-3 bg-zinc-900/80 border-b border-zinc-800/50">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            <span class="text-xs font-bold text-white uppercase tracking-wider">Try It</span>
                                        </div>
                                        <button
                                            @click="sendRequest()"
                                            :disabled="loading"
                                            class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-all shadow-lg shadow-indigo-900/30 hover:shadow-indigo-900/50">
                                            <template x-if="loading">
                                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </template>
                                            <template x-if="!loading">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </template>
                                            <span x-text="loading ? 'Sending...' : 'Test Request'"></span>
                                        </button>
                                    </div>

                                    {{-- Response area --}}
                                    <div x-show="hasRun" x-cloak x-transition.opacity.duration.300ms>
                                        {{-- Status code bar --}}
                                        <div class="flex items-center gap-3 px-4 py-2.5 border-b border-zinc-800/50"
                                             :class="statusCode >= 200 && statusCode < 300 ? 'bg-emerald-500/5' : 'bg-red-500/5'">
                                            <span class="text-xs font-bold uppercase tracking-wider"
                                                  :class="statusCode >= 200 && statusCode < 300 ? 'text-emerald-400' : 'text-red-400'">
                                                Status
                                            </span>
                                            <span class="font-mono text-sm font-bold"
                                                  :class="statusCode >= 200 && statusCode < 300 ? 'text-emerald-400' : 'text-red-400'"
                                                  x-text="statusCode === 0 ? 'ERR' : statusCode"></span>
                                            <span class="text-xs text-zinc-500" x-text="statusCode >= 200 && statusCode < 300 ? 'OK' : (statusCode === 401 ? 'Unauthorized' : (statusCode === 404 ? 'Not Found' : (statusCode === 0 ? 'Network Error' : 'Error')))"></span>
                                        </div>
                                        {{-- Response body --}}
                                        <div class="bg-zinc-950 p-4 overflow-x-auto max-h-[300px] scrollbar-thin scrollbar-thumb-zinc-800 scrollbar-track-transparent">
                                            <pre class="text-xs font-mono" :class="statusCode >= 200 && statusCode < 300 ? 'text-emerald-400' : 'text-red-400'" x-text="responseBody || 'Waiting...'"></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endforeach

                {{-- Footer spacer --}}
                <div class="h-32"></div>
            </div>
        </main>
    </div>
</div>

<script>
    function apiDocs() {
        return {
            apiKey: localStorage.getItem('api_docs_key') || '',
            persistKey() {
                localStorage.setItem('api_docs_key', this.apiKey);
            }
        }
    }
</script>
@endsection
