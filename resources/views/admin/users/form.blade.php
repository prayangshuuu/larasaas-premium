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
            <form method="POST"
                  action="{{ $isEdit ? route('admin.users.update', $managedUser) : route('admin.users.store') }}"
                  class="space-y-6">
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
                               required />
                        @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Username</label>
                        <x-ui.input type="text"
                               name="username"
                               placeholder="johndoe"
                               value="{{ old('username', $managedUser->username ?? '') }}"
                               autocomplete="username"
                               required />
                        @error('username') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Email</label>
                        <x-ui.input type="email"
                               name="email"
                               placeholder="john@example.com"
                               value="{{ old('email', $managedUser->email ?? '') }}"
                               autocomplete="email"
                               required />
                        @error('email') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Passwords --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-zinc-800/50">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">
                            Password <span class="text-zinc-500 font-normal">{{ $isEdit ? '(leave blank to keep)' : '' }}</span>
                        </label>
                        <div class="relative">
                            <x-ui.input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="••••••••"
                                   {{ $isEdit ? '' : 'required' }}
                                   autocomplete="new-password" />
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-500 hover:text-zinc-300 cursor-pointer"
                                    onclick="const p=document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Confirm Password</label>
                         <div class="relative">
                            <x-ui.input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   placeholder="••••••••"
                                   {{ $isEdit ? '' : 'required' }}
                                   autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                {{-- Toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-zinc-800/50">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox"
                               name="is_admin"
                               value="1"
                               class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50 transition-colors"
                               @checked( (bool) old('is_admin', $isEdit ? (bool) $managedUser->is_admin : false) )>
                        <span class="text-sm font-medium text-zinc-300 group-hover:text-white transition-colors">Admin Privileges</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox"
                               name="verified"
                               value="1"
                               class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50 transition-colors"
                               @checked( (bool) old('verified', $isEdit ? (bool) $managedUser->email_verified_at : false) )>
                        <span class="text-sm font-medium text-zinc-300 group-hover:text-white transition-colors">Email Verified</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox"
                               name="banned"
                               value="1"
                               class="rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-600/50 transition-colors"
                               @checked( (bool) old('banned', $isEdit ? (bool) $managedUser->banned_at : false) )>
                        <span class="text-sm font-medium text-zinc-300 group-hover:text-white transition-colors">Banned</span>
                    </label>
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
