<x-layouts.app>
    <div class="p-4 md:p-8 max-w-2xl">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-3 h-10 bg-graymatter-green rounded-none"></div>
            <h1>Add User</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-graymatter-red/20 border border-graymatter-red/50 text-graymatter-red rounded-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}" class="bg-graymatter-panel-light rounded-sm p-6">
            @csrf

            <div class="mb-6">
                <label for="name" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
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
                    value="{{ old('email') }}"
                    required
                    class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                >
            </div>

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
                        <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="btn btn-primary">
                    Create User
                </button>
                <a href="{{ route('users.index') }}" class="btn bg-graymatter-panel-light text-text hover:bg-divider no-underline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
