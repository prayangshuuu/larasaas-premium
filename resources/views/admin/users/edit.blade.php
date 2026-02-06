<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User') }} - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Back Request -->
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center mb-4 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                &larr; Back to Users
            </a>

            <div class="p-4 sm:p-8 bg-white dark:bg-zinc-900 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Profile Information') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Update the account's profile information.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-6">
                            @csrf
                            @method('put')

                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('role')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Manage Subscription Section -->
            <div class="p-4 sm:p-8 bg-white dark:bg-zinc-900 shadow sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Manage Subscription') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-zinc-400">
                                {{ __("Manually assign or remove subscriptions for this user.") }}
                            </p>
                        </header>

                        <div class="mt-6 space-y-6">
                            @php
                                $subscription = $user->subscriptions()->whereIn('status', ['active', 'past_due', 'canceled'])->latest()->first();
                            @endphp

                            @if($subscription)
                                <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                                     <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <div class="relative z-10 flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                                {{ $subscription->plan->name ?? 'Unknown Plan' }}
                                                <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-medium text-emerald-400 border border-emerald-500/20 capitalize">{{ $subscription->status }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1 font-mono">
                                                @if($subscription->current_period_end)
                                                    Renews: {{ $subscription->current_period_end->format('M d, Y') }}
                                                @else
                                                    No Renewal Date
                                                @endif
                                            </p>
                                        </div>
                                        <form method="POST" action="{{ route('admin.users.subscriptions.destroy', [$user, $subscription]) }}" onsubmit="return confirm('Are you sure you want to cancel this subscription immediately?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-500/20 border border-red-500/20 transition-colors">
                                                Cancel / Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Change Plan Form -->
                                <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Change Plan</h4>
                                    <form method="POST" action="{{ route('admin.users.subscriptions.update', [$user, $subscription]) }}" class="flex items-end gap-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex-1">
                                            <x-ui.label for="update_plan_id" value="Select New Plan" class="text-zinc-600 dark:text-zinc-400 mb-1" />
                                            <select id="update_plan_id" name="plan_id" class="block w-full rounded-md border-0 bg-white dark:bg-zinc-900 py-2.5 px-3 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-zinc-700 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                @foreach(\App\Models\Plan::where('is_active', true)->get() as $p)
                                                    <option value="{{ $p->id }}" {{ $subscription->plan_id == $p->id ? 'selected' : '' }}>
                                                        {{ $p->name }} — {{ $p->price }} {{ $p->currency }} / {{ $p->interval }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                                            Update
                                        </button>
                                    </form>
                                 </div>
                            @else
                                <div class="bg-yellow-50 dark:bg-yellow-900/10 p-4 rounded-xl border border-yellow-200 dark:border-yellow-500/20">
                                    <div class="flex">
                                        <div class="shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-500">No active subscription</h3>
                                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                                                <p>Assign a plan manually to give this user access.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.users.subscriptions.store', $user) }}" class="flex items-end gap-3 p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    @csrf
                                    <div class="flex-1">
                                        <x-ui.label for="plan_id" value="Select Plan" class="text-zinc-600 dark:text-zinc-400 mb-1" />
                                        <select id="plan_id" name="plan_id" class="block w-full rounded-md border-0 bg-white dark:bg-zinc-900 py-2.5 px-3 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-zinc-700 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            @foreach(\App\Models\Plan::where('is_active', true)->get() as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->price }} {{ $p->currency }} / {{ $p->interval }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                                        Assign Plan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </section>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
