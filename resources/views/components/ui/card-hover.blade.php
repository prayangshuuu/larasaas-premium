@props(['items'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 py-10">
    @foreach ($items as $item)
        <a 
            href="{{ $item['link'] ?? '#' }}"
            class="relative group block p-2 h-full w-full"
            x-data="{ hovered: false }"
            @mouseenter="hovered = true"
            @mouseleave="hovered = false"
        >
            <span 
                class="absolute inset-0 h-full w-full bg-slate-800/[0.8] block rounded-3xl"
                x-show="hovered"
                x-transition:enter="transition duration-150 ease-out"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition duration-150 ease-out"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                layoutId="hoverBackground"
            ></span>
            
            <div class="rounded-2xl h-full w-full p-4 overflow-hidden bg-black border border-transparent dark:border-white/[0.2] group-hover:border-slate-700 relative z-20 transition-colors duration-500">
                <div class="relative z-50">
                    <div class="p-4">
                        <h4 class="text-zinc-100 font-bold tracking-wide mt-4">
                            {{ $item['title'] }}
                        </h4>
                        <p class="mt-8 text-zinc-400 tracking-wide leading-relaxed text-sm">
                            {{ $item['description'] }}
                        </p>
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>
