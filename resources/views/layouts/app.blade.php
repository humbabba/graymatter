<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v={{ getVersion() }}" defer></script>
    <script src="{{ asset('js/fontawesome/all.min.js') }}?v={{ getVersion() }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/fontawesome/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ getVersion() }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav>
          @include('partials.nav')
        </nav>

        <main>
          @yield('content')
        </main>

        <footer>
          @include('partials.footer')
        </footer>
    </div>
</body>
</html>
