<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-zinc-400 mb-1">Name</label>
            <x-ui.input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email Address --}}
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-400 mb-1">Password</label>
            <x-ui.input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-zinc-400 mb-1">Confirm Password</label>
            <x-ui.input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button class="relative inline-flex h-12 w-full overflow-hidden rounded-md p-[1px] focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-50">
                <span class="absolute inset-[-1000%] animate-[spin_2s_linear_infinite] bg-[conic-gradient(from_90deg_at_50%_50%,#E2CBFF_0%,#393BB2_50%,#E2CBFF_100%)]"></span>
                <span class="inline-flex h-full w-full cursor-pointer items-center justify-center rounded-md bg-zinc-950 px-3 py-1 text-sm font-medium text-white backdrop-blur-3xl hover:bg-zinc-900 transition-colors">
                    Register
                </span>
            </button>
        </div>

        <div class="text-center mt-4">
             <span class="text-xs text-zinc-500">Already registered?</span>
             <a href="{{ route('login') }}" class="text-xs font-semibold text-zinc-300 hover:text-white ml-1">Log in</a>
        </div>
    </form>
</x-guest-layout>
