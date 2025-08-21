<section>
    <div class="card bg-base-100 border border-base-300 shadow-md rounded-2xl">
        <div class="card-body p-6 sm:p-8">
            @php
                $hasDeletionError = $errors->userDeletion->isNotEmpty();
            @endphp

            {{-- HEADER: title + description (left) with action button (right) --}}
            <header class="grid grid-cols-1 sm:grid-cols-[1fr_auto] items-center gap-4">
                <div>
                    <h2 class="card-title text-base-content text-lg">
                        {{ __('Delete Account') }}
                    </h2>
                    <p class="mt-1 text-sm text-base-content/70">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently removed. Download anything you wish to keep before continuing.') }}
                    </p>
                </div>

                <div class="hidden sm:flex items-center justify-end">
                    <button type="button" class="btn btn-error h-12"
                            onclick="document.getElementById('confirmDeleteDialog').showModal()">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </header>

            <div class="sm:hidden mt-4">
                <button type="button" class="btn btn-error w-full h-12"
                        onclick="document.getElementById('confirmDeleteDialog').showModal()">
                    {{ __('Delete Account') }}
                </button>
            </div>

            {{-- Modal (DaisyUI) --}}
            <dialog id="confirmDeleteDialog" class="modal" @if($hasDeletionError) open @endif>
                <div class="modal-box">
                    <h3 class="font-semibold text-base-content text-lg">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h3>
                    <p class="mt-1 text-sm text-base-content/70">
                        {{ __('This action is permanent. Please enter your password to confirm you wish to permanently delete your account.') }}
                    </p>

                    <form method="post" action="{{ route('profile.destroy') }}" class="mt-6 space-y-4"
                          x-data="{ showPass:false }">
                        @csrf
                        @method('delete')

                        {{-- Password row: label w-36 + field h-12 with Show/Hide attached (matches other forms) --}}
                        <div class="flex items-center gap-3">
                            <label for="delete_account_password"
                                   class="w-36 shrink-0 text-sm font-medium text-base-content">
                                {{ __('Password') }}
                            </label>
                            <div class="join w-full">
                                <input :type="showPass ? 'text' : 'password'"
                                       id="delete_account_password"
                                       name="password"
                                       class="join-item input input-bordered h-12 w-full"
                                       placeholder="{{ __('Password') }}"
                                       autocomplete="current-password"
                                       @if($hasDeletionError) autofocus @endif />
                                <button type="button"
                                        class="join-item btn btn-ghost h-12"
                                        @click="showPass = !showPass"
                                        :aria-pressed="showPass.toString()">
                                    <span x-show="!showPass">{{ __('Show') }}</span>
                                    <span x-show="showPass">{{ __('Hide') }}</span>
                                </button>
                            </div>
                        </div>
                        @error('password', 'userDeletion')
                        <p class="text-error text-sm">{{ $message }}</p>
                        @enderror

                        <div class="modal-action">
                            <button type="button" class="btn h-12"
                                    onclick="document.getElementById('confirmDeleteDialog').close()">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-error h-12">
                                {{ __('Delete Account') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Click outside to close --}}
                <form method="dialog" class="modal-backdrop">
                    <button>{{ __('Close') }}</button>
                </form>
            </dialog>
        </div>
    </div>
</section>
