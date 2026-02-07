{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-500/10 rounded-lg border border-indigo-500/20">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white">Users</h1>
                    <p class="text-sm text-zinc-400">Search, filter, and manage users. Bulk actions supported.</p>
                </div>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.users.create') }}" class="inline-flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Create User
                </a>
                <a href="{{ route('admin.users.export.csv') }}" class="inline-flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export CSV
                </a>
            </div>
        </div>

        {{-- Filters / Search --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-4 sm:p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email" />
                
                <div class="relative">
                    <select name="status" class="block w-full rounded-md border-0 bg-zinc-950 py-2.5 px-3 text-zinc-300 shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">All Statuses</option>
                        <option value="verified" @selected(request('status')==='verified')>Verified</option>
                        <option value="unverified" @selected(request('status')==='unverified')>Unverified</option>
                        <option value="banned" @selected(request('status')==='banned')>Banned</option>
                        <option value="admin" @selected(request('status')==='admin')>Admins</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <select name="perPage" class="block w-full rounded-md border-0 bg-zinc-950 py-2.5 px-3 text-zinc-300 shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @foreach([10,25,50,100] as $pp)
                            <option value="{{ $pp }}" @selected((int)request('perPage', 25)===$pp)>{{ $pp }} / page</option>
                        @endforeach
                    </select>
                    <button class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Apply</button>
                </div>
            </form>
        </div>

        {{-- Table + Bulk actions --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-zinc-800">
                <form method="POST" action="{{ route('admin.users.bulk') }}" x-data="bulkSelect()">
                    @csrf

                    {{-- Bulk bar --}}
                    <div class="flex flex-wrap items-center gap-4 justify-between">
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50" @click="toggleAll($event)">
                                <span class="text-sm font-medium text-white">Select all</span>
                            </label>
                            <span class="text-sm text-zinc-500" x-text="selected + ' selected'">0 selected</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <select name="action" class="block rounded-md border-0 bg-zinc-950 py-1.5 px-3 text-sm text-zinc-300 shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                <option value="">Bulk action…</option>
                                <option value="ban">Ban</option>
                                <option value="unban">Unban</option>
                                <option value="promote">Promote to Admin</option>
                                <option value="demote">Demote to User</option>
                                <option value="delete">Delete</option>
                            </select>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-zinc-800 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors"
                                    onclick="return confirm('Apply selected action to chosen users?')">
                                Apply
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 -mx-4 sm:-mx-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-800 text-left">
                            <thead>
                            <tr class="text-zinc-400 text-xs uppercase tracking-wider">
                                <th class="pl-4 sm:pl-6 py-3 w-10"></th>
                                <th class="px-3 py-3 font-medium">Name</th>
                                <th class="px-3 py-3 font-medium">Email</th>
                                <th class="px-3 py-3 font-medium">Status</th>
                                <th class="px-3 py-3 font-medium">Joined</th>
                                <th class="px-3 py-3 font-medium text-right pr-4 sm:pr-6">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800">
                            @forelse($users as $u)
                                <tr class="hover:bg-zinc-800/50 transition-colors">
                                    <td class="pl-4 sm:pl-6 py-4">
                                        <input type="checkbox" class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50"
                                               name="ids[]" value="{{ $u->id }}" @change="syncCount()">
                                    </td>
                                    <td class="px-3 py-4 text-sm font-medium text-white">{{ $u->name }}</td>
                                    <td class="px-3 py-4 text-sm text-zinc-400">{{ $u->email }}</td>
                                    <td class="px-3 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            @if($u->email_verified_at)
                                                <span class="inline-flex items-center rounded-md bg-green-400/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-400/20">Verified</span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-yellow-400/10 px-2 py-1 text-xs font-medium text-yellow-500 ring-1 ring-inset ring-yellow-400/20">Unverified</span>
                                            @endif
                                            @if($u->is_admin)
                                                <span class="inline-flex items-center rounded-md bg-indigo-400/10 px-2 py-1 text-xs font-medium text-indigo-400 ring-1 ring-inset ring-indigo-400/30">Admin</span>
                                            @endif
                                            @if($u->banned_at)
                                                <span class="inline-flex items-center rounded-md bg-red-400/10 px-2 py-1 text-xs font-medium text-red-500 ring-1 ring-inset ring-red-400/20">Banned</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-500">{{ $u->created_at->format('d M Y') }}</td>
                                    <td class="px-3 py-4 text-sm text-right pr-4 sm:pr-6">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.users.edit', $u) }}" class="text-zinc-400 hover:text-white transition-colors">Edit</a>
                                            <span class="text-zinc-700">|</span>

                                            @if(!$u->banned_at)
                                                <form method="POST" action="{{ route('admin.users.ban', $u) }}" class="inline">
                                                    @csrf
                                                    <button class="text-red-500 hover:text-red-400 transition-colors"
                                                            onclick="return confirm('Ban this user? They will not be able to login.')">
                                                        Ban
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.unban', $u) }}" class="inline">
                                                    @csrf
                                                    <button class="text-green-500 hover:text-green-400 transition-colors">
                                                        Unban
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Impersonate --}}
                                            @if(\App\Helpers\Feature::enabled('impersonation'))
                                                <span class="text-zinc-700">|</span>
                                                <form method="POST" action="{{ route('admin.impersonate.start', $u) }}" class="inline">
                                                    @csrf
                                                    <button class="text-indigo-400 hover:text-indigo-300 transition-colors"
                                                            onclick="return confirm('Impersonate this user? You will act as them until you stop.')">
                                                        Impersonate
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-zinc-500 py-12">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Alpine helper for bulk selection --}}
    <script>
        function bulkSelect(){
            return {
                selected: 0,
                toggleAll(e){
                    const checked = e.target.checked;
                    document.querySelectorAll('input[name="ids[]"]').forEach(cb => { cb.checked = checked; });
                    this.selected = this.countSelected();
                },
                countSelected(){
                    return document.querySelectorAll('input[name="ids[]"]:checked').length;
                },
                syncCount(){
                    this.selected = this.countSelected();
                }
            };
        }
    </script>
@endsection

