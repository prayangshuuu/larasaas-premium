<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-bold mb-4">Manage Plans</h2>
                    <a href="#" class="mb-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">Create New Plan</a>
                    <table class="min-w-full mt-4">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Price</th>
                                <th class="px-4 py-2">Interval</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td class="border px-4 py-2">{{ $plan->name }}</td>
                                    <td class="border px-4 py-2">{{ $plan->price }} {{ $plan->currency }}</td>
                                    <td class="border px-4 py-2">{{ $plan->interval }}</td>
                                    <td class="border px-4 py-2">
                                        <a href="#" class="text-blue-600">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
