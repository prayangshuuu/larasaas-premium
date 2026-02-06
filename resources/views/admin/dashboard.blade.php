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
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 bg-zinc-950/50 rounded-lg border border-zinc-800">
                            <div class="text-sm font-medium text-zinc-400">Total Revenue</div>
                            <div class="mt-2 text-3xl font-bold text-emerald-400">${{ number_format($totalRevenue, 2) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Lifetime earnings</div>
                        </div>

                        <div class="p-4 bg-zinc-950/50 rounded-lg border border-zinc-800">
                            <div class="text-sm font-medium text-zinc-400">Active Subscribers</div>
                            <div class="mt-2 text-3xl font-bold text-indigo-400">{{ number_format($activeSubscribers) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Current active plans</div>
                        </div>

                        <div class="p-4 bg-zinc-950/50 rounded-lg border border-zinc-800">
                            <div class="text-sm font-medium text-zinc-400">Users</div>
                            <div class="mt-2 text-3xl font-bold text-white">{{ number_format($userCount) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Total registered</div>
                        </div>

                        <div class="p-4 bg-zinc-950/50 rounded-lg border border-zinc-800">
                            <div class="text-sm font-medium text-zinc-400">Verified</div>
                            <div class="mt-2 text-3xl font-bold text-zinc-200">{{ number_format($verifiedCount) }}</div>
                            <div class="text-xs text-zinc-500 mt-1">Email verified</div>
                        </div>
                    </div>

                    {{-- Theme label (Disabled/Static since we enforce Dark/Aceternity) --}}
                    <div class="mt-4 text-sm text-zinc-500">
                        Theme: <span class="font-medium text-zinc-300">Aceternity Dark</span>
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
