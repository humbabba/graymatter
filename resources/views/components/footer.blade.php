<footer class="bg-graymatter-dark border-t border-divider">
    <div class="flex items-center justify-between gap-4 min-h-10 px-4 md:px-6 py-2 text-xs text-text-muted" style="font-family: var(--font-display);">
        <div class="flex items-center gap-6">
            <span class="text-graymatter-green">v{{ config('app.version', '1.00.00') }}</span>
            <a href="{{ route('manual') }}" class="text-graymatter-teal hover:text-graymatter-lime" target="_blank">User Manual</a>
        </div>
        <div class="flex items-center gap-6">
            @php
                $pubYear = (int) \App\Models\AppSetting::get('publication_year', '0000');
                $currentYear = (int) date('Y');
                $yearDisplay = ($pubYear > 0 && $pubYear < $currentYear) ? "{$pubYear} - {$currentYear}" : (string) $currentYear;
            @endphp
            <span>&copy; {{ $yearDisplay }} <a href="https://sublogicalendeavors.com/" target="_blank" class="text-graymatter-teal hover:text-graymatter-lime">Sublogical Endeavors</a></span>
        </div>
    </div>
</footer>
