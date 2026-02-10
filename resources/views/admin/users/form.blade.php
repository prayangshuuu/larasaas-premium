{{-- resources/views/admin/users/form.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        /** @var \App\Models\User|null $managedUser */
        $isEdit = isset($managedUser);
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">
                    {{ $isEdit ? 'Edit User' : 'Create User' }}
                </h1>
                <p class="text-sm text-zinc-400">
                    {{ $isEdit ? 'Update account details and privileges.' : 'Add a new account with the correct privileges.' }}
                </p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 ring-1 ring-inset ring-zinc-700 transition-colors">Back</a>
        </div>

        {{-- Card --}}
        <div class="bg-zinc-900 border border-zinc-800 shadow-xl rounded-xl p-6 sm:p-8">
            @if($isEdit)
                <form id="generate-password-form" action="{{ route('admin.users.send-password', $managedUser) }}" method="POST" class="hidden" onsubmit="return confirm('Are you sure? This will overwrite the current password and email a new one to the user.');">
                    @csrf
                </form>
            @endif

            <form method="POST"
                  action="{{ $isEdit ? route('admin.users.update', $managedUser) : route('admin.users.store') }}"
                  class="space-y-6"
                  x-data="{
                        isAdmin: {{ old('is_admin', ($isEdit && $managedUser->is_admin) ? 'true' : 'false') }},
                        isVerified: {{ old('verified', ($isEdit && $managedUser->email_verified_at) ? 'true' : 'false') }},
                        isBanned: {{ old('banned', ($isEdit && $managedUser->banned_at) ? 'true' : 'false') }},
                        toggleBanned() {
                            this.isBanned = !this.isBanned;
                            if (this.isBanned) {
                                this.isAdmin = false;
                                this.isVerified = false;
                            }
                        }
                     }">
                @csrf
                @if($isEdit) @method('PUT') @endif

                {{-- Identity --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Name</label>
                        <x-ui.input type="text"
                               name="name"
                               placeholder="John Doe"
                               value="{{ old('name', $managedUser->name ?? '') }}"
                               autocomplete="name"
                               required
                               x-bind:disabled="isBanned" />
                        @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Username</label>
                        <x-ui.input type="text"
                               name="username"
                               placeholder="johndoe"
                               value="{{ old('username', $managedUser->username ?? '') }}"
                               autocomplete="username"
                               required
                               x-bind:disabled="isBanned" />
                        @error('username') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Email</label>
                        <x-ui.input type="email"
                               name="email"
                               placeholder="john@example.com"
                               value="{{ old('email', $managedUser->email ?? '') }}"
                               autocomplete="email"
                               required
                               x-bind:disabled="isBanned" />
                        @error('email') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Passwords --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-zinc-800/50">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">
                            Password <span class="text-zinc-500 font-normal">{{ $isEdit ? '(leave blank to keep)' : '' }}</span>
                        </label>
                        
                        <div class="group relative rounded-lg p-[1px] bg-neutral-800 transition duration-500 focus-within:bg-gradient-to-r focus-within:from-indigo-500 focus-within:via-purple-500 focus-within:to-indigo-500">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="••••••••"
                                   {{ $isEdit ? '' : 'required' }}
                                   autocomplete="new-password"
                                   x-bind:disabled="isBanned"
                                   class="relative flex h-10 w-full rounded-md border-none bg-neutral-950 px-3 py-2 text-sm text-neutral-300 placeholder:text-neutral-500 focus:outline-none focus:ring-0 pr-10">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-500 hover:text-zinc-300 cursor-pointer z-10"
                                    onclick="const p=document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror

                        @if($isEdit)
                            <div class="mt-4 flex items-center justify-between">
                                <span class="flex-grow flex flex-col">
                                    <span class="text-sm font-medium text-zinc-300">Email User</span>
                                    <span class="text-xs text-zinc-500">Send the new password via email</span>
                                </span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="send_password_email" value="1" class="sr-only peer" x-bind:disabled="isBanned">
                                    <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Confirm Password</label>
                            <x-ui.input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="••••••••"
                                   :required="!$isEdit"
                                   autocomplete="new-password"
                                   x-bind:disabled="isBanned" />
                    </div>
                </div>

                @if($isEdit)
                    <div class="mt-4 p-4 rounded-lg border border-zinc-700 bg-zinc-800/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-white">Generate & Send New Password</h3>
                                <p class="text-xs text-zinc-400 mt-1">Instantly generate a random password and email it to the user.</p>
                            </div>
                            <button type="submit" 
                                    form="generate-password-form"
                                    class="inline-flex items-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    x-bind:disabled="isBanned">
                                Generate & Send
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Toggles --}}
                {{-- Toggles --}}
                <div class="grid grid-cols-1 gap-6 pt-4 border-t border-zinc-800/50">

                    {{-- Admin Toggle --}}
                    <div class="flex items-center justify-between p-3 rounded-lg border border-zinc-800 bg-zinc-900/50">
                        <span class="flex flex-col">
                            <span class="text-sm font-medium text-zinc-300">Admin Privileges</span>
                            <span class="text-xs text-zinc-500">Grant full administrative access</span>
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_admin" value="1" class="sr-only peer" x-model="isAdmin" :disabled="isBanned">
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                        </label>
                    </div>

                    {{-- Verified Toggle --}}
                    <div class="flex items-center justify-between p-3 rounded-lg border border-zinc-800 bg-zinc-900/50">
                        <span class="flex flex-col">
                            <span class="text-sm font-medium text-zinc-300">Email Verified</span>
                            <span class="text-xs text-zinc-500">Mark email as verified manually</span>
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="verified" value="1" class="sr-only peer" x-model="isVerified" :disabled="isBanned">
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                        </label>
                    </div>

                    {{-- Banned Toggle --}}
                    <div class="flex items-center justify-between p-3 rounded-lg border border-red-900/20 bg-red-900/10">
                        <span class="flex flex-col">
                            <span class="text-sm font-medium text-red-400">Banned</span>
                            <span class="text-xs text-red-500/70">Prevent user from logging in</span>
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="banned" value="1" class="sr-only peer" :checked="isBanned" @change="toggleBanned()">
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-900 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end pt-4">
                    <button class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]" type="submit">
                        {{ $isEdit ? 'Save Changes' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
