<section>
    <div class="bg-zinc-900 shadow-xl rounded-xl border border-red-500/20">
        <div class="px-4 py-5 sm:p-6">
            <header>
                <h2 class="text-lg font-medium text-white">Delete Account</h2>
                <p class="mt-1 text-sm text-zinc-400">
                    Once your account is deleted, all of its resources and data will be permanently removed.
                </p>
            </header>

            <div class="mt-6">
                 <button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
                    Delete Account
                </button>
            </div>
            
            {{-- Modal --}}
            <div x-data="{ show: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
                 x-show="show"
                 x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') show = true"
                 x-on:close-modal.window="show = false"
                 x-on:keydown.escape.window="show = false"
                 class="relative z-50">
                
                <div x-show="show" class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" style="display: none;"></div>

                <div x-show="show" class="fixed inset-0 z-10 w-screen overflow-y-auto" style="display: none;">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-xl bg-zinc-900 border border-zinc-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6"
                             @click.outside="show = false">
                             
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-500/10 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">Delete Account</h3>
                                    <div class="mt-2 text-sm text-zinc-400">
                                        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>

                            <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
                                @csrf
                                @method('delete')

                                <div>
                                    <label for="password" class="sr-only">Password</label>
                                    <x-ui.input type="password" name="password" id="password" placeholder="Password" />
                                    @error('password', 'userDeletion')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">Delete Account</button>
                                    <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-zinc-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 sm:mt-0 sm:w-auto">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
