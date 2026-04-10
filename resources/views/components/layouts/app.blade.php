<!DOCTYPE html>
@php
    $linkStyleSetting = \App\Models\AppSetting::get('link_style', []);
    $linkStyleAttr = is_array($linkStyleSetting) ? implode(' ', $linkStyleSetting) : '';
    $linkHoverStyleSetting = \App\Models\AppSetting::get('link_hover_style', []);
    $linkHoverStyleAttr = is_array($linkHoverStyleSetting) ? implode(' ', $linkHoverStyleSetting) : '';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full"
      data-accent="{{ \App\Models\AppSetting::get('theme_accent', 'grayscale') }}"
      data-font="{{ \App\Models\AppSetting::get('theme_font', 'inter') }}"
      data-link-color="{{ \App\Models\AppSetting::get('link_color', 'accent') }}"
      data-link-style="{{ $linkStyleAttr }}"
      data-link-hover-color="{{ \App\Models\AppSetting::get('link_hover_color', 'auto') }}"
      data-link-hover-style="{{ $linkHoverStyleAttr }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <script>
        (function() {
            @auth
                var dbTheme = @json(auth()->user()->theme);
            @endauth
            var theme = (typeof dbTheme !== 'undefined' && dbTheme) ? dbTheme : (localStorage.getItem('theme') || 'light');
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        })();
    </script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        [x-cloak] { display: none !important; }
        @php
            $fontMap = [
                'system' => [
                    'sans' => "ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
                    'display' => "ui-monospace, 'Cascadia Code', 'Segoe UI Mono', 'Roboto Mono', Menlo, monospace",
                ],
                'roboto-condensed' => [
                    'sans' => "'Source Sans 3', ui-sans-serif, system-ui, sans-serif",
                    'display' => "'Roboto Condensed', ui-sans-serif, system-ui, sans-serif",
                ],
                'titillium' => [
                    'sans' => "'Nunito Sans', ui-sans-serif, system-ui, sans-serif",
                    'display' => "'Titillium Web', ui-sans-serif, system-ui, sans-serif",
                ],
                'saira-stencil' => [
                    'sans' => "'Saira', ui-sans-serif, system-ui, sans-serif",
                    'display' => "'Saira Stencil One', ui-sans-serif, system-ui, sans-serif",
                ],
                'bitcount' => [
                    'sans' => "'Space Grotesk', ui-sans-serif, system-ui, sans-serif",
                    'display' => "'Bitcount Grid Double', ui-sans-serif, system-ui, sans-serif",
                ],
                'oswald' => [
                    'sans' => "'Lato', ui-sans-serif, system-ui, sans-serif",
                    'display' => "'Oswald', ui-sans-serif, system-ui, sans-serif",
                ],
                'ubuntu' => [
                    'sans' => "'Ubuntu Sans', ui-sans-serif, system-ui, sans-serif",
                    'display' => "'Ubuntu', ui-sans-serif, system-ui, sans-serif",
                ],
            ];
            $currentFont = \App\Models\AppSetting::get('theme_font', 'inter');
        @endphp
        @if(isset($fontMap[$currentFont]))
        :root {
            --font-sans: {!! $fontMap[$currentFont]['sans'] !!} !important;
            --font-display: {!! $fontMap[$currentFont]['display'] !!} !important;
        }
        @endif
    </style>
</head>
<body class="h-full bg-graymatter-black text-text">
    <div class="flex flex-col h-full">
        <x-nav />
        <div class="graymatter-bar"></div>

        <main class="flex-1 m-2 md:m-4 overflow-auto graymatter-panel">
            {{ $slot }}
        </main>

        <div class="graymatter-bar"></div>
        <x-footer />
    </div>
    <x-confirm-modal />

    {{-- AJAX Save Indicator --}}
    <div x-data="ajaxSave()"
         x-show="formExists && (isDirty || justSaved || isSaving)"
         x-cloak
         class="save-indicator"
         :class="{
             'save-indicator--unsaved': isDirty && !isSaving,
             'save-indicator--saved': justSaved,
             'save-indicator--saving': isSaving
         }"
         @click="save()"
         @keydown.window.ctrl.s.prevent="save()"
         @keydown.window.meta.s.prevent="save()">
        <span x-text="statusText"></span>
    </div>
</body>
</html>
