@props([
    'className' => '',
    'title' => '',
    'description' => '',
    'header' => null,
    'icon' => null,
])

<div class="row-span-1 rounded-xl group/bento hover:shadow-xl transition duration-200 shadow-input dark:shadow-none p-4 dark:bg-black dark:border-white/[0.2] bg-white border border-transparent justify-between flex flex-col space-y-4 {{ $className }}">
    
    @if($header)
        {{ $header }}
    @else
        <div class="flex flex-1 w-full h-full min-h-[6rem] rounded-xl bg-gradient-to-br from-neutral-200 to-neutral-100 dark:from-neutral-900 dark:to-neutral-800"></div>
    @endif

    <div class="group-hover/bento:translate-x-2 transition duration-200">
        @if($icon)
            <div class="mb-2 text-indigo-500">
                {{ $icon }}
            </div>
        @endif
        
        <div class="font-sans font-bold text-neutral-600 dark:text-neutral-200 mb-2 mt-2">
            {{ $title }}
        </div>
        
        <div class="font-sans font-normal text-neutral-600 text-xs dark:text-neutral-300">
            {{ $description }}
        </div>
    </div>
</div>
