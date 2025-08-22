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
                <h1 class="text-2xl font-semibold text-base-content">
                    {{ $isEdit ? 'Edit User' : 'Create User' }}
                </h1>
                <p class="text-sm text-base-content/70">
                    {{ $isEdit ? 'Update account details and privileges.' : 'Add a new account with the correct privileges.' }}
                </p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost border border-base-300 rounded-xl">Back</a>
        </div>

        {{-- Card --}}
        <div class="card bg-base-100 border border-base-300 shadow rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                <form method="POST"
                      action="{{ $isEdit ? route('admin.users.update', $managedUser) : route('admin.users.store') }}"
                      class="space-y-4">
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    {{-- Identity --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Name</span></label>
                            <input type="text"
                                   name="name"
                                   class="input input-bordered h-12 w-full"
                                   value="{{ old('name', $managedUser->name ?? '') }}"
                                   autocomplete="name"
                                   required />
                            @error('name') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Username</span></label>
                            <input type="text"
                                   name="username"
                                   class="input input-bordered h-12 w-full"
                                   value="{{ old('username', $managedUser->username ?? '') }}"
                                   autocomplete="username"
                                   required />
                            @error('username') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Email</span></label>
                            <input type="email"
                                   name="email"
                                   class="input input-bordered h-12 w-full"
                                   value="{{ old('email', $managedUser->email ?? '') }}"
                                   autocomplete="email"
                                   required />
                            @error('email') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Passwords --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Password {{ $isEdit ? '(leave blank to keep)' : '' }}</span>
                            </label>
                            <div class="join w-full">
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="join-item input input-bordered h-12 w-full"
                                       {{ $isEdit ? '' : 'required' }}
                                       autocomplete="new-password">
                                <button class="join-item btn btn-ghost"
                                        type="button"
                                        onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password'">
                                    Show
                                </button>
                            </div>
                            @error('password') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Confirm Password</span></label>
                            <div class="join w-full">
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="join-item input input-bordered h-12 w-full"
                                       {{ $isEdit ? '' : 'required' }}
                                       autocomplete="new-password">
                                <button class="join-item btn btn-ghost"
                                        type="button"
                                        onclick="const p=document.getElementById('password_confirmation');p.type=p.type==='password'?'text':'password'">
                                    Show
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Toggles --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <label class="label cursor-pointer justify-start gap-3">
                            <span class="label-text">Admin</span>
                            <input type="checkbox"
                                   name="is_admin"
                                   value="1"
                                   class="toggle"
                                   @checked( (bool) old('is_admin', $isEdit ? (bool) $managedUser->is_admin : false) )>
                        </label>

                        <label class="label cursor-pointer justify-start gap-3">
                            <span class="label-text">Verified</span>
                            <input type="checkbox"
                                   name="verified"
                                   value="1"
                                   class="toggle"
                                   @checked( (bool) old('verified', $isEdit ? (bool) $managedUser->email_verified_at : false) )>
                        </label>

                        <label class="label cursor-pointer justify-start gap-3">
                            <span class="label-text">Banned</span>
                            <input type="checkbox"
                                   name="banned"
                                   value="1"
                                   class="toggle"
                                   @checked( (bool) old('banned', $isEdit ? (bool) $managedUser->banned_at : false) )>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="card-actions justify-end">
                        <button class="btn btn-primary rounded-xl h-12 min-w-40" type="submit">
                            {{ $isEdit ? 'Save Changes' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
