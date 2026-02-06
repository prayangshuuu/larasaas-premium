{{-- resources/views/admin/audit/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Audit Log</h1>
                <p class="text-sm text-zinc-400">Chronological record of important actions.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">Back to Admin</a>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-zinc-800">
                {{-- Filters --}}
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <x-ui.input type="text" name="q" value="{{ request('q') }}" placeholder="Search action/desc/actor" />
                    <x-ui.input type="text" name="actor" value="{{ request('actor') }}" placeholder="Actor email or id" />
                    <x-ui.input type="text" name="target" value="{{ request('target') }}" placeholder="Target (class:id)" />
                    
                    <div class="flex gap-2">
                        <select name="perPage" class="block w-full rounded-md border-0 bg-zinc-950 py-2.5 px-3 text-zinc-300 shadow-sm ring-1 ring-inset ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @foreach([25,50,100] as $pp)
                                <option value="{{ $pp }}" @selected((int)request('perPage', 25)===$pp)>{{ $pp }} / page</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Apply</button>
                    </div>
                </form>

                <div class="mt-6 -mx-4 sm:-mx-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-800 text-left">
                        <thead>
                        <tr class="text-zinc-400 text-xs uppercase tracking-wider bg-zinc-900/50">
                            <th class="px-6 py-3 font-medium">When</th>
                            <th class="px-6 py-3 font-medium">Actor</th>
                            <th class="px-6 py-3 font-medium">Action</th>
                            <th class="px-6 py-3 font-medium">Target</th>
                            <th class="px-6 py-3 font-medium">IP</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                        @forelse($logs as $log)
                            <tr class="hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-zinc-400">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-300">
                                    @if($log->actor)
                                        {{ $log->actor->name }} <span class="text-zinc-600">(#{{ $log->actor_id }})</span>
                                    @else
                                        <span class="text-zinc-600">System</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-white">{{ $log->action }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-400">
                                    @if($log->target_type && $log->target_id)
                                        <span class="font-mono text-xs">{{ class_basename($log->target_type) }}</span> #{{ $log->target_id }}
                                    @else
                                        <span class="text-zinc-600">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-500">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-zinc-500 py-12">
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
