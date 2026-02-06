@props(['class' => ''])

<div class="h-full w-full dark:bg-black bg-white dark:bg-grid-white/[0.05] bg-grid-black/[0.2] relative flex items-center justify-center {{ $class }}">
    {{-- Radial gradient for the container to give a faded look --}}
    <div class="absolute pointer-events-none inset-0 flex items-center justify-center dark:bg-black bg-white [mask-image:radial-gradient(ellipse_at_center,transparent_20%,black)]"></div>

    <div class="relative z-20">
        {{ $slot ?? '' }}
    </div>
</div>
