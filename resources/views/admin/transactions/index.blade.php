@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Transactions</h1>
            <p class="text-sm text-zinc-400 mt-1">Manage manual payment verifications and view transaction history.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.transactions.index', ['status' => 'pending']) }}" class="inline-flex items-center justify-center rounded-lg bg-yellow-500/10 px-4 py-2 text-sm font-semibold text-yellow-500 ring-1 ring-inset ring-yellow-500/20 hover:bg-yellow-500/20 transition-all">
                Pending Verification
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center justify-center rounded-lg bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 transition-all border border-zinc-700">
                All Transactions
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-emerald-500/10 p-4 border border-emerald-500/20">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 text-sm text-emerald-400">{{ session('success') }}</div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="rounded-md bg-red-500/10 p-4 border border-red-500/20">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                         <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 text-sm text-red-400">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <div class="bg-zinc-900 border border-zinc-800 rounded-xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-800 text-left">
                <thead class="bg-zinc-950/50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">Method & Details</th>
                        <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-xs font-medium text-zinc-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 bg-zinc-900">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-8 w-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-bold text-white">
                                        {{ substr($transaction->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">{{ $transaction->user->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $transaction->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                {{ $transaction->amount }} <span class="text-zinc-500 text-xs uppercase">{{ $transaction->currency }}</span>
                                @if($transaction->discount_amount > 0)
                                    <div class="mt-1 space-y-0.5">
                                        <div class="text-xs text-zinc-500">
                                            Subtotal: <span class="line-through">{{ $transaction->subtotal }}</span>
                                        </div>
                                        <div class="text-xs text-emerald-400">
                                            -{{ $transaction->discount_amount }} discount
                                        </div>
                                        @if($transaction->coupon)
                                            <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-1.5 py-0.5 text-[10px] font-medium text-emerald-400 ring-1 ring-inset ring-emerald-400/20">
                                                {{ $transaction->coupon->code }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-300">
                                    @if($transaction->payment_method === 'bkash_manual')
                                        <span class="inline-flex items-center gap-1 text-pink-400">
                                            Bkash
                                        </span>
                                    @else
                                        <span class="text-indigo-400">{{ ucfirst($transaction->payment_method) }}</span>
                                    @endif
                                </div>
                                @if($transaction->payment_method === 'bkash_manual')
                                    <div class="text-xs text-zinc-500 mt-1">
                                        Sender: <span class="text-zinc-300">{{ $transaction->sender_number }}</span><br>
                                        TrxID: <span class="text-zinc-300 font-mono">{{ $transaction->transaction_id }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-400">
                                {{ $transaction->created_at->format('M d, Y H:i') }}
                                @if($transaction->payment_date)
                                    <div class="text-xs text-zinc-600">Paid: {{ $transaction->payment_date->format('M d') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->status === 'paid')
                                    <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-2 py-1 text-xs font-medium text-emerald-400 ring-1 ring-inset ring-emerald-400/20">Paid</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="inline-flex items-center rounded-full bg-yellow-400/10 px-2 py-1 text-xs font-medium text-yellow-500 ring-1 ring-inset ring-yellow-400/20">Pending</span>
                                @elseif($transaction->status === 'rejected')
                                    <span class="inline-flex items-center rounded-full bg-red-400/10 px-2 py-1 text-xs font-medium text-red-500 ring-1 ring-inset ring-red-400/20">Rejected</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-zinc-400/10 px-2 py-1 text-xs font-medium text-zinc-400 ring-1 ring-inset ring-zinc-400/20">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($transaction->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('transactions.approve', $transaction) }}" method="POST" onsubmit="return confirm('Approve this transaction? This will activate the subscription.')">
                                            @csrf
                                            <button type="submit" class="text-emerald-500 hover:text-emerald-400 transition-colors">Approve</button>
                                        </form>
                                        <span class="text-zinc-700">|</span>
                                        <form action="{{ route('transactions.reject', $transaction) }}" method="POST" onsubmit="return confirm('Reject this transaction?')">
                                            @csrf
                                            <button type="submit" class="text-red-500 hover:text-red-400 transition-colors">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-zinc-600 italic">No actions</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-zinc-500">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="border-t border-zinc-800 bg-zinc-950/50 px-6 py-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
