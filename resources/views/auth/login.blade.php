<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Error Flash --}}
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-500/10 p-3 border border-red-500/20">
            <p class="text-sm text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email Address --}}
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-400 mb-1">Password</label>
            <x-ui.input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-zinc-700 bg-zinc-900 text-indigo-600 shadow-sm focus:ring-indigo-500/50" name="remember">
                <span class="ms-2 text-sm text-zinc-400">Remember me</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <div class="pt-2">
            <button class="relative inline-flex h-12 w-full overflow-hidden rounded-md p-[1px] focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-50">
                <span class="absolute inset-[-1000%] animate-[spin_2s_linear_infinite] bg-[conic-gradient(from_90deg_at_50%_50%,#E2CBFF_0%,#393BB2_50%,#E2CBFF_100%)]"></span>
                <span class="inline-flex h-full w-full cursor-pointer items-center justify-center rounded-md bg-zinc-950 px-3 py-1 text-sm font-medium text-white backdrop-blur-3xl hover:bg-zinc-900 transition-colors">
                    Log in
                </span>
            </button>
        </div>
    </form>

    {{-- Social Login Section --}}
    @include('auth.partials.social-login-buttons')
        
    <div class="text-center mt-6">
         <span class="text-xs text-zinc-500">Don't have an account?</span>
         <a href="{{ route('register') }}" class="text-xs font-semibold text-zinc-300 hover:text-white ml-1">Register</a>
    </div>
</x-guest-layout>
