<x-guest-layout>
    <div class="bg-white dark:bg-gray-900 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Changelog</h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">Stay up to date with the latest improvements and fixes.</p>
            </div>
            <div class="mx-auto mt-16 max-w-2xl space-y-12">
                @forelse ($announcements as $announcement)
                    <article class="flex flex-col gap-8 lg:flex-row">
                        <div class="lg:w-1/4 lg:flex-none">
                            <time datetime="{{ $announcement->published_at->format('Y-m-d') }}" class="text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $announcement->published_at->format('F d, Y') }}</time>
                            <div class="mt-2">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset 
                                    @if($announcement->type === 'new') bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/20
                                    @elseif($announcement->type === 'improvement') bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/20
                                    @elseif($announcement->type === 'fix') bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/20
                                    @elseif($announcement->type === 'alert') bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/20
                                    @endif">
                                    {{ ucfirst($announcement->type) }}
                                </span>
                            </div>
                        </div>
                        <div class="lg:w-3/4 lg:flex-auto">
                            <h3 class="text-xl font-semibold leading-8 text-gray-900 dark:text-white">{{ $announcement->title }}</h3>
                            <div class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-300 prose dark:prose-invert">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-400">No updates yet.</p>
                @endforelse
                
                <div class="mt-8">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
