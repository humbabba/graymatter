<x-layouts.app>
    <div class="p-4 md:p-8 max-w-2xl">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-3 h-10 bg-graymatter-teal rounded-none"></div>
            <h1>Edit Role</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-graymatter-red/20 border border-graymatter-red/50 text-graymatter-red rounded-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('roles.update', $role) }}" data-ajax-save class="bg-graymatter-panel-light rounded-sm p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $role->name) }}"
                    required
                    autofocus
                    class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                >
            </div>

            <div class="mb-6">
                <label for="description" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Description</label>
                <input
                    type="text"
                    id="description"
                    name="description"
                    value="{{ old('description', $role->description) }}"
                    required
                    class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                >
            </div>

            <div class="mb-8">
                <label class="block mb-3 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Permissions</label>
                <div class="space-y-4">
                    @foreach($permissionGroups->sortKeys() as $group => $permissions)
                        <div class="bg-graymatter-panel rounded-sm p-4" x-data="{
                            ids: [{{ $permissions->pluck('id')->implode(',') }}],
                            get allChecked() {
                                return this.ids.every(id => document.querySelector('input[name=\'permissions[]\'][value=\'' + id + '\']')?.checked);
                            },
                            toggleAll() {
                                const newState = !this.allChecked;
                                this.ids.forEach(id => {
                                    const cb = document.querySelector('input[name=\'permissions[]\'][value=\'' + id + '\']');
                                    if (cb) cb.checked = newState;
                                });
                            }
                        }">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold text-graymatter-lime uppercase text-sm tracking-wider" style="font-family: var(--font-display);">{{ ucfirst(str_replace('-', ' ', $group)) }}</span>
                                <button type="button" @click="toggleAll()" class="text-text-muted hover:text-graymatter-teal text-xs uppercase tracking-wider transition-colors" style="font-family: var(--font-display);">
                                    <span x-text="allChecked ? 'Deselect all' : 'Select all'"></span>
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                @foreach($permissions as $permission)
                                    <label class="flex items-center gap-2 px-3 py-2 bg-graymatter-dark rounded-sm cursor-pointer hover:bg-graymatter-panel-light transition-colors">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-divider bg-graymatter-dark text-graymatter-lime focus:ring-graymatter-lime"
                                        >
                                        <span class="text-text text-sm">{{ ucfirst(explode('.', $permission->name)[1] ?? $permission->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-8" x-data x-show="document.querySelector('input[name=\'permissions[]\'][value=\'{{ $permissionGroups->get('users')?->firstWhere('name', 'users.edit')?->id }}\']')?.checked" x-cloak
                 x-effect="$el.style.display = document.querySelector('input[name=\'permissions[]\'][value=\'{{ $permissionGroups->get('users')?->firstWhere('name', 'users.edit')?->id }}\']')?.checked ? '' : 'none'">
                <label class="block mb-3 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Assignable Roles</label>
                <p class="text-text-muted text-xs mb-3">Which roles can users with this role assign to other users?</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($roles as $assignableRole)
                        <label class="flex items-center gap-3 p-3 bg-graymatter-panel rounded-sm cursor-pointer hover:bg-graymatter-panel-light transition-colors">
                            <input
                                type="checkbox"
                                name="assignable_roles[]"
                                value="{{ $assignableRole->id }}"
                                {{ in_array($assignableRole->id, old('assignable_roles', $assignableRoleIds)) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-divider bg-graymatter-dark text-graymatter-lime focus:ring-graymatter-lime"
                            >
                            <div>
                                <span class="text-graymatter-lime font-semibold block">{{ $assignableRole->name }}</span>
                                <span class="text-text-muted text-xs">{{ $assignableRole->description }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="btn btn-primary">
                    Update Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn bg-graymatter-panel-light text-text hover:bg-divider no-underline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
