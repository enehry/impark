<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title inertia>{{ config('app.name', 'Laravel') }}</title>
  <link rel="icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon" />

  <!-- Styles -->
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">

  <!-- Scripts -->
  @routes
  <script src="{{ mix('js/app.js') }}" defer></script>
  @inertiaHead
</head>

<body class="font-sans antialiased">
  @inertia

  @env ('local')
  <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
  @endenv
</body>

</html>