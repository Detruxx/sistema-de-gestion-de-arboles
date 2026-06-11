<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa | TreeBA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="map-body">

    <header class="navbar scrolled" id="navbar">
        <a href="/" class="nav-brand">
            <div class="logo"><img src="{{ asset('img/logo1.png') }}" alt="logo"></div>
            <span class="brand-name">TreeBA</span>
        </a>
        <nav class="nav-links">
            <a href="/mapa" class="nav-pill active">Mapa</a>
            <a href="/#modificaciones" class="nav-pill">Modificaciones</a>
            <a href="/#cuidados" class="nav-pill">Cuidados</a>
            <a href="/#reclamos" class="nav-pill">Reclamos</a>
            <a href="/#contacto" class="nav-pill">Contacto</a>
            <a href="/login" class="nav-pill btn-login">Login</a>
        </nav>
    </header>

    <main class="map-page-container">
        <div class="map-wrapper">
            
            <aside id="tree-sidebar" class="sidebar-closed">
                <button id="toggle-sidebar" class="toggle-btn">
                    <svg id="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <div class="sidebar-content">
                    <div class="sidebar-top">
                        <div class="badges-col">
                            <div class="info-badge">
                                <span class="badge-label">ID</span>
                                <span id="t-id">#0000</span>
                            </div>
                            <div class="info-badge">
                                <span class="badge-label">ESTADO</span>
                                <span id="t-estado" class="status-good">Saludable</span>
                            </div>
                        </div>
                        <div class="photo-col">
                            <img id="t-foto" src="https://via.placeholder.com/150" alt="Foto del árbol">
                        </div>
                    </div>

                    <div class="sidebar-bottom">
                        <h4 class="data-title">DATOS DEL ARBOL</h4>
                        <ul class="data-list">
                            <li><strong>Especie:</strong> <span id="t-especie">-</span></li>
                            <li><strong>Plantado/Años:</strong> <span id="t-edad">-</span></li>
                            <li><strong>Cantidad de reclamos:</strong> <span id="t-reclamos">-</span></li>
                        </ul>
                    </div>
                </div>
            </aside>

            <div id="tree-map"></div>
            
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>