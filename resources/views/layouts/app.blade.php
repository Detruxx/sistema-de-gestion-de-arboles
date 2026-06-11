<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TreeBA | Arbolado Urbano')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    @stack('styles') <!-- Para estilos extra como Leaflet -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="@yield('body-class')">
    
    <!-- Aca incluiremos el navbar -->
    <!-- Esto hace que el navbar sea dinamico y se pueda cambiar segun la pagina -->
    <x-navbar class="@yield('navbar-class')" />


    <!-- Aca se inyectara el contenido de otras vistas -->
    @yield('content')

    @stack('scripts') <!-- Para scripts específicos de cada página -->
</body>
</html>
