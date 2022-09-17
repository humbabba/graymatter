<div x-data="dropdown">
    <button x-bind="trigger">Show</button>

    <div x-bind="toggle" x-transition:opacity.duration.500ms style="display: none">Me</div>
</div>
