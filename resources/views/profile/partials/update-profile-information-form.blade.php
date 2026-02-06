<section>
    @php
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $emailVerified = !($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) || $user->hasVerifiedEmail();
        $twofaEnabled  = (bool) $user->two_factor_secret;
        $twofaConfirmed = method_exists($user, 'hasConfirmedTwoFactor') ? $user->hasConfirmedTwoFactor() : !empty($user->two_factor_confirmed_at);

        $progress = 0;
        if (!empty($user->name))           $progress += 33;
        if ($emailVerified)                 $progress += 33;
        if (!empty($user->profile_picture)) $progress += 34;

        $settings        = \App\Models\Setting::instance();
        $canEditUsername = (bool) $settings->feature_usernames_editable;
        $showTwoFactorSetup = session('status') === 'two-factor-authentication-enabled';
    @endphp

    @includeIf('profile.partials.two-factor-setup-modal', ['show' => $showTwoFactorSetup])

    @if (session('status') === 'two-factor-authentication-confirmed')
        <div class="rounded-lg bg-green-500/10 p-4 mb-4 border border-green-500/20">
            <p class="text-sm font-medium text-green-400">Two-factor authentication confirmed.</p>
        </div>
    @endif
    
    @if (session('status') === 'recovery-codes-generated')
         <div class="rounded-lg bg-green-500/10 p-4 mb-4 border border-green-500/20">
            <p class="text-sm font-medium text-green-400">New recovery codes generated.</p>
        </div>
    @endif

    <div class="bg-zinc-900 shadow-xl rounded-xl border border-zinc-800">
        <div class="px-4 py-5 sm:p-6">
            <header class="flex items-start justify-between">
                <div>
                   <h2 class="text-lg font-medium text-white">Profile Information</h2>
                   <p class="mt-1 text-sm text-zinc-400">Update your account's profile information and email address.</p>
                </div>
                
                 {{-- Completion Circle --}}
                 <div class="flex flex-col items-center">
                    <div class="relative w-16 h-16">
                         @php
                            $p = min($progress, 100);
                            $c = 2 * pi() * 28; // r=28
                            $offset = $c - ($p / 100) * $c;
                        @endphp
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 64 64">
                            <circle cx="32" cy="32" r="28" fill="none" stroke-width="6" class="text-zinc-800" stroke="currentColor"></circle>
                            <circle cx="32" cy="32" r="28" fill="none" stroke-width="6" class="text-indigo-500 transition-all duration-500" stroke="currentColor"
                                    stroke-dasharray="{{ $c }}" stroke-dashoffset="{{ $offset }}" stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-zinc-300">
                            {{ $p }}%
                        </div>
                    </div>
                </div>
            </header>

            <div class="mt-6 border-t border-zinc-800"></div>

            <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
                 <div class="md:col-span-1">
                     <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col items-center sm:items-start">
                        @csrf
                        @method('patch')
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Profile Picture</label>
                        <div class="relative group cursor-pointer inline-block overflow-hidden rounded-full ring-2 ring-zinc-800 ring-offset-2 ring-offset-zinc-950 hover:ring-indigo-500 transition-all">
                             <img class="h-24 w-24 object-cover" 
                                  src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=3f3f46&color=fff' }}" 
                                  alt="{{ $user->name }}">
                             <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-white text-xs font-medium">Change</span>
                             </div>
                             <input type="file" name="profile_picture" class="absolute inset-0 opacity-0 cursor-pointer" onchange="this.form.submit()">
                        </div>
                         @error('profile_picture') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                     </form>
                 </div>

                 <div class="md:col-span-2">
                     <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-zinc-300 mb-1">Name</label>
                            <x-ui.input name="name" id="name" autocomplete="name" :value="old('name', $user->name)" required />
                            @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">Email address</label>
                            <x-ui.input type="email" name="email" id="email" autocomplete="email" :value="old('email', $user->email)" required />
                             @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror

                             @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 p-3 bg-yellow-500/10 rounded-lg border border-yellow-500/20 flex items-center justify-between">
                                    <p class="text-xs text-yellow-400">Your email is unverified.</p>
                                    <button form="send-verification" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Resend Verification</button>
                                </div>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-xs font-medium text-green-400">A new verification link has been sent.</p>
                                @endif
                            @endif
                        </div>

                         {{-- Username --}}
                         <div>
                            <label for="username" class="block text-sm font-medium text-zinc-300 mb-1">Username</label>
                            <x-ui.input name="username" id="username" :value="old('username', $user->username)" :disabled="!$canEditUsername" />
                         </div>
                        
                        <div class="flex items-center gap-4">
                            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.3)]">Save</button>
                             @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-400">Saved.</p>
                            @endif
                        </div>
                     </form>
                     
                     <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
                 </div>
            </div>

            <div class="mt-10 border-t border-zinc-800 pt-8">
                 <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-0">
                            <h3 class="text-lg font-medium text-white">Two-Factor Authentication</h3>
                            <p class="mt-1 text-sm text-zinc-400">Add additional security to your account.</p>
                        </div>
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                         <div class="flex items-center justify-between py-4 bg-black/20 rounded-xl px-4 border border-zinc-800/50">
                            <div>
                                <span class="font-medium text-zinc-300">Status</span>
                                <span class="ml-3 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border {{ $twofaEnabled ? ($twofaConfirmed ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20') : 'bg-zinc-800 text-zinc-400 border-zinc-700' }}">
                                     {{ $twofaEnabled ? ($twofaConfirmed ? 'Enabled' : 'Pending Confirmation') : 'Disabled' }}
                                </span>
                            </div>
                            
                             @if ($twofaEnabled)
                                <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-400 shadow-sm hover:bg-red-500/20 border border-red-500/10 transition-all hover:scale-105 active:scale-95">Disable</button>
                                </form>
                            @else
                                <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all hover:shadow-[0_0_20px_rgba(79,70,229,0.5)] hover:scale-105 active:scale-95">Enable 2FA</button>
                                </form>
                            @endif
                         </div>

                         @if ($twofaEnabled && ! $twofaConfirmed)
                             <div class="mt-4 p-5 rounded-xl bg-sky-500/5 border border-sky-500/20 shadow-[0_0_30px_rgba(14,165,233,0.05)]">
                                <h4 class="text-sm font-bold text-sky-400 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M9 20h.01M3 20h.01M12 20h.01M17 20h.01M20 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-1v2H5V9H4a1 1 0 01-1-1V5a1 1 0 011-1h1m0-3h18M3 4H2" /></svg>
                                    Finish Setup
                                </h4>
                                <p class="text-sm text-sky-300/70 mb-4">Scan the QR code below with your authenticator app to confirm setup.</p>
                                
                                <div class="mb-4 p-2 bg-white rounded-lg inline-block shadow-lg border-4 border-sky-900/10">
                                    {!! $user->twoFactorQrCodeSvg() !!}
                                </div>
                                <p class="text-xs text-sky-300/60 mb-4 font-mono">
                                    KEY: <span class="bg-sky-950 px-1.5 py-0.5 rounded text-sky-200 select-all">{{ decrypt($user->two_factor_secret) }}</span>
                                </p>
                                
                                <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="flex gap-3 max-w-sm">
                                    @csrf
                                    <x-ui.input name="code" placeholder="123 456" required class="flex-1 text-center font-mono tracking-widest" inputmode="numeric" />
                                    <button type="submit" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 hover:shadow-sky-500/20 transition-all">Confirm</button>
                                </form>
                             </div>
                         @endif

                         @if ($twofaEnabled)
                             <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-zinc-800 bg-zinc-900/50">
                                <div>
                                    <h4 class="text-sm font-medium text-white">Recovery Codes</h4>
                                    <p class="text-xs text-zinc-500 mt-1">Generate new codes if you've lost access.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('two-factor.codes.show') }}" class="rounded-lg bg-zinc-800 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-700 hover:text-indigo-300 border border-zinc-700 transition-colors">View Codes</a>
                                    
                                     <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}"
                                          onsubmit="return confirm('Regenerate recovery codes? Old codes will stop working.');">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-zinc-800 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-700 hover:text-indigo-300 border border-zinc-700 transition-colors">Regenerate</button>
                                    </form>
                                </div>
                             </div>
                         @endif
                    </div>
                 </div>
            </div>
        </div>
    </div>
</section>
