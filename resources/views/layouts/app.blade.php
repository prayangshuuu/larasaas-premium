{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IELTSBandBooster') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Check local storage for theme preference and apply 'dark' class
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full font-sans antialiased text-slate-900">
<div class="min-h-full">
    {{-- Top navigation --}}
    @include('layouts.navigation')

    {{-- Impersonation banner --}}
    @php
        use App\Models\Setting;
        use App\Models\User;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\Route as RouteFacade;

        /** @var \App\Models\User|null $authUser */
        $authUser             = Auth::user(); 
        $impersonationEnabled = Setting::bool('features.impersonation', false);
        $impersonatedById     = session('impersonated_by');
        $impersonating        = (bool) $impersonatedById;
        $mode                 = session('impersonation_mode', 'readonly');
        $showStop             = RouteFacade::has('admin.impersonate.stop');

        $isAdminImpersonator  = false;
        if ($impersonating) {
            $actor = User::query()->find($impersonatedById);
            if ($actor) {
                $isAdminImpersonator = method_exists($actor, 'isAdmin')
                    ? $actor->isAdmin()
                    : (bool) ($actor->is_admin ?? false);
            }
        }
    @endphp

    @if($impersonationEnabled && $impersonating && $isAdminImpersonator)
        <div class="bg-yellow-50 border-b border-yellow-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex items-center gap-3 text-sm text-yellow-800">
                    <svg class="w-5 h-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5zM10 16a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                    </svg>

                    <div class="flex-1 flex flex-wrap items-center gap-x-2">
                        <span class="font-semibold">{{ __('Impersonation mode') }}:</span>
                        <span>
                            {{ __('Browsing as') }} <span class="font-medium underline">{{ $authUser?->name }}</span>
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $mode === 'full' ? 'bg-red-50 text-red-700 ring-red-600/10' : 'bg-yellow-100 text-yellow-800 ring-yellow-600/20' }}">
                            {{ $mode === 'full' ? __('Full Access') : __('Read-only') }}
                        </span>
                    </div>

                    @if($showStop)
                        <form method="POST" action="{{ route('admin.impersonate.stop') }}">
                            @csrf
                            <button type="submit" class="rounded-md bg-yellow-100 px-2.5 py-1.5 text-sm font-semibold text-yellow-800 shadow-sm hover:bg-yellow-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-600">
                                {{ __('Stop') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Optional page header --}}
    @isset($header)
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- Page Content --}}
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </div>
    </main>
</div>

</body>
</html>
