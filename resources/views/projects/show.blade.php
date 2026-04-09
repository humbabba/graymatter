<x-layouts.app>
    <div class="p-4 md:p-8 max-w-3xl mx-auto">
        @auth
            @if($project->canManage(auth()->user()))
                <div class="flex justify-end mb-4">
                    <a href="{{ route('projects.edit', $project) }}" class="text-text-muted hover:text-graymatter-teal text-xs uppercase tracking-wider no-underline transition-colors" style="font-family: var(--font-display);">Edit</a>
                </div>
            @endif
        @endauth

        @if($project->content)
            <div>
                {!! $project->content !!}
            </div>
        @endif
    </div>
</x-layouts.app>
