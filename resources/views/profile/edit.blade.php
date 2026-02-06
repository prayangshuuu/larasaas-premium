{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Profile Settings</h1>
                <p class="text-sm text-zinc-400">Manage your account settings and preferences.</p>
            </div>

            {{-- Success flashes --}}
            @if (session('status') === 'profile-updated')
                <div class="rounded-lg bg-green-500/10 p-4 border border-green-500/20">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-400">Profile updated successfully.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                 <div class="rounded-lg bg-green-500/10 p-4 border border-green-500/20">
                    <div class="flex">
                        <div class="shrink-0">
                             <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-400">Password updated successfully.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
