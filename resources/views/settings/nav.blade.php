<x-layouts.app>
    <script>
        window.__navData = {
            items: @json($navItems),
            roles: @json($roles),
            models: @json($manageableModels),
        };
    </script>
    <div class="p-4 md:p-8"
         x-data="navManager()"
         @keydown.window.prevent.ctrl.s="save()"
         @keydown.window.prevent.meta.s="save()">

        {{-- Save indicator --}}
        <div x-show="isDirty || saved || saving"
             x-cloak
             class="save-indicator"
             :class="{
                 'save-indicator--unsaved': isDirty && !saving,
                 'save-indicator--saved': saved,
                 'save-indicator--saving': saving
             }"
             @click="save()">
            <span x-text="saving ? 'Saving...' : (saved ? 'Saved!' : 'Unsaved changes')"></span>
        </div>
        <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-8">
            <h1>Navigation</h1>
            <a href="{{ route('settings.index') }}" class="btn btn-secondary">Back to settings</a>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-graymatter-green/20 border border-graymatter-green/50 text-graymatter-green rounded-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-graymatter-panel-light rounded-sm p-6">
            <p class="text-text-muted text-sm mb-4">Manage the main navigation menu. Use arrows to reorder, indent items to nest them under a parent.</p>

            {{-- Nav items list --}}
            <div class="space-y-2 mb-4">
                <template x-for="(item, i) in items" :key="item._id">
                    <div>
                        {{-- Parent item --}}
                        <div class="flex items-center gap-2 p-3 bg-graymatter-panel rounded-sm border border-divider">
                            <div class="flex flex-col gap-0.5">
                                <button type="button" @click="moveUp(i)" :disabled="i === 0" class="text-text-muted hover:text-text disabled:opacity-30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <button type="button" @click="moveDown(i)" :disabled="i === items.length - 1" class="text-text-muted hover:text-text disabled:opacity-30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                            <button type="button" @click="indentItem(i)" x-show="i > 0" title="Indent" class="text-text-muted hover:text-graymatter-teal transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <input type="text" x-model="item.label" placeholder="Label" @input="markDirty()" class="bg-graymatter-dark border border-divider rounded-sm px-3 py-1.5 text-text text-sm w-36 focus:border-graymatter-teal">
                            <input type="text" x-model="item.url" placeholder="/url" @input="markDirty()" class="bg-graymatter-dark border border-divider rounded-sm px-3 py-1.5 text-text text-sm w-40 font-mono focus:border-graymatter-teal">
                            <div class="flex-1 flex flex-wrap gap-1 items-center">
                                <template x-for="role in allRoles" :key="role">
                                    <label class="flex items-center gap-1 px-2 py-1 bg-graymatter-dark rounded-sm cursor-pointer text-xs hover:bg-graymatter-panel-light transition-colors">
                                        <input type="checkbox" :value="role" :checked="(item.roles || []).includes(role)" @change="toggleRole(item, role)" class="w-3 h-3 accent-graymatter-lime">
                                        <span class="text-text-muted" x-text="role"></span>
                                    </label>
                                </template>
                                <span x-show="!item.roles || item.roles.length === 0" class="text-text-muted text-xs italic">all users</span>
                            </div>
                            <button type="button" @click="removeItem(i)" class="text-graymatter-red hover:text-graymatter-red/80 text-xs uppercase tracking-wider shrink-0" style="font-family: var(--font-display);">Remove</button>
                        </div>

                        {{-- Children --}}
                        <template x-for="(child, ci) in item.children || []" :key="child._id">
                            <div class="flex items-center gap-2 p-3 bg-graymatter-dark rounded-sm border border-divider/50 ml-8 mt-1">
                                <div class="flex flex-col gap-0.5">
                                    <button type="button" @click="moveChildUp(i, ci)" :disabled="ci === 0" class="text-text-muted hover:text-text disabled:opacity-30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button type="button" @click="moveChildDown(i, ci)" :disabled="ci === (item.children || []).length - 1" class="text-text-muted hover:text-text disabled:opacity-30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <button type="button" @click="outdentChild(i, ci)" title="Outdent" class="text-text-muted hover:text-graymatter-teal transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <input type="text" x-model="child.label" placeholder="Label" @input="markDirty()" class="bg-graymatter-panel border border-divider rounded-sm px-3 py-1.5 text-text text-sm w-36 focus:border-graymatter-teal">
                                <input type="text" x-model="child.url" placeholder="/url" @input="markDirty()" class="bg-graymatter-panel border border-divider rounded-sm px-3 py-1.5 text-text text-sm w-40 font-mono focus:border-graymatter-teal">
                                <div class="flex-1 flex flex-wrap gap-1 items-center">
                                    <template x-for="role in allRoles" :key="role">
                                        <label class="flex items-center gap-1 px-2 py-1 bg-graymatter-panel rounded-sm cursor-pointer text-xs hover:bg-graymatter-panel-light transition-colors">
                                            <input type="checkbox" :value="role" :checked="(child.roles || []).includes(role)" @change="toggleRole(child, role)" class="w-3 h-3 accent-graymatter-lime">
                                            <span class="text-text-muted" x-text="role"></span>
                                        </label>
                                    </template>
                                    <span x-show="!child.roles || child.roles.length === 0" class="text-text-muted text-xs italic">all users</span>
                                </div>
                                <button type="button" @click="removeChild(i, ci)" class="text-graymatter-red hover:text-graymatter-red/80 text-xs uppercase tracking-wider shrink-0" style="font-family: var(--font-display);">Remove</button>
                            </div>
                        </template>

                        {{-- Add child button --}}
                        <div class="ml-8 mt-1">
                            <button type="button" @click="addChild(i)" class="text-text-muted hover:text-graymatter-teal text-xs transition-colors">+ Add child</button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-wrap gap-3 mb-4">
                <button type="button" @click="addItem()" class="btn btn-secondary text-sm">+ Add item</button>
                <button type="button" @click="addManageableModels()" class="btn btn-secondary text-sm">+ Add manageable models</button>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="save()" class="btn btn-primary" :disabled="saving || !isDirty">
                    <span x-show="!saving">Save navigation</span>
                    <span x-show="saving" x-cloak>Saving...</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function navManager() {
            const __d = window.__navData;
            let _nextId = 1;
            function uid() { return _nextId++; }
            function withId(item) {
                item._id = uid();
                if (item.children) item.children = item.children.map(c => withId(c));
                return item;
            }

            return {
                items: (__d.items || []).map(i => withId(i)),
                allRoles: __d.roles || [],
                manageableModels: __d.models || [],
                saving: false,
                saved: false,
                isDirty: false,

                markDirty() { this.isDirty = true; this.saved = false; },

                addItem() {
                    this.items.push(withId({ label: '', url: '', roles: [], children: [] }));
                    this.markDirty();
                },

                addChild(parentIndex) {
                    if (!this.items[parentIndex].children) this.items[parentIndex].children = [];
                    this.items[parentIndex].children.push(withId({ label: '', url: '', roles: [] }));
                    this.markDirty();
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.markDirty();
                },

                removeChild(parentIndex, childIndex) {
                    this.items[parentIndex].children.splice(childIndex, 1);
                    this.markDirty();
                },

                moveUp(index) {
                    if (index > 0) {
                        [this.items[index - 1], this.items[index]] = [this.items[index], this.items[index - 1]];
                        this.markDirty();
                    }
                },

                moveDown(index) {
                    if (index < this.items.length - 1) {
                        [this.items[index], this.items[index + 1]] = [this.items[index + 1], this.items[index]];
                        this.markDirty();
                    }
                },

                moveChildUp(parentIndex, childIndex) {
                    const children = this.items[parentIndex].children;
                    if (childIndex > 0) {
                        [children[childIndex - 1], children[childIndex]] = [children[childIndex], children[childIndex - 1]];
                        this.markDirty();
                    }
                },

                moveChildDown(parentIndex, childIndex) {
                    const children = this.items[parentIndex].children;
                    if (childIndex < children.length - 1) {
                        [children[childIndex], children[childIndex + 1]] = [children[childIndex + 1], children[childIndex]];
                        this.markDirty();
                    }
                },

                indentItem(index) {
                    if (index === 0) return;
                    const item = this.items.splice(index, 1)[0];
                    const grandchildren = (item.children || []).map(c => withId({ label: c.label, url: c.url, roles: c.roles, children: [] }));
                    const parent = this.items[index - 1];
                    if (!parent.children) parent.children = [];
                    parent.children.push({ _id: item._id, label: item.label, url: item.url, roles: item.roles });
                    // Promote grandchildren to top-level items below the parent
                    this.items.splice(index, 0, ...grandchildren);
                    this.markDirty();
                },

                outdentChild(parentIndex, childIndex) {
                    const child = this.items[parentIndex].children.splice(childIndex, 1)[0];
                    child.children = child.children || [];
                    this.items.splice(parentIndex + 1, 0, withId(child));
                    this.markDirty();
                },

                toggleRole(item, role) {
                    if (!item.roles) item.roles = [];
                    const idx = item.roles.indexOf(role);
                    if (idx === -1) {
                        item.roles.push(role);
                    } else {
                        item.roles.splice(idx, 1);
                    }
                    this.markDirty();
                },

                addManageableModels() {
                    const existingUrls = new Set();
                    this.items.forEach(item => {
                        existingUrls.add(item.url);
                        (item.children || []).forEach(c => existingUrls.add(c.url));
                    });
                    let added = 0;
                    this.manageableModels.forEach(model => {
                        if (!existingUrls.has(model.url)) {
                            this.items.push(withId({ label: model.label, url: model.url, roles: [], children: [] }));
                            added++;
                        }
                    });
                    if (added === 0) alert('All manageable models are already in the navigation.');
                },

                async save() {
                    this.saving = true;
                    this.saved = false;
                    const payload = this.items.map(item => ({
                        label: item.label,
                        url: item.url,
                        roles: item.roles?.length ? item.roles : null,
                        children: (item.children || []).map(c => ({
                            label: c.label,
                            url: c.url,
                            roles: c.roles?.length ? c.roles : null,
                        })),
                    }));
                    try {
                        const res = await fetch('{{ route("settings.nav.update") }}', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ items: payload }),
                        });
                        if (res.ok) {
                            this.isDirty = false;
                            this.saved = true;
                            setTimeout(() => this.saved = false, 3000);
                        }
                    } finally {
                        this.saving = false;
                    }
                },
            };
        }
    </script>
</x-layouts.app>
