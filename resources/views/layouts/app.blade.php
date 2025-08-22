{{-- resources/views/layouts/app.blade.php --}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="nord">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IELTSBandBooster') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-base-100 text-base-content font-sans antialiased">
<div class="min-h-screen">
    {{-- Top navigation (DaisyUI + global theme toggle via window.toggleTheme) --}}
    @include('layouts.navigation')

    {{-- Impersonation banner (feature-flagged) --}}
    @if (config('features.impersonation', false) && session('impersonating'))
        <div class="bg-warning/15 border-b border-warning/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="alert bg-warning/10 text-warning-content rounded-xl border border-warning/30">
                    <span class="font-semibold">
                        {{ __('You are impersonating') }}
                        <span class="underline underline-offset-4">
                            {{ \Illuminate\Support\Facades\Auth::user()->name ?? __('this user') }}
                        </span>
                    </span>
                    <div class="flex-1"></div>
                    <form method="POST" action="{{ route('admin.impersonate.stop') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            {{ __('Stop impersonation') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Optional page header (DaisyUI-styled) --}}
    @isset($header)
        <header class="bg-base-100 border-b border-base-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- Page Content (supports both $slot and @section) --}}
    <main class="py-6 px-4 sm:px-6 lg:px-8">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>
</div>

{{-- No theme scripts here. Global helpers live in resources/js/app.js and are called via window.toggleTheme(dark). --}}
</body>
</html>
