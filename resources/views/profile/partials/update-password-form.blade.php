<section>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
        <div class="max-w-xl">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Update Password') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>
            </header>

            <form method="post" action="{{ route('password.update') }}" class="mt-6">
                @csrf
                @method('put')

                {{-- Each div below is a row with a bottom border, using flexbox for alignment. --}}

                <div class="py-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <label for="update_password_current_password" class="w-1/3 font-medium text-sm text-gray-700 dark:text-gray-300">
                        {{ __('Current Password') }}
                    </label>
                    <div class="w-2/3">
                        <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>
                </div>

                <div class="py-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <label for="update_password_password" class="w-1/3 font-medium text-sm text-gray-700 dark:text-gray-300">
                        {{ __('New Password') }}
                    </label>
                    <div class="w-2/3">
                        <x-text-input id="update_password_password" name="password" type="password" class="block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>
                </div>

                <div class="py-4 flex justify-between items-center">
                    <label for="update_password_password_confirmation" class="w-1/3 font-medium text-sm text-gray-700 dark:text-gray-300">
                        {{ __('Confirm Password') }}
                    </label>
                    <div class="w-2/3">
                        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-4">
                    @if (session('status') === 'password-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 dark:text-gray-400"
                        >{{ __('Saved.') }}</p>
                    @endif

                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</section>
