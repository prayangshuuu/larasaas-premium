<section>
    {{-- Main card with modern styling --}}
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
        <div class="max-w-xl">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Profile Information') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __("Update your account's profile information, picture, email address, and 2FA settings.") }}
                </p>
            </header>

            <div class="mt-6 space-y-4">

                {{-- Profile Picture Form --}}
                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    @csrf
                    @method('patch')

                    <div class="w-1/3">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ __('Profile Picture') }}</h3>
                    </div>

                    <div class="w-1/3 flex justify-center">
                        {{-- This is the line that styles your image. --}}
                        {{-- The 'object-cover' class makes the image fill the circular space without distortion. This is the correct class to use. --}}
                        <img class="h-20 w-20 rounded-full object-cover" src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('images/default-avatar.png') }}" alt="{{ $user->name }}">
                    </div>

                    <div class="w-1/3 text-right">
                        <label for="profile_picture" class="cursor-pointer inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Update') }}
                        </label>
                        <input id="profile_picture" name="profile_picture" type="file" class="hidden" onchange="this.form.submit()">
                        <x-input-error class="mt-2 text-left" :messages="$errors->get('profile_picture')" />
                    </div>
                </form>

                {{-- Other form sections below... --}}
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <div class="w-2/3">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ __('Two-Factor Authentication (2FA)') }}</h3>
                        @if (auth()->user()->two_factor_secret)
                            <p class="mt-1 text-xs text-green-500">{{ __('2FA is currently ENABLED.') }}</p>
                        @else
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('2FA is currently DISABLED.') }}</p>
                        @endif
                    </div>
                    <div class="w-1/3 text-right">
                        @if (auth()->user()->two_factor_secret)
                            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                @csrf
                                @method('DELETE')
                                <x-secondary-button type="submit">{{ __('Disable 2FA') }}</x-secondary-button>
                            </form>
                        @else
                            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                @csrf
                                <x-primary-button type="submit">{{ __('Enable 2FA') }}</x-primary-button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <div class="w-1/3">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ __('Username') }}</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Your username cannot be changed.') }}</p>
                    </div>
                    <div class="w-2/3 text-left pl-4">
                        <span class="text-gray-700 dark:text-gray-300">{{ $user->username }}</span>
                    </div>
                </div>

                <div x-data="{ editing: {{ $errors->has('name') ? 'true' : 'false' }} }" class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="w-1/3 font-medium text-gray-900 dark:text-gray-100">{{ __('Name') }}</h3>
                    <div class="w-2/3">
                        <form method="post" action="{{ route('profile.update') }}" class="flex justify-between items-center">
                            @csrf
                            @method('patch')
                            <div class="flex-grow">
                                <span x-show="!editing" class="text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                                <div x-show="editing" x-cloak>
                                    <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <button type="button" x-show="!editing" @click="editing = true" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {{ __('Update') }}
                                </button>
                                <div x-show="editing" x-cloak class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                                    <button type="button" @click="editing = false" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">{{ __('Cancel') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div x-data="{ editing: {{ $errors->has('email') ? 'true' : 'false' }} }" class="p-4 flex justify-between items-center">
                    <h3 class="w-1/3 font-medium text-gray-900 dark:text-gray-100">{{ __('Email') }}</h3>
                    <div class="w-2/3">
                        <form method="post" action="{{ route('profile.update') }}" class="flex justify-between items-center">
                            @csrf
                            @method('patch')
                            <div class="flex-grow">
                                <span x-show="!editing" class="text-gray-700 dark:text-gray-300">{{ $user->email }}</span>
                                <div x-show="editing" x-cloak>
                                    <x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <button type="button" x-show="!editing" @click="editing = true" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {{ __('Update') }}
                                </button>
                                <div x-show="editing" x-cloak class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                                    <button type="button" @click="editing = false" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">{{ __('Cancel') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Email Verification Notice --}}
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="p-4 -mt-4">
                        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden"> @csrf </form>
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
