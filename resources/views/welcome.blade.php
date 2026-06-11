<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreeBA | Arbolado Urbano</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <header class="navbar" id="navbar">
        <a href="/" class="nav-brand">
            <div class="logo"><img src="{{ asset('img/opcion 1 cuad.png') }}" alt="logo"></div>
            <span class="brand-name">TreeBA</span>
        </a>
        <nav class="nav-links">
            <a href="/mapa" class="nav-pill">Mapa</a>
            <a href="#modificaciones" class="nav-pill">Modificaciones</a>
            <a href="#cuidados" class="nav-pill">Cuidados</a>
            <a href="#reclamos" class="nav-pill">Reclamos</a>
            <a href="#contacto" class="nav-pill">Contacto</a>
            <a href="/login" class="nav-pill btn-login">Login</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <canvas id="hero-canvas"></canvas>
            
            <div class="hero-content">
                <h1 class="hero-title">El bosque urbano<br>en tus manos</h1>
                <p class="hero-description">
                    Plataforma de ciencia ciudadana para mapear, reportar y aprender sobre el arbolado de la Ciudad de Buenos Aires.
                </p>
                <a href="/mapa" class="btn-main-cta">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" y1="3" x2="9" y2="18"></line><line x1="15" y1="6" x2="15" y2="21"></line></svg>
                    ABRIR MAPA
                </a>
            </div>
        </section>
        
        
    </main>

   

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>