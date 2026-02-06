<section>
    <div class="bg-white shadow-sm sm:rounded-xl border border-slate-200">
        <div class="px-4 py-5 sm:p-6">
            <header>
                <h2 class="text-base font-semibold leading-7 text-slate-900">Update Password</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">Ensure your account is using a long, random password to stay secure.</p>
            </header>

            <div class="mt-6 border-t border-slate-100"></div>

             <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('put')

                {{-- Current Password --}}
                <div>
                    <label for="update_password_current_password" class="block text-sm font-medium leading-6 text-slate-900">Current Password</label>
                    <div class="mt-2">
                        <input type="password" name="current_password" id="update_password_current_password" autocomplete="current-password"
                               class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div>
                    <label for="update_password_password" class="block text-sm font-medium leading-6 text-slate-900">New Password</label>
                    <div class="mt-2">
                        <input type="password" name="password" id="update_password_password" autocomplete="new-password"
                               class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="update_password_password_confirmation" class="block text-sm font-medium leading-6 text-slate-900">Confirm Password</label>
                    <div class="mt-2">
                        <input type="password" name="password_confirmation" id="update_password_password_confirmation" autocomplete="new-password"
                               class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                     @error('password_confirmation', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">Save</button>

                    @if (session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 dark:text-gray-400">Saved.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
