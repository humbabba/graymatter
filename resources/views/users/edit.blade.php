<x-layouts.app>
    <div class="p-4 md:p-8 max-w-2xl">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-3 h-10 bg-graymatter-teal rounded-none"></div>
            <h1>Edit User</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-graymatter-red/20 border border-graymatter-red/50 text-graymatter-red rounded-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user) }}" data-ajax-save class="bg-graymatter-panel-light rounded-sm p-6">
            @csrf
            @method('PUT')

            <div class="mb-6 flex flex-col items-center">
                <x-avatar :user="$user" :size="12" />
                <a href="https://gravatar.com/profile" target="_blank" rel="noopener noreferrer" class="mt-2 text-graymatter-teal text-xs hover:underline">
                    Edit Gravatar
                </a>
            </div>

            <div class="mb-6">
                <label for="name" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                    class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                >
            </div>

            <div class="mb-6">
                <label for="email" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                >
            </div>

            @if(auth()->id() === $user->id)
                <div class="mb-6">
                    <label for="starting_view" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Starting View</label>
                    <select
                        id="starting_view"
                        name="starting_view"
                        class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                    >
                        @foreach($startingViews as $value => $label)
                            <option value="{{ $value }}" {{ old('starting_view', $user->starting_view ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label for="theme" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Theme</label>
                    <select
                        id="theme"
                        name="theme"
                        class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                    >
                        <option value="light" {{ old('theme', $user->theme ?? 'light') === 'light' ? 'selected' : '' }}>Light</option>
                        <option value="dark" {{ old('theme', $user->theme ?? 'light') === 'dark' ? 'selected' : '' }}>Dark</option>
                    </select>
                </div>

                @if($user->isAppAdmin())
                    <div class="mb-6 pt-4 border-t border-divider">
                        <h2 class="text-lg font-semibold text-graymatter-teal mb-4" style="font-family: var(--font-display);">Email notifications</h2>

                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="notify_on_new_user" value="0">
                                <div class="relative">
                                    <input type="checkbox" name="notify_on_new_user" value="1"
                                        {{ old('notify_on_new_user', $user->notify_on_new_user) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-12 h-6 border border-subtle rounded-full peer-checked:bg-graymatter-green peer-checked:border-graymatter-green transition-colors"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-subtle rounded-full peer-checked:bg-graymatter-black peer-checked:translate-x-6 transition-all"></div>
                                </div>
                                <div>
                                    <span class="font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">New users</span>
                                    <p class="text-text-muted text-xs">Email when a new user first logs in</p>
                                </div>
                            </label>

                            @php $notifyOnCreate = old('notify_on_create', $user->notify_on_create ?? []); @endphp
                            @foreach(\App\Console\Commands\SyncPermissions::getManageableModels() as $modelClass)
                                @if($modelClass::permissionPrefix() !== 'users')
                                    @php $prefix = $modelClass::permissionPrefix(); @endphp
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <div class="relative">
                                            <input type="checkbox" name="notify_on_create[]" value="{{ $prefix }}"
                                                {{ in_array($prefix, (array) $notifyOnCreate) ? 'checked' : '' }}
                                                class="sr-only peer">
                                            <div class="w-12 h-6 border border-subtle rounded-full peer-checked:bg-graymatter-green peer-checked:border-graymatter-green transition-colors"></div>
                                            <div class="absolute left-1 top-1 w-4 h-4 bg-subtle rounded-full peer-checked:bg-graymatter-black peer-checked:translate-x-6 transition-all"></div>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">New {{ strtolower($modelClass::permissionLabel()) }}</span>
                                            <p class="text-text-muted text-xs">Email when a new {{ strtolower($modelClass::permissionLabel()) }} is created</p>
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            @if(auth()->id() === $user->id && $authMode !== 'code' && $user->hasPassword())
                <div class="mb-6 pt-6 border-t border-divider">
                    <h2 class="text-lg font-semibold text-graymatter-teal mb-4" style="font-family: var(--font-display);">Change password</h2>
                    <div class="space-y-4" x-data="{ saving: false, message: '' }">
                        <div>
                            <label for="current_password" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Current password</label>
                            <input type="password" id="current_password" name="current_password" form="password-form"
                                   class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal">
                        </div>
                        <div>
                            <label for="new_password" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">New password</label>
                            <input type="password" id="new_password" name="password" form="password-form"
                                   class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal">
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Confirm new password</label>
                            <input type="password" id="new_password_confirmation" name="password_confirmation" form="password-form"
                                   class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal">
                        </div>
                        <div>
                            <form id="password-form" method="POST" action="{{ route('users.update-password', $user) }}"
                                  @submit.prevent="saving = true; message = '';
                                      fetch($el.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: new FormData($el) })
                                      .then(r => r.json())
                                      .then(d => { saving = false; if (d.success) { message = 'Password updated.'; $el.reset(); } else { message = d.message || Object.values(d.errors || {}).flat().join(' '); } })
                                      .catch(() => { saving = false; message = 'An error occurred.'; })">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-secondary" :disabled="saving">
                                    <span x-show="!saving">Update password</span>
                                    <span x-show="saving" x-cloak>Saving...</span>
                                </button>
                            </form>
                            <p x-show="message" x-text="message" class="mt-2 text-sm text-graymatter-green" x-cloak></p>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->hasPermission('users.edit'))
                <div class="mb-8">
                    <label for="role" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Role</label>
                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                    >
                        <option value="">Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role', $userRoleIds[0] ?? null) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex gap-4">
                <button type="submit" class="btn btn-primary">
                    Update User
                </button>
                <a href="{{ route('users.show', $user) }}" class="btn bg-graymatter-panel-light text-text hover:bg-divider no-underline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
