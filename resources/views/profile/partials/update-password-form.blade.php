<section>
    <div class="bg-zinc-900 shadow-xl rounded-xl border border-zinc-800">
        <div class="px-4 py-5 sm:p-6">
            <header>
                <h2 class="text-lg font-medium text-white">Update Password</h2>
                <p class="mt-1 text-sm text-zinc-400">Ensure your account is using a long, random password to stay secure.</p>
            </header>

            <div class="mt-6 border-t border-zinc-800"></div>

             <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('put')

                {{-- Current Password --}}
                <div>
                    <label for="update_password_current_password" class="block text-sm font-medium text-zinc-300 mb-1">Current Password</label>
                    <x-ui.input type="password" name="current_password" id="update_password_current_password" autocomplete="current-password" />
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div>
                    <label for="update_password_password" class="block text-sm font-medium text-zinc-300 mb-1">New Password</label>
                    <x-ui.input type="password" name="password" id="update_password_password" autocomplete="new-password" />
                    @error('password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="update_password_password_confirmation" class="block text-sm font-medium text-zinc-300 mb-1">Confirm Password</label>
                    <x-ui.input type="password" name="password_confirmation" id="update_password_password_confirmation" autocomplete="new-password" />
                     @error('password_confirmation', 'updatePassword')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">Save</button>

                    @if (session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-400">Saved.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
