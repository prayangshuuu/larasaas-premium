{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-base-content">Admin Dashboard</h1>
                <p class="text-sm text-base-content/70">
                    Overview & controls for administrators.
                </p>
            </div>
        </div>

        {{-- Overview card (stats + theme + quick state) --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Stats (DaisyUI) --}}
                    <div class="col-span-2">
                        <div class="stats stats-vertical sm:stats-horizontal bg-base-200/50 rounded-xl">
                            <div class="stat">
                                <div class="stat-title">Users</div>
                                <div class="stat-value text-primary">{{ number_format($userCount) }}</div>
                                <div class="stat-desc text-base-content/70">Total registered</div>
                            </div>

                            <div class="stat">
                                <div class="stat-title">Verified</div>
                                <div class="stat-value text-success">{{ number_format($verifiedCount) }}</div>
                                <div class="stat-desc text-base-content/70">Email verified</div>
                            </div>

                            <div class="stat">
                                <div class="stat-title">2FA</div>
                                <div class="stat-value">{{ number_format($twofaCount) }}</div>
                                <div class="stat-desc text-base-content/70">Enabled (TOTP)</div>
                            </div>
                        </div>

                        {{-- Theme label (live from localStorage) --}}
                        <div class="mt-4 text-sm text-base-content/80"
                             x-data="{ theme: (localStorage.getItem('theme') || 'nord') }"
                             x-init="setInterval(()=>{theme = localStorage.getItem('theme') || 'nord'}, 400)">
                            Theme: <span class="font-medium" x-text="theme === 'dim' ? 'Dim' : 'Nord'">Nord</span>
                        </div>
                    </div>

                    {{-- Quick actions --}}
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-ghost rounded-xl border border-base-300 h-11">
                            My Account
                        </a>
                        {{-- Add your real admin routes here when available --}}
                        <a href="#" class="btn btn-primary rounded-xl h-11">Admin Action</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="btn rounded-xl h-11 w-full
                                       bg-neutral text-neutral-content hover:opacity-95
                                       dark:bg-neutral-content dark:text-neutral dark:border-base-300">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent users table --}}
        <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-base-content text-lg">Recent Users</h2>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-base-content/70">Name</th>
                            <th class="text-base-content/70">Email</th>
                            <th class="text-base-content/70">Verified</th>
                            <th class="text-base-content/70">Joined</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($recentUsers as $u)
                            <tr>
                                <td class="font-medium">{{ $u->name }}</td>
                                <td class="text-base-content/80">{{ $u->email }}</td>
                                <td>
                                    @if($u->email_verified_at)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-warning">No</span>
                                    @endif
                                </td>
                                <td class="text-base-content/70">{{ $u->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-base-content/60 py-6">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection
