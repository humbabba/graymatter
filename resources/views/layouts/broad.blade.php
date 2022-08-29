<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-tertiary text-sm">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('view_title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/fontawesome/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}?v={{ getVersion() }}" rel="stylesheet">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

</head>
<body class="text-primary max-h-full m-0">
    <div id="app">
        <nav class="main bg-primary text-secondary flex flex-row flex-nowrap justify-between content-center text-lg h-10 px-2">
          @include('partials.nav')
        </nav>

        <main class="bg-white">
          <p class="text-red-900 p-[8px]">Testing</p>
          {{-- Begin messages --}}
          @include('partials.messages')
          {{-- End messages --}}

          @yield('content.broad')
        </main>

        <footer>
          @include('partials.footer')
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v={{ getVersion() }}"></script>

</body>
</html>
