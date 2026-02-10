{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white gap-2">Admin Dashboard</h1>
                <p class="text-sm text-zinc-400">Overview &amp; controls for administrators.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Manage Users</a>
                <a href="{{ route('admin.audit.index') }}" class="inline-flex items-center justify-center rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">Audit Log</a>
                @if (Route::has('admin.settings.index'))
                    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center justify-center rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">System Settings</a>
                @endif
                <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center justify-center rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">Plans</a>

                {{-- API Docs link --}}
                @php
                    $apiDocsUrl = Route::has('admin.docs.api')
                        ? route('admin.docs.api')
                        : (Route::has('docs') ? route('docs') : url('/docs'));
                @endphp
                <a href="{{ $apiDocsUrl }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600/10 px-4 py-2 text-sm font-semibold text-emerald-400 shadow-sm hover:bg-emerald-600/20 ring-1 ring-inset ring-emerald-500/20 transition-colors">API Documentation</a>
            </div>
        </div>

        {{-- Overview card --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Stats --}}
                <div class="col-span-2 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        
                        {{-- Total Revenue --}}
                        <a href="{{ route('admin.revenue.index') }}" class="group block p-4 bg-zinc-950/50 rounded-lg border border-zinc-800 hover:border-emerald-500/50 hover:bg-zinc-900 transition-all hover:scale-[1.02] hover:shadow-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400 group-hover:bg-emerald-500/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="text-sm font-medium text-zinc-400 group-hover:text-emerald-300 transition-colors">Total Revenue</div>
                            </div>
                            <div class="text-2xl font-bold text-white">${{ number_format($totalRevenue, 2) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Lifetime earnings</div>
                        </a>

                        {{-- Active Subscribers --}}
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}" class="group block p-4 bg-zinc-950/50 rounded-lg border border-zinc-800 hover:border-indigo-500/50 hover:bg-zinc-900 transition-all hover:scale-[1.02] hover:shadow-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400 group-hover:bg-indigo-500/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                                <div class="text-sm font-medium text-zinc-400 group-hover:text-indigo-300 transition-colors">Active Subscribers</div>
                            </div>
                            <div class="text-2xl font-bold text-white">{{ number_format($activeSubscribers) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Current active plans</div>
                        </a>

                        {{-- Pending Payments (Bkash Manual) --}}
                        @if($bkashEnabled)
                        <a href="{{ route('admin.transactions.index', ['status' => 'pending']) }}" class="group block p-4 bg-zinc-950/50 rounded-lg border border-zinc-800 hover:border-yellow-500/50 hover:bg-zinc-900 transition-all hover:scale-[1.02] hover:shadow-lg {{ $pendingPaymentsCount > 0 ? 'ring-1 ring-yellow-500/30' : '' }}">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-yellow-500/10 rounded-lg text-yellow-400 group-hover:bg-yellow-500/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="text-sm font-medium text-zinc-400 group-hover:text-yellow-300 transition-colors">Pending Payments</div>
                            </div>
                            <div class="text-2xl font-bold text-white">{{ number_format($pendingPaymentsCount) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Bkash awaiting review</div>
                        </a>
                        @endif

                        {{-- Total Users --}}
                        <a href="{{ route('admin.users.index') }}" class="group block p-4 bg-zinc-950/50 rounded-lg border border-zinc-800 hover:border-blue-500/50 hover:bg-zinc-900 transition-all hover:scale-[1.02] hover:shadow-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-blue-500/10 rounded-lg text-blue-400 group-hover:bg-blue-500/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                </div>
                                <div class="text-sm font-medium text-zinc-400 group-hover:text-blue-300 transition-colors">Total Users</div>
                            </div>
                            <div class="text-2xl font-bold text-white">{{ number_format($userCount) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Total registered</div>
                        </a>

                        {{-- Verified Users --}}
                        <a href="{{ route('admin.users.index', ['verified' => 'true']) }}" class="group block p-4 bg-zinc-950/50 rounded-lg border border-zinc-800 hover:border-teal-500/50 hover:bg-zinc-900 transition-all hover:scale-[1.02] hover:shadow-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-teal-500/10 rounded-lg text-teal-400 group-hover:bg-teal-500/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="text-sm font-medium text-zinc-400 group-hover:text-teal-300 transition-colors">Verified Users</div>
                            </div>
                            <div class="text-2xl font-bold text-white">{{ number_format($verifiedCount) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Email verified</div>
                        </a>

                        {{-- Pending Tickets (Conditional) --}}
                        @if(\App\Helpers\Feature::enabled('support_enabled'))
                        <a href="{{ route('admin.support.index', ['status' => 'pending']) }}" class="group block p-4 bg-zinc-950/50 rounded-lg border border-zinc-800 hover:border-orange-500/50 hover:bg-zinc-900 transition-all hover:scale-[1.02] hover:shadow-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-orange-500/10 rounded-lg text-orange-400 group-hover:bg-orange-500/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414" /></svg>
                                </div>
                                <div class="text-sm font-medium text-zinc-400 group-hover:text-orange-300 transition-colors">Pending Tickets</div>
                            </div>
                            <div class="text-2xl font-bold text-white">{{ number_format($pendingTicketsCount) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Needs attention</div>
                        </a>
                        @endif

                        {{-- New Users (30d) --}}
                        <a href="{{ route('admin.users.index', ['sort' => 'newest']) }}" class="group block p-4 bg-zinc-950/50 rounded-lg border border-zinc-800 hover:border-purple-500/50 hover:bg-zinc-900 transition-all hover:scale-[1.02] hover:shadow-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-purple-500/10 rounded-lg text-purple-400 group-hover:bg-purple-500/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                                </div>
                                <div class="text-sm font-medium text-zinc-400 group-hover:text-purple-300 transition-colors">New Users (30d)</div>
                            </div>
                            <div class="text-2xl font-bold text-white">{{ number_format($newUsersCount) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Growth this month</div>
                        </a>
                    </div>


                </div>

                {{-- Quick actions --}}
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('admin.users.create') }}" class="flex items-center justify-center w-full rounded-md bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Create Admin/User</a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-center w-full rounded-md bg-zinc-800 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">User Directory</a>
                    <a href="{{ route('admin.audit.index') }}" class="flex items-center justify-center w-full rounded-md bg-zinc-800 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">View Audit Log</a>
                    @if (Route::has('admin.settings.index'))
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center justify-center w-full rounded-md bg-zinc-800 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">System Settings</a>
                    @endif
                    <a href="{{ route('admin.plans.index') }}" class="flex items-center justify-center w-full rounded-md bg-indigo-600/10 px-4 py-3 text-sm font-semibold text-indigo-400 shadow-sm hover:bg-indigo-600/20 ring-1 ring-inset ring-indigo-500/20 transition-colors">Subscription Plans</a>
                </div>
            </div>
        </div>

        {{-- Recent users table --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl overflow-hidden">
            <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white">Recent Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">View all</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-800 text-left">
                    <thead>
                    <tr class="text-zinc-400 text-xs uppercase tracking-wider bg-zinc-900/50">
                        <th class="px-6 py-3 font-medium">Name</th>
                        <th class="px-6 py-3 font-medium">Email</th>
                        <th class="px-6 py-3 font-medium">Verified</th>
                        <th class="px-6 py-3 font-medium">Joined</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                    @forelse ($recentUsers as $u)
                        <tr class="hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-white">{{ $u->name }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-400">{{ $u->email }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($u->email_verified_at)
                                    <span class="inline-flex items-center rounded-md bg-green-400/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-400/20">Yes</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-yellow-400/10 px-2 py-1 text-xs font-medium text-yellow-500 ring-1 ring-inset ring-yellow-400/20">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-500">{{ $u->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-zinc-500 py-8">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
