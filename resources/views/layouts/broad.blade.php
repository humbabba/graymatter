<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('view_title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/fontawesome/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}?v={{ getVersion() }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="main bg-primary text-secondary flex flex-row flex-nowrap justify-between content-center text-lg h-10 px-10">
          @include('partials.nav')
        </nav>

        <main>
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
