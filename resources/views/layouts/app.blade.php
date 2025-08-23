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

    {{-- Impersonation banner (DB flag + only when an ADMIN is impersonating) --}}
    @php
        use App\Models\Setting;
        use App\Models\User;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\Route as RouteFacade;

        /** @var \App\Models\User|null $authUser */
        $authUser             = Auth::user(); // current "effective" user (impersonated)
        $impersonationEnabled = Setting::bool('features.impersonation', false);
        $impersonatedById     = session('impersonated_by');               // set by your ImpersonationController@start
        $impersonating        = (bool) $impersonatedById;
        $mode                 = session('impersonation_mode', 'readonly'); // readonly|full
        $showStop             = RouteFacade::has('admin.impersonate.stop');

        // Only show the banner if the impersonator is actually an admin
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
        <div class="bg-warning/15 border-b border-warning/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="alert bg-warning/10 text-warning-content rounded-xl border border-warning/30">
                    <div class="flex items-center gap-2">
                        {{-- shield icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253l7.5 4.327v5.62A2.75 2.75 0 0116.75 19H7.25A2.75 2.75 0 014.5 16.2v-5.62L12 6.253z"/>
                        </svg>

                        <span class="font-semibold">
                            {{ __('Impersonation mode') }}
                        </span>

                        <span class="opacity-80">
                            {{ __('Admin is browsing as') }}
                            <span class="underline underline-offset-4">
                                {{ $authUser?->name ?? __('this user') }}
                            </span>
                            <span class="ml-2 badge badge-sm {{ $mode === 'full' ? 'badge-error' : 'badge-ghost' }}">
                                {{ $mode === 'full' ? __('Full access') : __('Read-only') }}
                            </span>
                        </span>
                    </div>

                    <div class="flex-1"></div>

                    @if($showStop)
                        <form method="POST" action="{{ route('admin.impersonate.stop') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                {{ __('Stop impersonation') }}
                            </button>
                        </form>
                    @endif
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
