<section>
    <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
        <div class="card-body p-6 sm:p-8">

            {{-- HEADER (no icons/avatars here) --}}
            <header class="grid grid-cols-1 items-center gap-4">
                <div>
                    <h2 class="card-title text-base-content text-lg">
                        {{ __('Update Password') }}
                    </h2>
                    <p class="mt-1 text-sm text-base-content/70">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                    </p>
                </div>
            </header>

            <div class="divider my-4"></div>

            {{-- FORM: exact row layout as profile info (icon – label – field), uniform h-12, attached Show/Hide --}}
            <form method="post" action="{{ route('password.update') }}" class="space-y-4"
                  x-data="{ showCur:false, showNew:false, showConf:false }">
                @csrf
                @method('put')

                {{-- Current Password --}}
                <div class="flex items-center gap-3">
                    <span class="btn btn-ghost btn-square pointer-events-none" aria-hidden="true">
                        {{-- lock icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 10V8a4 4 0 00-8 0v2M8 10h8a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2z"/>
                        </svg>
                    </span>
                    <label for="update_password_current_password"
                           class="w-36 shrink-0 text-sm font-medium text-base-content">
                        {{ __('Current Password') }}
                    </label>
                    <div class="join w-full">
                        <input :type="showCur ? 'text' : 'password'"
                               id="update_password_current_password"
                               name="current_password"
                               class="join-item input input-bordered h-12 w-full"
                               autocomplete="current-password" />
                        <button type="button"
                                class="join-item btn btn-ghost h-12"
                                @click="showCur = !showCur"
                                :aria-pressed="showCur.toString()">
                            <span x-show="!showCur">{{ __('Show') }}</span>
                            <span x-show="showCur">{{ __('Hide') }}</span>
                        </button>
                    </div>
                </div>
                @error('current_password', 'updatePassword')
                <p class="text-error text-sm">{{ $message }}</p>
                @enderror

                {{-- New Password --}}
                <div class="flex items-center gap-3">
                    <span class="btn btn-ghost btn-square pointer-events-none" aria-hidden="true">
                        {{-- lock icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 10V8a4 4 0 00-8 0v2M8 10h8a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2z"/>
                        </svg>
                    </span>
                    <label for="update_password_password"
                           class="w-36 shrink-0 text-sm font-medium text-base-content">
                        {{ __('New Password') }}
                    </label>
                    <div class="join w-full">
                        <input :type="showNew ? 'text' : 'password'"
                               id="update_password_password"
                               name="password"
                               class="join-item input input-bordered h-12 w-full"
                               autocomplete="new-password" />
                        <button type="button"
                                class="join-item btn btn-ghost h-12"
                                @click="showNew = !showNew"
                                :aria-pressed="showNew.toString()">
                            <span x-show="!showNew">{{ __('Show') }}</span>
                            <span x-show="showNew">{{ __('Hide') }}</span>
                        </button>
                    </div>
                </div>
                @error('password', 'updatePassword')
                <p class="text-error text-sm">{{ $message }}</p>
                @enderror

                {{-- Confirm Password --}}
                <div class="flex items-center gap-3">
                    <span class="btn btn-ghost btn-square pointer-events-none" aria-hidden="true">
                        {{-- lock icon --}}
                        <svg class="w-5 h-5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 10V8a4 4 0 00-8 0v2M8 10h8a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2z"/>
                        </svg>
                    </span>
                    <label for="update_password_password_confirmation"
                           class="w-36 shrink-0 text-sm font-medium text-base-content">
                        {{ __('Confirm Password') }}
                    </label>
                    <div class="join w-full">
                        <input :type="showConf ? 'text' : 'password'"
                               id="update_password_password_confirmation"
                               name="password_confirmation"
                               class="join-item input input-bordered h-12 w-full"
                               autocomplete="new-password" />
                        <button type="button"
                                class="join-item btn btn-ghost h-12"
                                @click="showConf = !showConf"
                                :aria-pressed="showConf.toString()">
                            <span x-show="!showConf">{{ __('Show') }}</span>
                            <span x-show="showConf">{{ __('Hide') }}</span>
                        </button>
                    </div>
                </div>
                @error('password_confirmation', 'updatePassword')
                <p class="text-error text-sm">{{ $message }}</p>
                @enderror

                {{-- Actions (identical to profile form) --}}
                <div class="card-actions justify-end pt-2">
                    <button type="submit" class="btn btn-primary h-12 min-w-40">
                        {{ __('Save changes') }}
                    </button>

                    @if (session('status') === 'password-updated')
                        <span
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="badge badge-success badge-outline">
                            {{ __('Saved') }}
                        </span>
                    @endif
                </div>
            </form>

        </div>
    </div>
</section>
