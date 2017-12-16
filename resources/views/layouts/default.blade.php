<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <link rel ="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body>
  <div class="container">
    @yield('content')
  </div>
</body>
<script src="{{ mix('/js/app.js') }}"></script>
</html>

