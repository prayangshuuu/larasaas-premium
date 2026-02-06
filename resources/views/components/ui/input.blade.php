@props(['disabled' => false])

<div class="group relative rounded-lg p-[1px] bg-neutral-800 transition duration-500 focus-within:bg-gradient-to-r focus-within:from-indigo-500 focus-within:via-purple-500 focus-within:to-indigo-500">
    <input 
        {{ $disabled ? 'disabled' : '' }} 
        {!! $attributes->merge([
            'class' => 'relative flex h-10 w-full rounded-md border-none bg-neutral-950 px-3 py-2 text-sm text-neutral-300 placeholder:text-neutral-500 focus:outline-none focus:ring-0 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:cursor-not-allowed disabled:opacity-50'
        ]) !!}
    />
</div>
