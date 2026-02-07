<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('API Tokens') }}
        </h2>

        <p class="mt-1 text-sm text-zinc-400">
            {{ __('Manage API tokens that allow third-party services to access this application on your behalf.') }}
        </p>
    </header>

    {{-- New Token Form --}}
    <form method="post" action="{{ route('api-tokens.store') }}" class="mt-6 space-y-6">
        @csrf
        <div>
            <x-input-label for="token_name" :value="__('Token Name')" />
            <x-text-input id="token_name" name="name" type="text" class="mt-1 block w-full" placeholder="e.g. My Development Token" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create Token') }}</x-primary-button>

            @if (session('status') === 'token-created')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400"
                >{{ __('Created.') }}</p>
            @endif
        </div>
    </form>

    {{-- Display New Token (Only Once) --}}
    @if (session('flash.token'))
        <div class="mt-6 p-4 bg-green-900/20 border border-green-500/30 rounded-lg">
            <h3 class="text-sm font-medium text-green-400 mb-2">
                {{ __('New API Token Created') }}
            </h3>
            <div class="text-xs text-zinc-400 mb-3">
                {{ __('Please copy your new API token. For your security, it won\'t be shown again.') }}
            </div>
            
            <div x-data="{ copied: false }" class="relative">
                <code class="block w-full p-3 bg-black/50 border border-green-500/20 rounded-md text-green-300 font-mono text-sm break-all">
                    {{ session('flash.token') }}
                </code>
                <button @click="navigator.clipboard.writeText('{{ session('flash.token') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="absolute top-2 right-2 px-2 py-1 bg-green-500/20 hover:bg-green-500/30 text-green-400 text-xs rounded transition-colors flex items-center gap-1">
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied">Copied!</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Existing Tokens List --}}
    @if ($user->tokens->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-sm font-medium text-white mb-4">{{ __('Active Tokens') }}</h3>
            
            <div class="divide-y divide-zinc-800 border border-zinc-800 rounded-lg overflow-hidden">
                @foreach ($user->tokens as $token)
                    <div class="flex items-center justify-between p-4 bg-zinc-900/30">
                        <div>
                            <div class="text-sm font-medium text-white">{{ $token->name }}</div>
                            <div class="text-xs text-zinc-500 mt-1">
                                {{ __('Last used') }} {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : __('Never') }}
                            </div>
                        </div>

                        <form method="post" action="{{ route('api-tokens.destroy', $token->id) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition-colors">
                                {{ __('Revoke') }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
