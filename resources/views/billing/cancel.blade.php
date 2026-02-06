<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-bold mb-4">Checkout Cancelled</h2>
                    <p>The checkout process was cancelled. No charges were made.</p>
                    <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-blue-600 hover:underline">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
