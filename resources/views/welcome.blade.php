<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <link rel="icon" type="image/svg+xml" href="/logo.svg" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Task manager для бухгалтерии</title>
  @vite('resources/css/app.css')
  <!-- <script src="//api.bitrix24.com/api/v1/"></script> -->
</head>

<body>
  <script>
    let user = @json($user);
    // console.log(user)
    window.user = user
  </script>

  <div id="app"></div>
  @vite('resources/js/app.js')
</body>

</html>