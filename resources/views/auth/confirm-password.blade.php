<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-white mb-2">Confirm Password</h2>
        <p class="text-sm text-zinc-400">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-300 mb-1">Password</label>
            <x-ui.input type="password" name="password" id="password" required autocomplete="current-password" autofocus placeholder="••••••••" />
             @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">
                Confirm
            </button>
        </div>
    </form>
</x-guest-layout>
