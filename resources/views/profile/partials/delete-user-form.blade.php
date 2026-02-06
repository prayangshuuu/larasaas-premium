<section>
    <div class="bg-white shadow-sm sm:rounded-xl border border-red-100/50">
        <div class="px-4 py-5 sm:p-6">
            <header>
                <h2 class="text-base font-semibold leading-7 text-slate-900">Delete Account</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Once your account is deleted, all of its resources and data will be permanently removed. Before deleting your account, please download any data or information that you wish to retain.
                </p>
            </header>

            <div class="mt-6">
                 <button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 transition-colors">
                    Delete Account
                </button>
            </div>
            
            {{-- Modal implementation using standard Alpine + Tailwind approach (no daisyui modal) --}}
            <div x-data="{ show: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
                 x-show="show"
                 x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') show = true"
                 x-on:close-modal.window="show = false"
                 x-on:keydown.escape.window="show = false"
                 class="relative z-50"
                 style="display: none;">
                
                <div x-show="show" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div x-show="show" 
                             class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             @click.outside="show = false">
                             
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-slate-900" id="modal-title">Are you sure you want to delete your account?</h3>
                                    <div class="mt-2 text-sm text-slate-500">
                                        <p>Once your account is deleted, all of its resources and data will be permanently removed. Please enter your password to confirm you would like to permanently delete your account.</p>
                                    </div>
                                </div>
                            </div>

                            <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
                                @csrf
                                @method('delete')

                                <div>
                                    <label for="password" class="sr-only">Password</label>
                                    <input type="password" name="password" id="password" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6" placeholder="Password">
                                    @error('password', 'userDeletion')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Delete Account</button>
                                    <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
