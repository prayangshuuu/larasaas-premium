@props(['className' => ''])

<div class="grid md:auto-rows-[18rem] grid-cols-1 md:grid-cols-3 gap-4 max-w-7xl mx-auto {{ $className }}">
    {{ $slot }}
</div>
