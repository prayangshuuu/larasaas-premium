{{-- resources/views/admin/audit/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-base-content">Audit Log</h1>
                <p class="text-sm text-base-content/70">Chronological record of important actions.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost border border-base-300 rounded-xl">Back to Admin</a>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow rounded-2xl">
            <div class="card-body p-4 sm:p-6">
                {{-- Filters --}}
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search action/desc/actor"
                           class="input input-bordered w-full" />
                    <input type="text" name="actor" value="{{ request('actor') }}" placeholder="Actor email or id"
                           class="input input-bordered w-full" />
                    <input type="text" name="target" value="{{ request('target') }}" placeholder="Target (class:id)"
                           class="input input-bordered w-full" />
                    <div class="flex gap-2">
                        <select name="perPage" class="select select-bordered w-full">
                            @foreach([25,50,100] as $pp)
                                <option value="{{ $pp }}" @selected((int)request('perPage', 25)===$pp)>{{ $pp }} / page</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary">Apply</button>
                    </div>
                </form>

                <div class="overflow-x-auto mt-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-base-content/70">When</th>
                            <th class="text-base-content/70">Actor</th>
                            <th class="text-base-content/70">Action</th>
                            <th class="text-base-content/70">Target</th>
                            <th class="text-base-content/70">IP</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-base-content/80">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="text-base-content/80">
                                    @if($log->actor)
                                        {{ $log->actor->name }} <span class="text-base-content/50">(#{{ $log->actor_id }})</span>
                                    @else
                                        <span class="text-base-content/50">System</span>
                                    @endif
                                </td>
                                <td class="font-medium">{{ $log->action }}</td>
                                <td class="text-base-content/80">
                                    @if($log->target_type && $log->target_id)
                                        {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                                    @else
                                        <span class="text-base-content/50">—</span>
                                    @endif
                                </td>
                                <td class="text-base-content/80">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-base-content/60 py-6">
                                    No audit entries found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
