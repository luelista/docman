<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>docman</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ url('style/css/bootstrap-yeti-theme.min.css') }}">
    <link rel="stylesheet" href="{{ url('style/style.css') }}">
    <script>csrf="{{ csrf_token() }}"</script>
    <script src="https://static.luelista.net/jquery/jquery-2.1.3.min.js"></script>
    <script src="{{ url('style/helpers.js') }}"></script>
</head>
  <body>
<nav class="navbar navbar-default">
<div class="container">
<div class="navbar-header"><a href="/" class="navbar-brand">Dokumentenverwaltung</a></div>

 @yield("toolbar")
</div>
</nav>

<div class="container">
@yield("main")

<footer>

</footer>


</div>

 </body>
</html>
