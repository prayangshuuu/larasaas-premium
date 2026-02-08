<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Announcement') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="col-span-6 sm:col-span-4">
                <x-input-label for="title" value="{{ __('Title') }}" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $announcement->title)" required autofocus />
                <x-input-error for="title" class="mt-2" />
            </div>

            <!-- Content -->
            <div class="col-span-6 sm:col-span-4 mt-4">
                <x-input-label for="content" value="{{ __('Content') }}" />
                <textarea id="content" name="content" rows="6" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('content', $announcement->content) }}</textarea>
                <x-input-error for="content" class="mt-2" />
            </div>

            <!-- Type -->
            <div class="col-span-6 sm:col-span-4 mt-4">
                <x-input-label for="type" value="{{ __('Type') }}" />
                <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="new" {{ old('type', $announcement->type) == 'new' ? 'selected' : '' }}>New Feature</option>
                    <option value="improvement" {{ old('type', $announcement->type) == 'improvement' ? 'selected' : '' }}>Improvement</option>
                    <option value="fix" {{ old('type', $announcement->type) == 'fix' ? 'selected' : '' }}>Bug Fix</option>
                    <option value="alert" {{ old('type', $announcement->type) == 'alert' ? 'selected' : '' }}>Alert</option>
                </select>
                <x-input-error for="type" class="mt-2" />
            </div>

            <!-- Published At -->
            <div class="col-span-6 sm:col-span-4 mt-4">
                <x-input-label for="published_at" value="{{ __('Published At (Leave empty for Draft)') }}" />
                <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local" name="published_at" :value="old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '')" />
                <x-input-error for="published_at" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ml-4">
                    {{ __('Update') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
