{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-base-content">Users</h1>
                <p class="text-sm text-base-content/70">Search, filter, and manage users. Bulk actions supported.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-xl">Create</a>
                <a href="{{ route('admin.users.export.csv') }}" class="btn btn-outline rounded-xl">Export CSV</a>
            </div>
        </div>

        {{-- Filters / Search --}}
        <div class="card bg-base-100 border border-base-300 shadow rounded-2xl">
            <div class="card-body p-4 sm:p-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email"
                           class="input input-bordered w-full" />
                    <select name="status" class="select select-bordered w-full">
                        <option value="">All Statuses</option>
                        <option value="verified" @selected(request('status')==='verified')>Verified</option>
                        <option value="unverified" @selected(request('status')==='unverified')>Unverified</option>
                        <option value="banned" @selected(request('status')==='banned')>Banned</option>
                        <option value="admin" @selected(request('status')==='admin')>Admins</option>
                    </select>
                    <div class="flex gap-2">
                        <select name="perPage" class="select select-bordered w-full">
                            @foreach([10,25,50,100] as $pp)
                                <option value="{{ $pp }}" @selected((int)request('perPage', 25)===$pp)>{{ $pp }} / page</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table + Bulk actions --}}
        <div class="card bg-base-100 border border-base-300 shadow rounded-2xl">
            <div class="card-body p-4 sm:p-6">
                <form method="POST" action="{{ route('admin.users.bulk') }}" x-data="bulkSelect()">
                    @csrf

                    {{-- Bulk bar --}}
                    <div class="flex flex-wrap items-center gap-2 justify-between">
                        <div class="flex items-center gap-2">
                            <label class="label cursor-pointer p-0 gap-2">
                                <span class="label-text">Select all</span>
                                <input type="checkbox" class="checkbox checkbox-sm" @click="toggleAll($event)">
                            </label>
                            <span class="text-sm text-base-content/70" x-text="selected + ' selected'">0 selected</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <select name="action" class="select select-bordered">
                                <option value="">Bulk action…</option>
                                <option value="ban">Ban</option>
                                <option value="unban">Unban</option>
                                <option value="promote">Promote to Admin</option>
                                <option value="demote">Demote to User</option>
                                <option value="delete">Delete</option>
                            </select>
                            <button type="submit" class="btn btn-outline"
                                    onclick="return confirm('Apply selected action to chosen users?')">
                                Apply
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-4">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>
                                    {{-- spacer for checkboxes --}}
                                </th>
                                <th class="text-base-content/70">Name</th>
                                <th class="text-base-content/70">Email</th>
                                <th class="text-base-content/70">Status</th>
                                <th class="text-base-content/70">Joined</th>
                                <th class="text-base-content/70 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $u)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox checkbox-sm"
                                               name="ids[]" value="{{ $u->id }}" @change="syncCount()">
                                    </td>
                                    <td class="font-medium">{{ $u->name }}</td>
                                    <td class="text-base-content/80">{{ $u->email }}</td>
                                    <td class="flex gap-2 items-center">
                                        @if($u->email_verified_at)
                                            <span class="badge badge-success">Verified</span>
                                        @else
                                            <span class="badge badge-warning">Unverified</span>
                                        @endif
                                        @if($u->is_admin)
                                            <span class="badge badge-primary">Admin</span>
                                        @endif
                                        @if($u->banned_at)
                                            <span class="badge badge-error">Banned</span>
                                        @endif
                                    </td>
                                    <td class="text-base-content/70">{{ $u->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-xs btn-ghost border border-base-300 rounded-xl">Edit</a>

                                            @if(!$u->banned_at)
                                                <form method="POST" action="{{ route('admin.users.ban', $u) }}">
                                                    @csrf
                                                    <button class="btn btn-xs btn-outline btn-error rounded-xl"
                                                            onclick="return confirm('Ban this user? They will not be able to login.')">
                                                        Ban
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.unban', $u) }}">
                                                    @csrf
                                                    <button class="btn btn-xs btn-outline rounded-xl">
                                                        Unban
                                                    </button>
                                                </form>
                                            @endif

                                            @if(!$u->is_admin)
                                                <form method="POST" action="{{ route('admin.users.promote', $u) }}">
                                                    @csrf
                                                    <button class="btn btn-xs btn-outline rounded-xl">Promote</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.demote', $u) }}">
                                                    @csrf
                                                    <button class="btn btn-xs btn-outline rounded-xl">Demote</button>
                                                </form>
                                            @endif

                                            {{-- Impersonate (feature-flagged) --}}
                                            @if(config('features.impersonation', false))
                                                <form method="POST" action="{{ route('admin.impersonate.start', $u) }}">
                                                    @csrf
                                                    <button class="btn btn-xs btn-neutral rounded-xl"
                                                            onclick="return confirm('Impersonate this user? You will act as them until you stop.')">
                                                        Impersonate
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-xs btn-outline btn-error rounded-xl"
                                                        onclick="return confirm('Permanently delete this user? This cannot be undone.')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-base-content/60 py-6">
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

