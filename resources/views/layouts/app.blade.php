<!-- Plantilla principal de la pagina web -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Aca va el icono de la pagina -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>@yield('title', 'TreeBA | Mapeado de Arboles')</title>

    <!-- Aca van las fuentes que usa la pagina -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    @yield('styles') <!-- Aca se colocan estilos especificos de cada vista -->
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=1.1"> <!-- Aca va el css de la pagina -->
    <link rel="stylesheet" href="{{ asset('css/shared/modal.css') }}">
</head>
<body class="@yield('body-class')">
    @yield('canvas') <!-- Aca va el canvas de la pagina -->

    <header class="navbar @yield('navbar-class')" id="navbar"> <!-- Aca va la barra de navegacion de la pagina -->
        <a href="/" class="nav-brand">
            <div class="logo"><img src="{{ asset('img/logo.png') }}" alt="logo"></div>
            <span class="brand-name">TreeBA</span>
        </a>
        
        <button class="nav-toggle" id="nav-toggle" aria-label="Abrir menú" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="nav-links" id="nav-links">
            <a href="/mapa" class="nav-pill @yield('active-mapa')">Mapa</a>
            <a href="/cuidados" class="nav-pill @yield('active-cuidados')">Cuidados</a>
            
            <div class="nav-dropdown">
                <button class="nav-pill dropdown-trigger @yield('active-tramites')" aria-expanded="false">
                    Trámites
                    <svg class="dropdown-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="dropdown-menu">
                    <a href="/tramites/reclamos" class="@yield('active-reclamos')">Reclamos</a>
                    <a href="/tramites/plantacion" class="@yield('active-plantacion')">Plantación</a>
                    <a href="/tramites/permisos" class="@yield('active-permisos')">Permisos</a>
                </div>
            </div>
            
            @guest
                <a href="/#sobre-nosotros" class="nav-pill">Sobre Nosotros</a>
                <a href="/#contacto" class="nav-pill">Contacto</a>
            @else
                @php $role = Auth::user()->role; @endphp
                @if($role === 'inspector')
                    <a href="/dashboard/inspector#reclamos" class="nav-pill">Mensajes de Reclamos</a>
                    <a href="/mensajes" class="nav-pill">Mensajes</a>
                @elseif($role === 'admin')
                    <div class="nav-dropdown">
                        <button class="nav-pill dropdown-trigger" aria-expanded="false">
                            Usuarios
                            <svg class="dropdown-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/dashboard/admin#vecinos">Gestión de Vecinos</a>
                            <a href="/dashboard/admin#inspectores">Inspectores</a>
                        </div>
                    </div>
                    <a href="/dashboard/admin#estadisticas" class="nav-pill">Estadísticas</a>
                @elseif($role === 'empresa')
                    <a href="/dashboard/empresa#trabajos" class="nav-pill">Trabajos</a>
                    <a href="/dashboard/empresa#pagos" class="nav-pill">Pagos</a>
                @else
                    <!-- Vecino (default) -->
                    <a href="/#sobre-nosotros" class="nav-pill">Sobre Nosotros</a>
                    <a href="/#contacto" class="nav-pill">Contacto</a>
                @endif
            @endguest
            @guest <!-- Si el usuario no esta logueado, se muestra el boton de login -->
                <a href="/login" class="nav-pill btn-login @yield('active-login')">Login</a>
            @endguest
            @auth <!-- Si el usuario esta logueado, se muestra el menu de perfil -->
                @php
                    // TODO (Backend): Consultar notificaciones reales a la BD
                    $unreadClaimsCount = 2; // Número de notificaciones no leídas en Mis Reclamos
                    $unreadMessagesCount = 0; // Número de notificaciones de Mensajes
                        
                    $hasAnyNotification = ($unreadClaimsCount > 0 || $unreadMessagesCount > 0);
                @endphp
                <div class="nav-dropdown">
                    <button class="nav-pill dropdown-trigger" aria-expanded="false" style="position: relative; background: none; border: 1px solid transparent; padding: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 50%; color: var(--paper-white); overflow: visible;" title="Perfil de {{ Auth::user()->name }}">
                        <!-- Icono SVG de persona (cabeza y cuerpo) o imagen del avatar -->
                        <span id="nav-avatar-container" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; border-radius: 50%; overflow: hidden;">
                            <svg id="nav-avatar-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <img id="nav-avatar-img" src="" alt="Avatar" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                        </span>
                        <!-- Punto rojo de notificacion general -->
                        @if(Auth::user()->role === 'vecino' && $hasAnyNotification)
                            <x-layouts.notification-badge id="badge-global-dot" isDot="true" position="absolute" top="-2px" right="-2px" />
                        @endif
                    </button>
                    <div class="dropdown-menu">
                        <a href="/configuracion">Mi Perfil</a>
                        @if(in_array(Auth::user()->role, ['vecino', 'admin', 'inspector']))
                            <a href="/mis-reclamos" style="display: flex; justify-content: space-between; align-items: center;">
                                Mis Reclamos
                                @if($unreadClaimsCount > 0)
                                    <x-layouts.notification-badge id="badge-unread-claims" :count="$unreadClaimsCount" />
                                @endif
                            </a>
                        @endif
                        @if(Auth::user()->role === 'vecino')
                            <a href="/bandeja-entrada" style="display: flex; justify-content: space-between; align-items: center;">
                                Bandeja de Entrada
                                @if($unreadMessagesCount > 0)
                                    <x-layouts.notification-badge id="badge-unread-messages" :count="$unreadMessagesCount" />
                                @endif
                            </a>
                        @elseif(Auth::user()->role === 'empresa')
                            <a href="/dashboard/empresa">Panel de Empresa</a>
                        @elseif(Auth::user()->role === 'inspector')
                            <a href="/dashboard/inspector">Panel de Control</a>
                        @else
                            <a href="/dashboard/admin">Panel de Control</a>
                        @endif
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="border-top: 1px solid rgba(45, 122, 79, 0.15); color: #d32f2f; display: flex; justify-content: center; align-items: center; gap: 8px;">
                            <span>Cerrar Sesión</span>
                            <img src="{{ asset('img/buttons/logout_icon_red.webp') }}" alt="Cerrar Sesión" style="width: 18px; height: auto;">
                        </a>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endauth
        </nav>
    </header>

    @yield('content') <!-- Aca va el contenido de la pagina el cual es propio de cada pagina-->

    @section('footer') <!-- Aca va el footer de la pagina -->
    <footer class="main-footer" style="position: relative; z-index: 20; background-color: var(--forest-night, #203528);">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo"><img src="{{ asset('img/logo.png') }}" alt="logo"></div>
                    <span class="brand-name">TreeBA</span>
                </div>
                <p class="footer-tagline">Mapeando el futuro verde de la ciudad.</p>
                <p class="footer-source">Datos abiertos obtenidos de BA Data - GCBA.</p>
                <p class="footer-source" style="margin-top: 5px; opacity: 0.85;">Basado en protocolos estandarizados de Espacio Público.</p>
            </div>
            
            <div class="footer-links">
                <h4>Navegación</h4>
                <ul>
                    <li><a href="/">Inicio</a></li>
                    <li><a href="/mapa">Mapa Interactivo</a></li>
                    <li><a href="/cuidados">Cuidados del Árbol</a></li>
                    <li><a href="/#sobre-nosotros">Sobre Nosotros</a></li>
                    @auth
                        @if(Auth::user()->role === 'inspector' || Auth::user()->role === 'admin')
                            <li><a href="/mensajes">Mensajes</a></li>
                        @else
                            <li><a href="/#contacto">Contacto</a></li>
                        @endif
                    @else
                        <li><a href="/#contacto">Contacto</a></li>
                    @endauth
                    @guest
                        <li><a href="/login">Login</a></li>
                    @endguest
                    @auth
                        <li><a href="/configuracion">Configuración</a></li>
                        @if(in_array(Auth::user()->role, ['vecino', 'admin', 'inspector']))
                            <li><a href="/mis-reclamos">Mis Reclamos</a></li>
                        @endif
                        @if(Auth::user()->role === 'empresa')
                            <li><a href="/dashboard/empresa">Panel de Empresa</a></li>
                        @elseif(Auth::user()->role === 'inspector')
                            <li><a href="/dashboard/inspector">Panel de Control</a></li>
                        @elseif(Auth::user()->role === 'admin')
                            <li><a href="/dashboard/admin">Panel de Control</a></li>
                        @endif
                        <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #d32f2f;">Cerrar Sesión</a></li>
                    @endauth
                </ul>
            </div>

            <div class="footer-links">
                <h4>Trámites</h4>
                <ul>
                    <li><a href="/tramites/reclamos">Reclamos y Solicitudes</a></li>
                    <li><a href="/tramites/plantacion">Solicitar Plantación</a></li>
                    <li><a href="/tramites/permisos">Permisos de Poda</a></li>
                    <li><a href="/postulacion-empresa" style="font-weight: 600; color: var(--spring-leaf);">¡Postúlate como Empresa!</a></li>
                </ul>
            </div>
            
            <div class="footer-social">
                <h4>Contacto</h4>
                <ul class="footer-contact-list">
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span>Línea 147 (GCBA)</span>
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span>contacto@treeba.gob.ar</span>
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>Av. Cabildo 3067, CABA</span>
                    </li>
                </ul>
                <div class="social-icons" style="margin-top: 15px;">
                    <a href="https://github.com" target="_blank" aria-label="GitHub">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                    </a>
                    <a href="https://instagram.com" target="_blank" aria-label="Instagram">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </a>
                    <a href="https://twitter.com" target="_blank" aria-label="Twitter">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 TreeBA. Creado con <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#5bbf8c" stroke="#5bbf8c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin: 0 3px; position: relative; top: -1px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg> para la Ciudad de Buenos Aires.</p>
        </div>
    </footer>
    @show

    @yield('scripts') <!-- Aca van los scripts de cada vista -->
    <script src="{{ asset('js/shared/navbar.js') }}"></script> <!-- script de la barra de navegacion -->
    <script src="{{ asset('js/shared/reveal.js') }}"></script> <!-- script de revelacion de elementos -->
    <!-- Modal de Éxito Global -->
    <div id="success-modal" class="address-map-modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 9999; background: rgba(0, 0, 0, 0.7);">
        <div class="address-map-modal-container" style="background-color: var(--paper-white); max-width: 400px; text-align: center; padding: 40px 30px; border-radius: 20px;">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--spring-leaf)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px;">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <h3 id="success-modal-title" style="color: var(--deep-canopy); margin-bottom: 15px; font-family: var(--font-display); font-size: 1.8rem;">¡Éxito!</h3>
            <p id="success-modal-message" style="color: var(--forest-night); margin-bottom: 30px; font-size: 1.1rem; line-height: 1.5;"></p>
            <button type="button" onclick="closeSuccessModal()" class="btn-main-cta">Aceptar</button>
        </div>
    </div>

    <script>
        window.showSuccessModal = function(title, message) {
            document.getElementById('success-modal-title').textContent = title;
            document.getElementById('success-modal-message').textContent = message;
            document.getElementById('success-modal').style.display = 'flex';
        }
        
        window.closeSuccessModal = function() {
            document.getElementById('success-modal').style.display = 'none';
        }

        window.togglePasswordVisibility = function(inputId, btn) {
            const input = document.getElementById(inputId);
            const svg = btn.querySelector('svg');
            if (input.type === 'password') {
                input.type = 'text';
                // Ícono de ojo tachado (eye-off)
                svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                // Ícono de ojo normal (eye)
                svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
</body>
</html>

