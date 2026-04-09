<x-layouts.app>
    <div class="p-4 md:p-8 max-w-2xl">
        <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-8">
            <div class="flex items-center gap-4">
                <div class="w-3 h-10 bg-graymatter-green rounded-none"></div>
                <h1>{{ $user->name }}</h1>
            </div>
            <div class="flex gap-2 shrink-0">
                @if(auth()->id() === $user->id || (auth()->user()->hasPermission('users.edit') && !array_diff($user->roles->pluck('id')->toArray(), $assignableRoleIds)))
                    <a href="{{ route('users.edit', $user) }}" class="btn bg-graymatter-teal text-graymatter-black hover:bg-graymatter-teal/80 no-underline">
                        Edit Profile
                    </a>
                @endif
                @if(auth()->user()->hasPermission('users.view'))
                    <a href="{{ route('users.index') }}" class="btn bg-graymatter-panel-light text-text hover:bg-divider no-underline">
                        Back to Users
                    </a>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-graymatter-green/20 border border-graymatter-green/50 text-graymatter-green rounded-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-graymatter-panel-light rounded-sm p-6">
            <div class="flex items-start gap-6">
                <x-avatar :user="$user" :size="12" />

                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-1" style="font-family: var(--font-display);">Name</label>
                        <p class="text-text">{{ $user->name }}</p>
                    </div>

                    @if(auth()->user()?->hasPermission('users.edit'))
                        <div>
                            <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-1" style="font-family: var(--font-display);">Email</label>
                            <p class="text-text-muted">{{ $user->email }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-1" style="font-family: var(--font-display);">Role</label>
                        <div>
                            @foreach($user->roles as $role)
                                <span class="badge badge-teal mr-1">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-1" style="font-family: var(--font-display);">Last Login</label>
                        <p class="text-text-muted">{{ $user->last_login_at ? $user->last_login_at->format('F j, Y g:i A') : 'Never' }}</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-1" style="font-family: var(--font-display);">Member Since</label>
                        <p class="text-text-muted">{{ $user->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
