@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
            Create Coupon
        </a>
    </div>

    <div class="bg-zinc-900 overflow-hidden shadow-xl sm:rounded-lg border border-zinc-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-700">
                <thead class="bg-zinc-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            Code
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            Discount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            Usage
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            Expires
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-zinc-900 divide-y divide-zinc-700">
                    @forelse($coupons as $coupon)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-white font-mono">{{ $coupon->code }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $coupon->type === 'percent' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    @if($coupon->type === 'percent')
                                        {{ $coupon->value }}% Off
                                    @else
                                        ${{ $coupon->value }} Off
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                {{ $coupon->times_used }} / {{ $coupon->max_uses ?? '∞' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-indigo-400 hover:text-indigo-300 mr-2">Edit</a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Are you sure? This will archive the promo code so it cannot be used again.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-400">Delete</button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 text-center">
                                No coupons found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="bg-zinc-800 px-4 py-3 border-t border-zinc-700">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
