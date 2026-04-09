<x-layouts.app>
    <script>
        (function() {
            var pref = localStorage.getItem('projects_show_mine');
            if (pref === '1' && !new URLSearchParams(window.location.search).has('mine')) {
                var url = new URL(window.location);
                url.searchParams.set('mine', '1');
                window.location.replace(url);
            }
        })();
    </script>
    <div class="p-4 md:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-8">
            <h1>Projects</h1>
            <div class="flex gap-2 items-center">
                @auth
                    <a href="{{ route('projects.index', array_merge(request()->except('mine'), $showMine ? [] : ['mine' => 1])) }}"
                       class="btn btn-secondary whitespace-nowrap"
                       x-data
                       x-on:click="localStorage.setItem('projects_show_mine', '{{ $showMine ? '0' : '1' }}')">
                        {{ $showMine ? 'All projects' : 'My projects' }}
                    </a>
                @endauth
                @if(auth()->user()?->hasPermission('projects.create'))
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        Add project
                    </a>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-graymatter-green/20 border border-graymatter-green/50 text-graymatter-green rounded-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-graymatter-red/20 border border-graymatter-red/50 text-graymatter-red rounded-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 p-4 bg-graymatter-panel-light rounded-sm">
            <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap gap-4 items-end">
                @if($showMine)
                    <input type="hidden" name="mine" value="1">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
                <div>
                    <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-2" style="font-family: var(--font-display);">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                           class="bg-graymatter-panel border border-divider rounded-sm px-4 py-2 text-text min-w-[200px]">
                </div>
                <div>
                    <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-2" style="font-family: var(--font-display);">From</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                           class="bg-graymatter-panel border border-divider rounded-sm px-4 py-2 text-text">
                </div>
                <div>
                    <label class="block font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-2" style="font-family: var(--font-display);">To</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                           class="bg-graymatter-panel border border-divider rounded-sm px-4 py-2 text-text">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                    @if(request()->hasAny(['search', 'from', 'to', 'sort']))
                        <a href="{{ route('projects.index', $showMine ? ['mine' => 1] : []) }}" class="btn btn-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-sm border border-divider">
            <table class="w-full">
                <thead>
                    <tr>
                        <x-sort-header column="name" label="Name" :sort="$sort" :direction="$direction" />
                        <x-sort-header column="created_at" label="Created" :sort="$sort" :direction="$direction" />
                        <th class="p-4 text-left border-b border-divider w-80">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr class="hover:bg-graymatter-panel-light transition-colors">
                            <td class="p-4 border-b border-divider/50">
                                <a href="{{ route('projects.show', $project) }}" class="text-graymatter-green hover:text-graymatter-lime font-semibold">{{ $project->name }}</a>
                                @if($project->description)
                                    <p class="text-text-muted text-xs mt-1 line-clamp-1">{{ $project->description }}</p>
                                @endif
                            </td>
                            <td class="p-4 border-b border-divider/50 text-text-muted">
                                {{ $project->created_at->format('M j, Y') }}
                            </td>
                            <td class="p-4 border-b border-divider/50">
                                <div class="flex gap-2">
                                    <a href="{{ route('projects.show', $project) }}" class="px-3 py-1.5 rounded-none bg-graymatter-panel-light text-graymatter-teal hover:bg-graymatter-teal hover:text-graymatter-black transition-all text-xs uppercase tracking-wider no-underline" style="font-family: var(--font-display);">
                                        View
                                    </a>
                                    @if(auth()->user()?->hasPermission('projects.edit'))
                                        <a href="{{ route('projects.edit', $project) }}" class="px-3 py-1.5 rounded-none bg-graymatter-panel-light text-graymatter-teal hover:bg-graymatter-teal hover:text-graymatter-black transition-all text-xs uppercase tracking-wider no-underline" style="font-family: var(--font-display);">
                                            Edit
                                        </a>
                                    @endif
                                    @if(auth()->user()?->hasPermission('projects.create'))
                                        <form method="POST" action="{{ route('projects.copy', $project) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-none bg-graymatter-panel-light text-graymatter-lime hover:bg-graymatter-lime hover:text-graymatter-black transition-all text-xs uppercase tracking-wider" style="font-family: var(--font-display);">
                                                Copy
                                            </button>
                                        </form>
                                    @endif
                                    @if(auth()->user()?->hasPermission('projects.edit'))
                                        <form method="POST" action="{{ route('projects.destroy', $project) }}" id="delete-project-{{ $project->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                x-data
                                                class="px-3 py-1.5 rounded-none bg-graymatter-red text-white hover:bg-graymatter-red/80 transition-all text-xs uppercase tracking-wider"
                                                style="font-family: var(--font-display);"
                                                x-on:click="$dispatch('confirm-delete', {
                                                    title: 'Delete project',
                                                    message: 'Are you sure you want to delete project \'' + {{ Js::from($project->name) }} + '\'? This will move it to the trash.',
                                                    formId: 'delete-project-{{ $project->id }}'
                                                })"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-text-muted">
                                No projects found. Create your first project to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    </div>
</x-layouts.app>
