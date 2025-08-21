{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)] bg-base-100">
        <div class="max-w-3xl mx-auto px-4 py-10">

            {{-- Header (no avatar) --}}
            <div class="text-center space-y-1 mb-8">
                <h1 class="text-xl font-bold text-base-content">Your Profile</h1>
                <p class="text-sm text-base-content/70">Manage your account settings and preferences.</p>
            </div>

            {{-- Success flashes --}}
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success mb-6">
                    <span>Profile updated successfully.</span>
                </div>
            @endif
            @if (session('status') === 'password-updated')
                <div class="alert alert-success mb-6">
                    <span>Password updated successfully.</span>
                </div>
            @endif

            {{-- Sections (partials should use DaisyUI cards/inputs/buttons) --}}
            <div class="space-y-6">
                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
