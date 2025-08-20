<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in as an ADMIN!") }}
                </div>
            </div>

            <!-- Admin-specific content starts here -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Site Statistics
                    </h3>
                    <div class="mt-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            Total Registered Users: <span class="font-bold text-xl">{{ $userCount }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Admin-specific content ends here -->

        </div>
    </div>
</x-app-layout>
