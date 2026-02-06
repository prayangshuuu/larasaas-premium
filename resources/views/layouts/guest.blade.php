{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IELTSBandBooster') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full font-sans antialiased text-slate-900">

<div class="flex min-h-full">
    {{-- Left Side: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 lg:flex-col lg:justify-between lg:bg-primary-600 lg:p-12 xl:p-16 relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white rounded-full blur-3xl mix-blend-overlay"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-white rounded-full blur-3xl mix-blend-overlay"></div>
        </div>

        <div class="relative z-10">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg border border-white/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-xl font-bold text-white tracking-tight">IELTSBandBooster</span>
            </a>
        </div>

        <div class="relative z-10 mt-16 max-w-lg">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl mb-6">
                Achieve Your Target Band Score
            </h1>
            <p class="text-lg text-primary-100 leading-relaxed mb-8">
                Join thousands of students who have verified their skills and achieved their dreams with our AI-powered platform.
            </p>
            
            <ul class="space-y-4 text-primary-100">
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Personalized AI feedback loops</span>
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Real-time mock test simulations</span>
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Expert-verified study materials</span>
                </li>
            </ul>
        </div>

        <div class="relative z-10 mt-16 text-sm text-primary-200">
            &copy; {{ date('Y') }} IELTSBandBooster. All rights reserved.
        </div>
    </div>

    {{-- Right Side: Form --}}
    <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:bg-white lg:flex-none lg:px-20 xl:px-24 w-full lg:w-1/2">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            {{-- Mobile Logo --}}
            <div class="lg:hidden text-center mb-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                    <div class="p-2 bg-primary-600 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <span class="text-xl font-bold text-slate-900 tracking-tight">IELTSBandBooster</span>
                </a>
            </div>

            {{-- Errors --}}
            @if($errors->any())
                <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul role="list" class="list-disc space-y-1 pl-5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main Content --}}
            @yield('content')
        </div>
    </div>
</div>

</body>
</html>
