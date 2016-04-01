<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>docman</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://static.luelistan.net/bootstrap-3.3.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/style/style.css">
    <script>csrf="{{ csrf_token() }}"</script>
    <script src="https://static.luelistan.net/jquery/jquery-2.1.3.min.js"></script>
    <script src="/style/helpers.js"></script>
</head>
  <body>
<header>
<div class="container">
<a href="/" class="brand">Dokumentenverwaltung</a>
 &nbsp; &nbsp; 
 @yield("toolbar")
</div>
</header>

<div class="container">
@yield("main")

<footer>

</footer>


</div>

 </body>
</html>