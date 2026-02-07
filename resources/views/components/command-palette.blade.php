
@props(['trigger' => 'Cmd+K'])

<div x-data="commandPalette()"
     @keydown.window.prevent.cmd.k="toggle()"
     @keydown.window.prevent.ctrl.k="toggle()"
     @keydown.window.escape="isOpen = false"
     class="relative z-[9999]"
     style="display: none;"
     x-show="isOpen">

    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
         @click="isOpen = false"></div>

    {{-- Modal --}}
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="mx-auto max-w-2xl transform divide-y divide-zinc-800 overflow-hidden rounded-xl bg-zinc-900 shadow-2xl ring-1 ring-white/10 transition-all">

            {{-- Search Input --}}
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-zinc-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text"
                       class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-white placeholder:text-zinc-500 focus:ring-0 sm:text-sm"
                       placeholder="Search commands..."
                       role="combobox"
                       aria-expanded="false"
                       aria-controls="options"
                       x-model="query"
                       x-ref="searchInput"
                       @keydown.arrow-down.prevent="selectNext()"
                       @keydown.arrow-up.prevent="selectPrev()"
                       @keydown.enter.prevent="executeSelected()">
            </div>

            {{-- Results List --}}
            <ul class="max-h-96 scroll-py-3 overflow-y-auto p-3" id="options" role="listbox">
                <template x-for="(group, groupIndex) in filteredGroups" :key="groupIndex">
                    <li x-show="group.items.length > 0">
                        <div class="px-2 py-1.5 text-xs font-semibold text-zinc-500" x-text="group.category"></div>
                        <ul class="mb-2">
                            <template x-for="(item, itemIndex) in group.items" :key="item.id">
                                <li class="group flex cursor-default select-none rounded-xl p-3 hover:bg-zinc-800/50"
                                    :class="{ 'bg-indigo-600/10 text-indigo-400': isSelected(item.id) }"
                                    @click="execute(item)"
                                    @mouseenter="selectedIndex = getItemGlobalIndex(item.id)"
                                    role="option"
                                    tabindex="-1">
                                    <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 group-hover:border-zinc-600"
                                         :class="{ 'border-indigo-500/30 bg-indigo-500/10': isSelected(item.id) }">
                                        <span x-html="item.icon" class="h-6 w-6 text-zinc-400 group-hover:text-white" :class="{ 'text-indigo-400': isSelected(item.id) }"></span>
                                    </div>
                                    <div class="ml-4 flex-auto">
                                        <p class="text-sm font-medium text-zinc-200 group-hover:text-white"
                                           :class="{ 'text-indigo-300': isSelected(item.id) }"
                                           x-text="item.name"></p>
                                        <p class="text-xs text-zinc-500 group-hover:text-zinc-400" x-text="item.description"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>
                
                {{-- Empty State --}}
                <li x-show="filteredGroups.every(g => g.items.length === 0)" class="px-6 py-14 text-center sm:px-14">
                    <svg class="mx-auto h-6 w-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <p class="mt-4 text-sm text-zinc-400">No commands found matching "<span x-text="query" class="font-semibold text-white"></span>"</p>
                </li>
            </ul>
            
            {{-- Footer --}}
            <div class="flex flex-wrap items-center bg-zinc-900/50 px-4 py-2.5 text-xs text-zinc-500 border-t border-zinc-800">
                Type <kbd class="mx-1 flex h-5 w-5 items-center justify-center rounded border border-zinc-700 bg-zinc-800 font-semibold text-zinc-400">Cmd</kbd> <kbd class="mx-1 flex h-5 w-5 items-center justify-center rounded border border-zinc-700 bg-zinc-800 font-semibold text-zinc-400">K</kbd> to open
                <span class="mx-2 text-zinc-700">|</span>
                <kbd class="mx-1 flex h-5 w-5 items-center justify-center rounded border border-zinc-700 bg-zinc-800 font-semibold text-zinc-400">↑</kbd> <kbd class="mx-1 flex h-5 w-5 items-center justify-center rounded border border-zinc-700 bg-zinc-800 font-semibold text-zinc-400">↓</kbd> to navigate
                <span class="mx-2 text-zinc-700">|</span>
                <kbd class="mx-1 flex h-5 items-center justify-center rounded border border-zinc-700 bg-zinc-800 px-1.5 font-semibold text-zinc-400">Enter</kbd> to select
                <span class="mx-2 text-zinc-700">|</span>
                <kbd class="mx-1 flex h-5 items-center justify-center rounded border border-zinc-700 bg-zinc-800 px-1.5 font-semibold text-zinc-400">Esc</kbd> to close
            </div>
        </div>
    </div>
</div>

<script>
    function commandPalette() {
        return {
            isOpen: false,
            query: '',
            selectedIndex: 0,
            originalGroups: [
                {
                    category: 'Navigation',
                    items: [
                        { id: 'nav-dashboard', name: 'Dashboard', description: 'Go to your personalized dashboard', url: '{{ route("dashboard") }}', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>' },
                        { id: 'nav-profile', name: 'Profile Settings', description: 'Manage your account and preferences', url: '{{ route("profile.edit") }}', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>' },
                        @if(Route::has('billing.index'))
                        { id: 'nav-billing', name: 'Billing & Plans', description: 'View your subscription and invoices', url: '{{ route("billing.index") }}', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>' },
                        @endif
                    ]
                },
                @if(Auth::user()->isAdmin())
                {
                    category: 'Admin',
                    items: [
                        { id: 'admin-dashboard', name: 'Admin Dashboard', description: 'Overview of system statistics', url: '{{ route("admin.dashboard") }}', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>' },
                        { id: 'admin-users', name: 'Manage Users', description: 'View, edit, and ban users', url: '{{ route("admin.users.index") }}', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>' },
                        { id: 'admin-settings', name: 'System Settings', description: 'Configure application settings', url: '{{ route("admin.settings.index") }}', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>' },
                    ]
                },
                @endif
                {
                    category: 'Actions',
                    items: [
                        { id: 'act-theme', name: 'Toggle Theme', description: 'Switch between light and dark mode', action: 'toggleTheme', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>' },
                        { id: 'act-logout', name: 'Log Out', description: 'Sign out of your account', action: 'logout', icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>' },
                    ]
                }
            ],
            
            get filteredGroups() {
                if (this.query === '') return this.originalGroups;
                
                return this.originalGroups.map(group => {
                    return {
                        ...group,
                        items: group.items.filter(item => 
                            item.name.toLowerCase().includes(this.query.toLowerCase()) || 
                            item.description.toLowerCase().includes(this.query.toLowerCase())
                        )
                    };
                }).filter(group => group.items.length > 0);
            },
            
            get allItems() {
                return this.filteredGroups.flatMap(group => group.items);
            },

            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.query = '';
                    this.selectedIndex = 0;
                    this.$nextTick(() => this.$refs.searchInput.focus());
                }
            },
            
            selectNext() {
                if (this.selectedIndex < this.allItems.length - 1) {
                    this.selectedIndex++;
                } else {
                    this.selectedIndex = 0; // Loop back
                }
                this.scrollToSelected();
            },
            
            selectPrev() {
                if (this.selectedIndex > 0) {
                    this.selectedIndex--;
                } else {
                    this.selectedIndex = this.allItems.length - 1; // Loop back
                }
                this.scrollToSelected();
            },
            
            isSelected(id) {
                 const item = this.allItems[this.selectedIndex];
                 return item && item.id === id;
            },
            
            getItemGlobalIndex(id) {
                return this.allItems.findIndex(item => item.id === id);
            },
            
            scrollToSelected() {
                 // Optional: Implement nice scrolling if list is long
            },

            executeSelected() {
                const item = this.allItems[this.selectedIndex];
                if (item) {
                    this.execute(item);
                }
            },

            execute(item) {
                if (item.url) {
                    window.location.href = item.url;
                } else if (item.action === 'logout') {
                    // Create a form and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("logout") }}';
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                } else if (item.action === 'toggleTheme') {
                     // Trigger theme toggle logic if available (assumed handled by ThemeController)
                     // Or just submit a form to the route
                     const form = document.createElement('form');
                     form.method = 'POST';
                     form.action = '{{ route("theme.update") }}';
                     const csrf = document.createElement('input');
                     csrf.type = 'hidden';
                     csrf.name = '_token';
                     csrf.value = '{{ csrf_token() }}';
                     // Add theme input if needed, or controller toggles
                     // Assuming simple toggle
                     form.appendChild(csrf);
                     document.body.appendChild(form);
                     form.submit();
                }
                this.isOpen = false;
            }
        };
    }
</script>
