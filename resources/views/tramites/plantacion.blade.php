@extends('layouts.app')

@section('title', 'Solicitud de Plantación | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-plantacion', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tramites/plantacion.css') }}?v={{ filemtime(public_path('css/tramites/plantacion.css')) }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endsection

@section('content')
    <main class="tramites-page-container" style="position: relative; overflow: hidden;">
        <!-- Fondo de Bosque Urbano Line-Art (Más detallado) -->
        <div class="lineart-bg lineart-skyline">
            <svg viewBox="0 0 1440 350" preserveAspectRatio="xMidYMax slice" class="skyline-svg" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <!-- Gradientes para la madera y ciudad (Marrón cálido acuarelado con bordes nítidos) -->
                    <linearGradient id="wood-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#452718" stop-opacity="0.65" />
                        <stop offset="50%" stop-color="#78350f" stop-opacity="0.55" />
                        <stop offset="100%" stop-color="#b45309" stop-opacity="0.25" />
                    </linearGradient>
                    <linearGradient id="wood-grad-light" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#8b5a2b" stop-opacity="0.45" />
                        <stop offset="100%" stop-color="#d2b48c" stop-opacity="0.15" />
                    </linearGradient>
                    
                    <linearGradient id="city-grad-1" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#8b5a2b" stop-opacity="0.30" />
                        <stop offset="100%" stop-color="#d2b48c" stop-opacity="0.08" />
                    </linearGradient>
                    <linearGradient id="city-grad-2" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#5c4033" stop-opacity="0.35" />
                        <stop offset="100%" stop-color="#8b5a2b" stop-opacity="0.10" />
                    </linearGradient>
                    <linearGradient id="city-grad-3" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#b58a57" stop-opacity="0.28" />
                        <stop offset="100%" stop-color="#c29d70" stop-opacity="0.08" />
                    </linearGradient>

                    <!-- Gradientes radiales para las hojas (Efecto manchas de acuarela nítidas) -->
                    <radialGradient id="leaf-grad-1" cx="35%" cy="35%" r="65%">
                        <stop offset="0%" stop-color="#144d29" stop-opacity="0.60" />
                        <stop offset="60%" stop-color="#166534" stop-opacity="0.45" />
                        <stop offset="100%" stop-color="#15803d" stop-opacity="0.12" />
                    </radialGradient>
                    <radialGradient id="leaf-grad-2" cx="35%" cy="35%" r="65%">
                        <stop offset="0%" stop-color="#166534" stop-opacity="0.62" />
                        <stop offset="65%" stop-color="#1e824c" stop-opacity="0.48" />
                        <stop offset="100%" stop-color="#2ecc71" stop-opacity="0.15" />
                    </radialGradient>
                    <radialGradient id="leaf-grad-3" cx="35%" cy="35%" r="65%">
                        <stop offset="0%" stop-color="#2d7a4f" stop-opacity="0.55" />
                        <stop offset="70%" stop-color="#4ade80" stop-opacity="0.38" />
                        <stop offset="100%" stop-color="#a3e635" stop-opacity="0.10" />
                    </radialGradient>
                    <radialGradient id="leaf-grad-4" cx="35%" cy="35%" r="65%">
                        <stop offset="0%" stop-color="#854d0e" stop-opacity="0.45" />
                        <stop offset="70%" stop-color="#ca8a04" stop-opacity="0.30" />
                        <stop offset="100%" stop-color="#eab308" stop-opacity="0.08" />
                    </radialGradient>

                    <!-- Gradiente para flores silvestres -->
                    <radialGradient id="blossom-grad-yellow" cx="40%" cy="40%" r="60%">
                        <stop offset="0%" stop-color="#fef08a" stop-opacity="0.95" />
                        <stop offset="60%" stop-color="#facc15" stop-opacity="0.80" />
                        <stop offset="100%" stop-color="#eab308" stop-opacity="0.40" />
                    </radialGradient>
                    <radialGradient id="blossom-grad-pink" cx="40%" cy="40%" r="60%">
                        <stop offset="0%" stop-color="#fff1f2" stop-opacity="0.95" />
                        <stop offset="50%" stop-color="#fecdd3" stop-opacity="0.80" />
                        <stop offset="100%" stop-color="#fda4af" stop-opacity="0.45" />
                    </radialGradient>

                    <!-- Gradiente para mariposas (Naranja y amarillo vibrantes) -->
                    <linearGradient id="bf-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ea580c" stop-opacity="0.85" />
                        <stop offset="100%" stop-color="#eab308" stop-opacity="0.75" />
                    </linearGradient>

                    <!-- Gradiente de luz para faroles -->
                    <radialGradient id="glow-grad" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.35" />
                        <stop offset="50%" stop-color="#f59e0b" stop-opacity="0.15" />
                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.0" />
                    </radialGradient>

                    <!-- Gradiente para nubes -->
                    <radialGradient id="cloud-grad" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#cbd5e1" stop-opacity="0.25" />
                        <stop offset="70%" stop-color="#e2e8f0" stop-opacity="0.08" />
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0.0" />
                    </radialGradient>

                    <!-- Hojas Planas Translúcidas con Borde de Agua -->
                    <g id="watercolor-leaf-1">
                        <path d="M0,0 C12,-15 28,-10 32,5 C30,20 10,22 0,0 Z" fill="url(#leaf-grad-1)" stroke="#14532d" stroke-width="1.2" stroke-opacity="0.5" />
                    </g>
                    <g id="watercolor-leaf-2">
                        <path d="M0,0 C10,-12 25,-15 28,-2 C25,12 8,15 0,0 Z" fill="url(#leaf-grad-2)" stroke="#166534" stroke-width="1.2" stroke-opacity="0.5" />
                    </g>
                    <g id="watercolor-leaf-3">
                        <path d="M0,0 C15,-8 30,-3 32,10 C18,16 5,12 0,0 Z" fill="url(#leaf-grad-3)" stroke="#1b5e20" stroke-width="1.0" stroke-opacity="0.4" />
                    </g>

                    <!-- Semilla flotante de acuarela -->
                    <g id="watercolor-seed">
                        <circle cx="10" cy="-10" r="10" fill="url(#leaf-grad-3)" opacity="0.5" />
                        <line x1="0" y1="0" x2="10" y2="-10" stroke="#8b7355" stroke-width="0.8" opacity="0.5" />
                        <path d="M10,-10 C8,-14 6,-16 4,-18 M10,-10 C9,-15 9,-17 9,-20" stroke="#8b7355" stroke-width="0.6" fill="none" opacity="0.5" />
                    </g>

                    <!-- Flores Silvestres del Parque (Manchas de color vivo) -->
                    <g id="watercolor-flower-yellow">
                        <circle cx="0" cy="0" r="4.5" fill="url(#blossom-grad-yellow)" stroke="#d97706" stroke-width="0.6" stroke-opacity="0.5" />
                        <circle cx="0" cy="0" r="1.5" fill="#ca8a04" />
                    </g>
                    <g id="watercolor-flower-pink">
                        <circle cx="0" cy="0" r="5" fill="url(#blossom-grad-pink)" stroke="#e11d48" stroke-width="0.6" stroke-opacity="0.5" />
                        <circle cx="0" cy="0" r="1.5" fill="#db2777" />
                    </g>

                    <!-- Mariposa Acuarela -->
                    <g id="watercolor-butterfly">
                        <path d="M0,0 C4,-8 12,-8 10,0 C8,4 4,2 0,0 Z" fill="url(#bf-grad)" stroke="#b45309" stroke-width="0.8" stroke-opacity="0.6" />
                        <path d="M0,0 C4,8 10,6 8,2 C6,0 3,0 0,0 Z" fill="url(#bf-grad)" stroke="#b45309" stroke-width="0.6" stroke-opacity="0.5" />
                        <path d="M0,0 C-4,-8 -12,-8 -10,0 C-8,4 -4,2 0,0 Z" fill="url(#bf-grad)" stroke="#b45309" stroke-width="0.8" stroke-opacity="0.6" transform="scale(-1, 1)" />
                        <path d="M0,0 C-4,8 -10,6 -8,2 C-6,0 -3,0 0,0 Z" fill="url(#bf-grad)" stroke="#b45309" stroke-width="0.6" stroke-opacity="0.5" transform="scale(-1, 1)" />
                        <line x1="0" y1="4" x2="0" y2="-6" stroke="#451a03" stroke-width="1.2" stroke-linecap="round" opacity="0.8" />
                    </g>

                    <!-- Árbol Tipo 1 (Roble Frondoso - Acuarela Definida) -->
                    <g id="watercolor-tree-1">
                        <!-- Tronco -->
                        <path d="M-5,0 L-3,-25 Q-8,-40 -18,-50 M5,0 L3,-25 Q8,-40 18,-48 M0,0 L0,-30" stroke="url(#wood-grad)" stroke-width="4.5" fill="none" stroke-linecap="round" />
                        <!-- Copa de Hojas con Bordes de Agua -->
                        <circle cx="-25" cy="-50" r="35" fill="url(#leaf-grad-1)" stroke="#14532d" stroke-width="1" stroke-opacity="0.4" />
                        <circle cx="25" cy="-45" r="38" fill="url(#leaf-grad-2)" stroke="#166534" stroke-width="1" stroke-opacity="0.4" />
                        <circle cx="0" cy="-70" r="42" fill="url(#leaf-grad-1)" stroke="#14532d" stroke-width="1" stroke-opacity="0.4" />
                        <circle cx="-10" cy="-55" r="30" fill="url(#leaf-grad-3)" opacity="0.85" stroke="#15803d" stroke-width="1" stroke-opacity="0.3" />
                        <circle cx="15" cy="-60" r="32" fill="url(#leaf-grad-2)" opacity="0.9" stroke="#166534" stroke-width="1" stroke-opacity="0.3" />
                    </g>

                    <!-- Árbol Tipo 2 (Pino - Acuarela Definida) -->
                    <g id="watercolor-tree-2">
                        <!-- Tronco -->
                        <path d="M-3,0 L-3,-45 M3,0 L3,-45 M0,0 L0,-75" stroke="url(#wood-grad)" stroke-width="3.5" fill="none" opacity="0.8" />
                        <!-- Follaje Cónico con Bordes de Agua -->
                        <path d="M-45,-35 C-30,-45 -10,-40 0,-60 C10,-40 30,-45 45,-35 C35,-22 15,-18 0,-22 C-15,-18 -35,-22 -45,-35 Z" fill="url(#leaf-grad-1)" stroke="#14532d" stroke-width="1.2" stroke-opacity="0.4" />
                        <path d="M-35,-58 C-20,-68 -8,-63 0,-78 C8,-63 20,-68 35,-58 C25,-48 12,-44 0,-48 C-12,-44 -25,-48 -35,-58 Z" fill="url(#leaf-grad-2)" stroke="#166534" stroke-width="1.2" stroke-opacity="0.4" />
                        <path d="M-22,-78 C-12,-88 -5,-83 0,-98 C5,-83 12,-88 22,-78 C15,-70 8,-67 0,-70 C-8,-67 -15,-70 -22,-78 Z" fill="url(#leaf-grad-3)" stroke="#15803d" stroke-width="1.0" stroke-opacity="0.3" />
                    </g>

                    <!-- Árbol Tipo 3 (Abedul Esbelto - Acuarela Definida) -->
                    <g id="watercolor-tree-3">
                        <!-- Tronco -->
                        <path d="M-2,0 L-1,-35 Q-6,-50 -12,-65 M2,0 L1,-35 Q6,-50 12,-62" stroke="url(#wood-grad-light)" stroke-width="2.5" fill="none" opacity="0.9" />
                        <!-- Copa de Hojas con Bordes de Agua -->
                        <circle cx="-12" cy="-65" r="22" fill="url(#leaf-grad-2)" stroke="#166534" stroke-width="1.0" stroke-opacity="0.4" />
                        <circle cx="12" cy="-60" r="24" fill="url(#leaf-grad-3)" stroke="#15803d" stroke-width="1.0" stroke-opacity="0.3" />
                        <circle cx="0" cy="-80" r="26" fill="url(#leaf-grad-2)" stroke="#166534" stroke-width="1.0" stroke-opacity="0.4" />
                        <circle cx="-5" cy="-70" r="18" fill="url(#leaf-grad-4)" opacity="0.65" stroke="#ca8a04" stroke-width="0.8" stroke-opacity="0.3" />
                    </g>

                    <!-- Farol Acuarela -->
                    <g id="watercolor-lamp">
                        <line x1="0" y1="0" x2="0" y2="-65" stroke="#64748b" stroke-width="1.8" opacity="0.5" />
                        <line x1="-1" y1="0" x2="-1" y2="-65" stroke="#64748b" stroke-width="0.8" opacity="0.4" />
                        <polygon points="-6,-65 6,-65 4,-77 -4,-77" fill="#64748b" opacity="0.6" stroke="#475569" stroke-width="0.5" stroke-opacity="0.5" />
                        <circle cx="0" cy="-71" r="25" fill="url(#glow-grad)" />
                    </g>

                    <!-- Banco Acuarela -->
                    <g id="watercolor-bench">
                        <rect x="-20" y="-12" width="40" height="4" rx="1" fill="#8b7355" opacity="0.75" stroke="#5c4033" stroke-width="0.8" stroke-opacity="0.5" />
                        <rect x="-20" y="-22" width="40" height="4" rx="1" fill="#8b7355" opacity="0.75" stroke="#5c4033" stroke-width="0.8" stroke-opacity="0.5" />
                        <path d="M-15,0 L-15,-22 M15,0 L15,-22 M-18,-12 H18" stroke="#64748b" stroke-width="1.5" opacity="0.6" />
                    </g>

                    <!-- Cerco Acuarela -->
                    <g id="watercolor-fence">
                        <line x1="0" y1="-3" x2="60" y2="-3" stroke="#64748b" stroke-width="1.2" opacity="0.4" />
                        <line x1="0" y1="-12" x2="60" y2="-12" stroke="#64748b" stroke-width="1.2" opacity="0.4" />
                        <path d="M10,0 V-18 M25,0 V-18 M40,0 V-18 M55,0 V-18" stroke="#64748b" stroke-width="1.0" opacity="0.4" />
                    </g>
                </defs>

                <!-- Suelo Acuarelado Suave -->
                <rect x="0" y="338" width="1440" height="12" fill="url(#city-grad-1)" opacity="0.4" />
                <line x1="0" y1="340" x2="1440" y2="340" stroke="#cbd5e1" stroke-width="1.2" opacity="0.6" />

                <!-- Nubes Acuarela (Cielo vivo y suave) -->
                <circle cx="200" cy="70" r="70" fill="url(#cloud-grad)" />
                <circle cx="250" cy="80" r="50" fill="url(#cloud-grad)" />
                <circle cx="580" cy="60" r="60" fill="url(#cloud-grad)" />
                <circle cx="900" cy="75" r="80" fill="url(#cloud-grad)" />
                <circle cx="950" cy="85" r="55" fill="url(#cloud-grad)" />
                <circle cx="1250" cy="65" r="65" fill="url(#cloud-grad)" />

                <!-- Ráfagas de viento y hojas flotantes -->
                <path d="M 50,150 Q 200,80 400,120 T 700,90 T 1000,130 T 1350,100" stroke="#cbd5e1" stroke-width="0.8" fill="none" stroke-dasharray="8,16" opacity="0.25" />
                <path d="M 150,180 Q 300,120 500,160 T 800,110 T 1100,150 T 1400,120" stroke="#cbd5e1" stroke-width="0.6" fill="none" opacity="0.15" />

                <use href="#watercolor-seed" transform="translate(180, 80) scale(0.9)" />
                <use href="#watercolor-seed" transform="translate(420, 110) rotate(15) scale(1.1)" />
                <use href="#watercolor-seed" transform="translate(680, 70) rotate(-10) scale(0.8)" />
                <use href="#watercolor-seed" transform="translate(920, 105) rotate(20) scale(1.0)" />
                
                <!-- Hojas sueltas sopladas -->
                <use href="#watercolor-leaf-1" transform="translate(250, 120) rotate(45) scale(0.7)" />
                <use href="#watercolor-leaf-2" transform="translate(580, 140) rotate(20) scale(0.6)" />
                <use href="#watercolor-leaf-3" transform="translate(850, 110) rotate(-30) scale(0.8)" />
                <use href="#watercolor-leaf-1" transform="translate(1150, 130) rotate(15) scale(0.7)" />

                <!-- CAPA 1: Skyline Lejano de la Ciudad (Formas de Acuarela translúcidas con bordes finos de agua) -->
                <g fill="none">
                    <!-- Edificios del fondo -->
                    <rect x="70" y="80" width="60" height="260" rx="3" fill="url(#city-grad-3)" stroke="#c29d70" stroke-width="0.8" stroke-opacity="0.2" opacity="0.12" />
                    <rect x="290" y="50" width="70" height="290" rx="3" fill="url(#city-grad-1)" stroke="#d2b48c" stroke-width="0.8" stroke-opacity="0.2" opacity="0.14" />
                    <rect x="520" y="110" width="55" height="230" rx="3" fill="url(#city-grad-3)" stroke="#c29d70" stroke-width="0.8" stroke-opacity="0.2" opacity="0.10" />
                    <rect x="670" y="90" width="80" height="250" rx="3" fill="url(#city-grad-2)" stroke="#8b5a2b" stroke-width="0.8" stroke-opacity="0.2" opacity="0.12" />
                    <rect x="880" y="120" width="60" height="220" rx="3" fill="url(#city-grad-1)" stroke="#d2b48c" stroke-width="0.8" stroke-opacity="0.2" opacity="0.11" />
                    <rect x="1050" y="60" width="70" height="280" rx="3" fill="url(#city-grad-3)" stroke="#c29d70" stroke-width="0.8" stroke-opacity="0.2" opacity="0.14" />
                    <rect x="1220" y="90" width="65" height="250" rx="3" fill="url(#city-grad-1)" stroke="#d2b48c" stroke-width="0.8" stroke-opacity="0.2" opacity="0.12" />
                </g>

                <!-- CAPA 2: Ciudad Intermedia (Acuarela con mayor detalle de siluetas y bordes definidos) -->
                <g fill="none">
                    <!-- Casa con tejado izquierda -->
                    <path d="M15,210 L45,180 L75,210 V340 H15 Z" fill="url(#city-grad-2)" stroke="#8b5a2b" stroke-width="1.0" stroke-opacity="0.3" opacity="0.18" />
                    <!-- Edificio stepped izquierda -->
                    <path d="M140,340 L140,160 H165 V130 H195 V340 Z" fill="url(#city-grad-1)" stroke="#d2b48c" stroke-width="1.0" stroke-opacity="0.3" opacity="0.20" />
                    <!-- Casa con techo piramidal izquierda -->
                    <path d="M210,340 L210,180 L235,145 L260,180 L260,340 Z" fill="url(#city-grad-3)" stroke="#c29d70" stroke-width="1.0" stroke-opacity="0.3" opacity="0.18" />
                    
                    <!-- Edificios bajos centro -->
                    <rect x="440" y="240" width="55" height="100" rx="4" fill="url(#city-grad-2)" stroke="#8b5a2b" stroke-width="1.0" stroke-opacity="0.3" opacity="0.18" />
                    <rect x="830" y="210" width="70" height="130" rx="4" fill="url(#city-grad-3)" stroke="#c29d70" stroke-width="1.0" stroke-opacity="0.3" opacity="0.20" />
                    
                    <!-- Palacio municipal / Edificio clásico derecha -->
                    <path d="M1110,340 L1110,165 H1190 V340 Z" fill="url(#city-grad-2)" stroke="#8b5a2b" stroke-width="1.0" stroke-opacity="0.3" opacity="0.18" />
                    <path d="M1110,165 Q1150,130 1190,165 Z" fill="url(#city-grad-1)" stroke="#d2b48c" stroke-width="1.0" stroke-opacity="0.3" opacity="0.22" />
                    
                    <!-- Skyscraper derecha -->
                    <rect x="1210" y="100" width="60" height="240" rx="4" fill="url(#city-grad-3)" stroke="#c29d70" stroke-width="1.0" stroke-opacity="0.3" opacity="0.16" />
                    <line x1="1240" y1="100" x2="1240" y2="340" stroke="#b58a57" stroke-width="1" stroke-dasharray="2,6" opacity="0.25" />

                    <!-- Torre esbelta extrema derecha -->
                    <rect x="1290" y="140" width="45" height="200" rx="3" fill="url(#city-grad-1)" stroke="#d2b48c" stroke-width="1.0" stroke-opacity="0.3" opacity="0.18" />
                    <line x1="1312" y1="140" x2="1312" y2="85" stroke="url(#wood-grad)" stroke-width="1.8" opacity="0.4" />
                </g>

                <!-- Elementos del Parque (Cercos, bancos y faroles distribuidos en el plano de tierra) -->
                <use href="#watercolor-fence" transform="translate(15, 340)" />
                <use href="#watercolor-fence" transform="translate(390, 340)" />
                <use href="#watercolor-fence" transform="translate(965, 340)" />
                <use href="#watercolor-fence" transform="translate(1345, 340)" />

                <use href="#watercolor-bench" transform="translate(135, 340)" />
                <use href="#watercolor-bench" transform="translate(735, 340)" />
                <use href="#watercolor-bench" transform="translate(1145, 340)" />

                <use href="#watercolor-lamp" transform="translate(230, 340)" />
                <use href="#watercolor-lamp" transform="translate(630, 340)" />
                <use href="#watercolor-lamp" transform="translate(850, 340)" />
                <use href="#watercolor-lamp" transform="translate(1260, 340)" />

                <!-- Siluetas humanas y de mascotas que dan VIDA al parque -->
                <!-- Persona paseando perro (x=210) -->
                <g fill="#64748b" opacity="0.4">
                    <!-- Humano -->
                    <path d="M200,340 L202,320 Q205,315 208,320 L210,340" stroke="#475569" stroke-width="0.8" />
                    <circle cx="205" cy="309" r="3.5" />
                    <!-- Correa -->
                    <line x1="208" y1="322" x2="224" y2="334" stroke="#475569" stroke-width="0.6" />
                    <!-- Perro -->
                    <path d="M224,340 L226,331 Q230,330 234,332 L236,340" stroke="#475569" stroke-width="0.8" />
                    <circle cx="233" cy="328" r="2" />
                </g>
                <!-- Pareja sentada en el banco central (x=735) -->
                <g fill="#64748b" opacity="0.4">
                    <path d="M725,328 C725,324 728,322 730,322 C732,322 734,324 734,328 Z" />
                    <circle cx="730" cy="318" r="2.5" />
                    <path d="M740,328 C740,324 743,322 745,322 C747,322 750,324 750,328 Z" />
                    <circle cx="745" cy="318" r="2.5" />
                </g>

                <!-- CAPA 3: Bosque Urbano (Árboles en Acuarela - Llenos de Vida y Color) -->
                <!-- Grupo Izquierda -->
                <use href="#watercolor-tree-1" transform="translate(70, 340) scale(0.95)" />
                <use href="#watercolor-tree-2" transform="translate(145, 340) scale(1.0)" />
                <use href="#watercolor-tree-3" transform="translate(225, 340) scale(1.1)" />
                <use href="#watercolor-tree-1" transform="translate(325, 340) scale(0.9)" />
                
                <!-- Parque Central (Bosque denso que rodea la zona del formulario) -->
                <use href="#watercolor-tree-2" transform="translate(480, 340) scale(1.05)" />
                <use href="#watercolor-tree-3" transform="translate(540, 340) scale(0.95)" />
                <use href="#watercolor-tree-1" transform="translate(605, 340) scale(1.15)" />
                <use href="#watercolor-tree-2" transform="translate(680, 340) scale(1.0)" />
                <use href="#watercolor-tree-3" transform="translate(755, 340) scale(1.1)" />
                <use href="#watercolor-tree-1" transform="translate(830, 340) scale(0.95)" />
                <use href="#watercolor-tree-2" transform="translate(895, 340) scale(1.05)" />

                <!-- Grupo Derecha -->
                <use href="#watercolor-tree-1" transform="translate(980, 340) scale(0.9)" />
                <use href="#watercolor-tree-2" transform="translate(1050, 340) scale(1.1)" />
                <use href="#watercolor-tree-3" transform="translate(1120, 340) scale(1.0)" />
                <use href="#watercolor-tree-1" transform="translate(1220, 340) scale(1.05)" />
                <use href="#watercolor-tree-2" transform="translate(1310, 340) scale(0.95)" />
                <use href="#watercolor-tree-3" transform="translate(1390, 340) scale(1.05)" />

                <!-- Flores Silvestres del Parque (Varios splotches amarillos y rosas en el suelo) -->
                <use href="#watercolor-flower-yellow" transform="translate(90, 339) scale(1.1)" />
                <use href="#watercolor-flower-pink" transform="translate(115, 339) scale(0.9)" />
                <use href="#watercolor-flower-yellow" transform="translate(170, 339) scale(1.0)" />
                <use href="#watercolor-flower-pink" transform="translate(250, 339) scale(1.2)" />
                <use href="#watercolor-flower-yellow" transform="translate(370, 339) scale(0.9)" />
                <use href="#watercolor-flower-pink" transform="translate(490, 339) scale(1.1)" />
                <use href="#watercolor-flower-yellow" transform="translate(565, 339) scale(1.0)" />
                <use href="#watercolor-flower-pink" transform="translate(680, 339) scale(1.2)" />
                <use href="#watercolor-flower-yellow" transform="translate(770, 339) scale(0.9)" />
                <use href="#watercolor-flower-pink" transform="translate(870, 339) scale(1.1)" />
                <use href="#watercolor-flower-yellow" transform="translate(995, 339) scale(1.2)" />
                <use href="#watercolor-flower-pink" transform="translate(1085, 339) scale(0.9)" />
                <use href="#watercolor-flower-yellow" transform="translate(1180, 339) scale(1.0)" />
                <use href="#watercolor-flower-pink" transform="translate(1295, 339) scale(1.1)" />
                <use href="#watercolor-flower-yellow" transform="translate(1370, 339) scale(1.0)" />

                <!-- Mariposas del Parque -->
                <use href="#watercolor-butterfly" transform="translate(190, 260) rotate(-15) scale(1.1)" />
                <use href="#watercolor-butterfly" transform="translate(670, 240) rotate(10) scale(1.2)" />
                <use href="#watercolor-butterfly" transform="translate(850, 260) rotate(-35) scale(0.9)" />
                <use href="#watercolor-butterfly" transform="translate(1180, 230) rotate(20) scale(1.1)" />

                <!-- Aves Volando en el Cielo (Más dinámicas y llenas de vida) -->
                <g stroke="#2d7a4f" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.35">
                    <path d="M 230,90 Q 236,83 242,90 Q 248,83 254,90" />
                    <path d="M 245,102 Q 249,97 253,102 Q 257,97 261,102" />
                    <path d="M 520,70 Q 526,63 532,70 Q 538,63 544,70" />
                    <path d="M 780,100 Q 786,93 792,100 Q 798,93 804,100" />
                    <path d="M 795,112 Q 799,107 803,112 Q 807,107 811,112" />
                    <path d="M 1190,90 Q 1196,83 1202,90 Q 1208,83 1214,90" />
                    <path d="M 1205,102 Q 1209,97 1213,102 Q 1217,97 1221,102" />
                </g>
            </svg>
        </div>
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Solicitud de Plantación</h1>
            <p class="section-subtitle">
                Solicita la plantación de un nuevo ejemplar en la vereda de tu hogar. La comuna evaluará y proveerá la especie adecuada.
            </p>
        </section>

        <section class="plantacion-form-container reveal delay-1">
            @auth
                <form class="contact-form" onsubmit="event.preventDefault(); alert('Solicitud enviada con éxito (Simulación).');">
                    <div class="form-group">
                        <label for="ancho-vereda">Ancho Estimado de la Vereda</label>
                        <select id="ancho-vereda" class="form-control" required>
                            <option value="">Selecciona una opción...</option>
                            <option value="angosta">Angosta (Menos de 2 metros)</option>
                            <option value="media">Media (Entre 2 y 3.5 metros)</option>
                            <option value="ancha">Ancha (Más de 3.5 metros)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cazuela-estado">¿La cazuela (espacio de tierra) está disponible?</label>
                        <select id="cazuela-estado" class="form-control" required>
                            <option value="">Selecciona una opción...</option>
                            <option value="si">Sí, está abierta y con tierra suelta</option>
                            <option value="cemento">No, la vereda está completamente cementada</option>
                            <option value="tocon">No, hay un tronco/muñón viejo que debe extraerse primero</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="direccion-solicitud">Dirección Exacta</label>
                        <div class="input-with-button">
                            <input type="text" id="direccion-solicitud" class="form-control" placeholder="Ej: Av. Rivadavia 4800, Caballito" required>
                            <button type="button" id="btn-select-map" class="btn-main-cta">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--spring-leaf)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Seleccionar en Mapa
                            </button>
                        </div>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="compromiso" required>
                        <label for="compromiso">
                            Me comprometo a cuidar y regar el árbol regularmente durante sus primeros 3 años de vida para asegurar su crecimiento saludable.
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-main-cta">Enviar Solicitud</button>
                    </div>
                </form>
            @else
                <div class="contact-login-card">
                    <span class="lock-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <p>Para solicitar la plantación de un nuevo árbol, por favor inicia sesión en tu cuenta de vecino.</p>
                    <a href="/login" class="btn-main-cta">Iniciar Sesión</a>
                </div>
            @endauth
        </section>
    </main>

    @auth
    <!-- Modal de Selección de Dirección desde Mapa (Estilo Uber) -->
    <div id="address-map-modal" class="address-map-modal-overlay">
        <div class="address-map-modal-container">
            <div class="address-map-modal-header">
                <h3>Selecciona la ubicación</h3>
                <button type="button" id="address-map-modal-close" class="address-map-modal-close">&times;</button>
            </div>
            <div class="address-map-body">
                <div id="address-map-canvas-plantacion"></div>
                <!-- Pin flotante central y sombra (Estilo Uber) -->
                <div class="map-center-pin-shadow"></div>
                <div class="map-center-pin">
                    <svg width="34" height="46" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0C5.37 0 0 5.37 0 12C0 21 12 32 12 32C12 32 24 21 24 12C24 5.37 18.63 0 12 0ZM12 16.5C9.51 16.5 7.5 14.49 7.5 12C7.5 9.51 9.51 7.5 12 7.5C14.49 7.5 16.5 9.51 16.5 12C16.5 14.49 14.49 16.5 12 16.5Z" fill="#C62828"/>
                    </svg>
                </div>
            </div>
            <div class="address-map-modal-footer">
                <div class="address-preview-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span id="address-preview-text" class="address-preview-text">Buscando dirección...</span>
                </div>
                <button type="button" id="btn-confirm-address" class="btn-main-cta btn-confirm-address" disabled>Confirmar Ubicación</button>
            </div>
        </div>
    </div>
    @endauth
@endsection

@section('scripts')
    @auth
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/tramites/plantacion.js') }}"></script>
    @endauth
@endsection
