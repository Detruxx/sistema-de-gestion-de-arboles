@extends('layouts.app')

@section('title', 'Permisos y Normativas | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-permisos', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/forms/permisos.css') }}?v=2">
@endsection

@section('content')
    <main class="tramites-page-container permisos-full-bg">
        <!-- Fondo general que cubre toda la pantalla -->
        <div class="bg-blurred-image permisos-bg"></div>

        <!-- Contenedor alineado a la izquierda -->
        <div class="permisos-content-wrapper">
            <section class="cuidados-header reveal">
                <h1 class="hero-title">Permisos y Normativas</h1>
                <p class="section-subtitle">
                    De acuerdo con la Ley N° 3263, toda intervención sobre el arbolado público requiere de una autorización formal de la comuna.
                </p>
            </section>

            <section class="permisos-container reveal delay-1">
            
            <div class="permisos-card">
                <h3 class="permisos-card-title">
                    Tipos de Trámites Disponibles
                </h3>
                
                <div class="permisos-list">
                    <div class="permisos-list-item border-spring">
                        <h4 class="permisos-list-item-title">1. Solicitud de Extracción o Traslado</h4>
                        <p class="permisos-list-item-text">
                            Válido únicamente si el ejemplar compromete la seguridad de las personas, presenta una inclinación severa irreversible o produce daños estructurales irreparables en el frente edilicio.
                        </p>
                    </div>

                    <div class="permisos-list-item border-moss">
                        <h4 class="permisos-list-item-title">2. Solicitud de Poda Excepcional</h4>
                        <p class="permisos-list-item-text">
                            Reservado cuando las ramas interfieren con el cableado de alta tensión, luminarias públicas o interfieren con las cámaras de seguridad urbana.
                        </p>
                    </div>

                    <div class="permisos-list-item border-night">
                        <h4 class="permisos-list-item-title">3. Modificación de Veredas</h4>
                        <p class="permisos-list-item-text">
                            Permiso para agrandar la plantera del árbol para optimizar el paso del agua de lluvia y evitar el levantamiento de baldosas.
                        </p>
                    </div>
                </div>
            </div>

            <div class="permisos-docs-card">
                <h4 class="permisos-docs-title">
                    Documentación Necesaria para iniciar el Trámite
                </h4>
                <ul class="permisos-docs-list">
                    <li>✓ Documento Nacional de Identidad (DNI) del solicitante.</li>
                    <li>✓ Acreditación de domicilio (Ej: impuesto o título de propiedad).</li>
                    <li>✓ Fotografías claras del estado actual del árbol y el daño reclamado.</li>
                </ul>
                <div>
                    <a href="https://buenosaires.gob.ar/tramites" target="_blank" class="btn-main-cta">
                        Iniciar Trámite en GCBA
                    </a>
                </div>
            </div>

            </section>
        </div>
    </main>
@endsection

