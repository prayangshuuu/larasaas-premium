<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-bold mb-4">My Invoices</h2>
                    <ul>
                        @foreach($invoices as $invoice)
                            <li class="mb-2">
                                Invoice #{{ $invoice->id }} - {{ $invoice->amount }} - 
                                <a href="{{ route('billing.invoices.show', $invoice) }}" class="text-blue-600 hover:underline">Download PDF</a>
                            </li>
                        @endforeach
                    </ul>
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
