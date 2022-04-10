<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} - Down for maintenance</title>

</head>
<body>
  <style>
    .maintenance {
      font-family: Arial,sans;
      font-size: 2rem;
      text-align: center;
    }
  </style>
  <div class="maintenance">
    <h1>Out of order</h1>
    <p>We are down for maintenance.</p>
  </div>
</body>
</html>
