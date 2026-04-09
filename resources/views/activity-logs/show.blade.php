<x-layouts.app>
    <div class="p-4 md:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-8">
            <h1>Activity Log Detail</h1>
            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                Back to Activity Log
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-graymatter-panel-light rounded-sm p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-3 h-8 bg-graymatter-teal rounded-none"></div>
                    <h2>Activity Details</h2>
                </div>
                <dl class="space-y-4">
                    <div class="flex items-center">
                        <dt class="font-semibold text-graymatter-lime w-36 uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Date/Time</dt>
                        <dd>
                            {{ $activityLog->created_at->format('M j, Y g:i A') }}
                            <span class="text-text-muted">({{ $activityLog->created_at->diffForHumans() }})</span>
                        </dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="font-semibold text-graymatter-lime w-36 uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Action</dt>
                        <dd>
                            <span class="badge {{ $activityLog->getActionColorClass() }}">
                                {{ ucfirst($activityLog->action) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="font-semibold text-graymatter-lime w-36 uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Model Type</dt>
                        <dd>
                            <span class="badge badge-teal">
                                {{ $activityLog->getModelName() }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="font-semibold text-graymatter-lime w-36 uppercase text-sm tracking-wider" style="font-family: var(--font-display);">Model</dt>
                        <dd>
                            @if($activityLog->loggableExists())
                                @php
                                    $modelRoutes = [
                                        'Node' => 'nodes.show',
                                        'User' => 'users.show',
                                        'Wave' => 'waves.show',
                                        'Role' => 'roles.edit',
                                    ];
                                    $routeName = $modelRoutes[$activityLog->getModelName()] ?? null;
                                @endphp
                                @if($routeName)
                                    <a href="{{ route($routeName, $activityLog->loggable_id) }}" class="text-graymatter-lime hover:text-graymatter-green">
                                        {{ $activityLog->getLoggableDisplayName() }}
                                    </a>
                                @else
                                    <span class="text-graymatter-lime">{{ $activityLog->getLoggableDisplayName() }}</span>
                                @endif
                                <span class="text-text-muted">#{{ $activityLog->loggable_id }}</span>
                            @else
                                <span class="text-text-muted">{{ $activityLog->getLoggableDisplayName() }}</span>
                                <span class="text-text-muted">#{{ $activityLog->loggable_id }}</span>
                                <span class="text-graymatter-red text-xs">(deleted)</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="font-semibold text-graymatter-lime w-36 uppercase text-sm tracking-wider" style="font-family: var(--font-display);">User</dt>
                        <dd>
                            @if($activityLog->user_id)
                                @if($activityLog->userExists())
                                    <a href="{{ route('users.edit', $activityLog->user_id) }}" class="text-graymatter-teal hover:text-graymatter-lime">
                                        {{ $activityLog->user_name }}
                                    </a>
                                    <span class="text-text-muted">#{{ $activityLog->user_id }}</span>
                                @else
                                    <span class="text-text-muted">{{ $activityLog->user_name ?? 'Unknown' }}</span>
                                    <span class="text-text-muted">#{{ $activityLog->user_id }}</span>
                                    <span class="text-graymatter-red text-xs">(deleted)</span>
                                @endif
                            @else
                                <span class="text-text-muted">System</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-3 h-8 bg-graymatter-green rounded-none"></div>
                <h2>
                    @if($activityLog->action === 'updated')
                        Changes
                    @elseif($activityLog->action === 'created')
                        Created Data
                    @elseif($activityLog->action === 'run')
                        Run Details
                    @else
                        Deleted Data
                    @endif
                </h2>
            </div>

            @if($activityLog->action === 'run' && is_array($activityLog->changes))
                <div class="space-y-4">
                    @if(isset($activityLog->changes['node']))
                        <div class="bg-graymatter-panel-light rounded-sm p-4">
                            <div class="font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-2" style="font-family: var(--font-display);">
                                Step {{ $activityLog->changes['step'] ?? '?' }}: {{ $activityLog->changes['node'] }}
                            </div>
                        </div>
                    @endif

                    @if(!empty($activityLog->changes['inputs']))
                        <div class="bg-graymatter-panel-light rounded-sm p-4">
                            <div class="font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-3" style="font-family: var(--font-display);">Inputs</div>
                            <div class="space-y-2">
                                @foreach($activityLog->changes['inputs'] as $field => $value)
                                    <div class="border-l-2 border-graymatter-teal pl-4">
                                        <div class="text-graymatter-teal text-sm font-semibold">{{ str_replace('_', ' ', ucfirst($field)) }}</div>
                                        <div class="bg-graymatter-dark border border-divider rounded-sm p-3 mt-1 overflow-x-auto">
                                            <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value }}</pre>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($activityLog->changes['output']))
                        <div class="bg-graymatter-panel-light rounded-sm p-4">
                            <div class="font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-3" style="font-family: var(--font-display);">Output</div>
                            <div class="bg-graymatter-dark border border-divider rounded-sm p-3 overflow-x-auto">
                                <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ $activityLog->changes['output'] }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            @elseif($activityLog->action === 'updated' && is_array($activityLog->changes))
                <div class="space-y-4">
                    @foreach($activityLog->changes as $field => $change)
                        <div class="bg-graymatter-panel-light rounded-sm p-4">
                            <div class="font-semibold text-graymatter-teal uppercase text-sm tracking-wider mb-3" style="font-family: var(--font-display);">
                                {{ str_replace('_', ' ', ucfirst($field)) }}
                            </div>
                            @if(array_key_exists('old', $change) && array_key_exists('new', $change))
                                {{-- Simple field change --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-graymatter-red text-xs uppercase tracking-wider mb-1" style="font-family: var(--font-display);">Old Value</div>
                                        <div class="bg-graymatter-dark border border-divider rounded-sm p-3 overflow-x-auto">
                                            @if(!is_array($change['old']) && is_string($change['old']) && $change['old'] !== strip_tags($change['old']))
                                                <div class="text-sm">{!! App\Models\Project::sanitizeHtml($change['old']) !!}</div>
                                            @else
                                                <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ is_array($change['old']) ? json_encode($change['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($change['old'] ?? '(empty)') }}</pre>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-graymatter-green text-xs uppercase tracking-wider mb-1" style="font-family: var(--font-display);">New Value</div>
                                        <div class="bg-graymatter-dark border border-divider rounded-sm p-3 overflow-x-auto">
                                            @if(!is_array($change['new']) && is_string($change['new']) && $change['new'] !== strip_tags($change['new']))
                                                <div class="text-sm">{!! App\Models\Project::sanitizeHtml($change['new']) !!}</div>
                                            @else
                                                <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ is_array($change['new']) ? json_encode($change['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($change['new'] ?? '(empty)') }}</pre>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Nested array/JSON diff --}}
                                <div class="space-y-3">
                                    @foreach($change as $subKey => $subChange)
                                        <div class="border-l-2 border-graymatter-lime pl-4">
                                            <div class="text-graymatter-teal text-sm font-semibold mb-2">[{{ $subKey }}]</div>
                                            @if(is_array($subChange) && array_key_exists('old', $subChange) && array_key_exists('new', $subChange))
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <div class="text-graymatter-red text-xs uppercase tracking-wider mb-1" style="font-family: var(--font-display);">Old</div>
                                                        <div class="bg-graymatter-dark border border-divider rounded-sm p-2 overflow-x-auto">
                                                            @if(!is_array($subChange['old']) && is_string($subChange['old']) && $subChange['old'] !== strip_tags($subChange['old']))
                                                                <div class="text-sm">{!! App\Models\Project::sanitizeHtml($subChange['old']) !!}</div>
                                                            @else
                                                                <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ is_array($subChange['old']) ? json_encode($subChange['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($subChange['old'] ?? '(empty)') }}</pre>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-graymatter-green text-xs uppercase tracking-wider mb-1" style="font-family: var(--font-display);">New</div>
                                                        <div class="bg-graymatter-dark border border-divider rounded-sm p-2 overflow-x-auto">
                                                            @if(!is_array($subChange['new']) && is_string($subChange['new']) && $subChange['new'] !== strip_tags($subChange['new']))
                                                                <div class="text-sm">{!! App\Models\Project::sanitizeHtml($subChange['new']) !!}</div>
                                                            @else
                                                                <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ is_array($subChange['new']) ? json_encode($subChange['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($subChange['new'] ?? '(empty)') }}</pre>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-graymatter-dark border border-divider rounded-sm p-2 overflow-x-auto">
                                                    <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ json_encode($subChange, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-graymatter-dark border border-divider rounded-sm p-6 overflow-x-auto">
                    @if(is_array($activityLog->changes))
                        @foreach($activityLog->changes as $key => $value)
                            <div class="mb-4">
                                <div class="text-graymatter-teal text-sm font-semibold mb-1">{{ str_replace('_', ' ', ucfirst($key)) }}</div>
                                @if(is_string($value) && $value !== strip_tags($value))
                                    <div class="text-sm bg-graymatter-panel rounded-sm p-3">{!! App\Models\Project::sanitizeHtml($value) !!}</div>
                                @else
                                    <pre class="text-sm text-text font-mono whitespace-pre-wrap">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($value ?? '(empty)') }}</pre>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <pre class="text-sm text-graymatter-teal font-mono">{{ json_encode($activityLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
