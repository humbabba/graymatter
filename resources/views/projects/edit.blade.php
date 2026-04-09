<x-layouts.app>
    <div class="p-4 md:p-8 max-w-4xl">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-3 h-10 bg-graymatter-teal rounded-none"></div>
            <h1>Edit project</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-graymatter-red/20 border border-graymatter-red/50 text-graymatter-red rounded-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 p-4 bg-graymatter-green/20 border border-graymatter-green/50 text-graymatter-green rounded-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('projects.update', $project) }}" data-ajax-save class="bg-graymatter-panel-light rounded-sm p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $project->name) }}"
                    required
                    autofocus
                    class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                >
            </div>

            <div class="mb-6">
                <label for="description" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    class="w-full p-3 bg-graymatter-panel border border-divider rounded-sm text-text focus:border-graymatter-teal"
                >{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label for="content" class="block mb-2 font-semibold text-graymatter-teal uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Content</label>
                <x-rich-editor id="content" name="content" :value="old('content', $project->content)" />
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <button type="submit" class="btn btn-primary">
                    Update project
                </button>
                <a href="{{ route('projects.show', $project) }}" class="btn bg-graymatter-panel text-text hover:bg-divider no-underline text-center">
                    View
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
