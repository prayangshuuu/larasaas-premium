@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">
        {{-- Header --}}
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Billing History</h1>
            <p class="text-sm text-zinc-400 mt-1">View and download your past invoices.</p>
        </div>

        {{-- Invoices Card --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-800 text-left">
                    <thead>
                        <tr class="bg-zinc-900/50 text-zinc-400 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-medium">Date</th>
                            <th class="px-6 py-4 font-medium">Invoice ID</th>
                            <th class="px-6 py-4 font-medium">Amount</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800 bg-zinc-900">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-zinc-800/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                    {{ $invoice->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-zinc-500">
                                    #{{ $invoice->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                    ${{ number_format($invoice->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($invoice->status == 'paid')
                                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-400 border border-emerald-500/20">
                                            Paid
                                        </span>
                                    @elseif($invoice->status == 'pending')
                                        <span class="inline-flex items-center rounded-full bg-yellow-500/10 px-2.5 py-0.5 text-xs font-medium text-yellow-400 border border-yellow-500/20">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-500/10 px-2.5 py-0.5 text-xs font-medium text-red-400 border border-red-500/20">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('billing.invoices.show', $invoice) }}" target="_blank" class="inline-flex items-center text-indigo-400 hover:text-indigo-300 transition-colors font-medium">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-zinc-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-10 w-10 text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p>No invoices found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($invoices->hasPages())
                <div class="border-t border-zinc-800 bg-zinc-900/50 px-6 py-4">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
