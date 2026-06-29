@extends('layouts.app')

@section('title', 'Reclamos | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-reclamos', 'active')

    <link rel="stylesheet" href="{{ asset('css/tramites/reclamos.css') }}?v={{ filemtime(public_path('css/tramites/reclamos.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dynamic-status.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endsection

@section('content')
    <main class="tramites-page-container" style="position: relative; overflow: hidden;">
        @include('partials.forest-background')
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Registro de Reclamos</h1>
            <p class="section-subtitle">
                Reporta incidencias, árboles caídos, ramas peligrosas o raíces que afecten la infraestructura pública.
            </p>
        </section>

        <!-- Tabs for choosing action -->
        <div class="tramites-tabs">
            <button class="tab-btn active" onclick="switchTab('create')" id="tab-btn-create">
                Registrar Reclamo
            </button>
            <button class="tab-btn" onclick="switchTab('track')" id="tab-btn-track">
                Seguimiento de Reclamo
            </button>
        </div>

        <!-- TAB: CREATE COMPLAINT -->
        <div id="section-create" class="tab-content">
            <section class="reveal delay-1">
                <!-- Banner de información de árbol preseleccionado -->
                <div id="selected-tree-banner" style="display: none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--living-moss)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div>
                        <strong class="selected-tree-title">Árbol Seleccionado del Mapa</strong>
                        <span id="selected-tree-text"></span>
                    </div>
                </div>

                <form class="contact-form" id="reclamo-form">
                    <div class="form-group">
                        <label for="tipo-reclamo">Tipo de Incidencia <span class="required-asterisk">*</span></label>
                        <select id="tipo-reclamo" class="form-control" required>
                        <!-- Aca se rellena solo con el JS -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="arbol-id">ID del Árbol (Opcional)</label>
                        <input type="number" id="arbol-id" placeholder="Ej: 1001 (Si lo conoces y deseas vincularlo)" class="form-control">
                        <small id="arbol-id-help" style="display: none;"></small>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección / Ubicación aproximada <span class="required-asterisk">*</span></label>
                        <div class="input-with-button">
                            <input type="text" id="direccion" placeholder="Ej: Av. Santa Fe 2500, Palermo" class="form-control" required>
                            <button type="button" id="btn-select-map" class="btn-main-cta track-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--spring-leaf)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Seleccionar en Mapa
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Detalles del Reclamo <span class="required-asterisk">*</span></label>
                        <textarea id="descripcion" placeholder="Describe brevemente la situación para ayudar a los inspectores..." required rows="4" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="foto">Adjuntar Foto (Opcional)</label>
                        <input type="file" id="foto" accept="image/*" class="form-control foto-input">
                        <small class="foto-help-text">Formatos soportados: JPG, PNG. Tamaño máximo: 5MB.</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-main-cta">Enviar Reclamo</button>
                    </div>
                </form>
            </section>
        </div>

        <!-- TAB: TRACK COMPLAINT -->
        <div id="section-track" class="tab-content" style="display: none;">
            <div class="track-container">
                <h2 class="track-title">Consulta tu Reclamo</h2>
                <p class="track-subtitle">Ingresa el código identificador de tu solicitud (ej: REC-2026-001) para ver el progreso actual y la respuesta de la Comuna.</p>
                
                <div class="track-input-group">
                    <input type="text" id="track-id-input" placeholder="Ej. REC-2026-001" class="track-input">
                    <button type="button" class="btn-main-cta track-btn" onclick="trackComplaint()">Buscar Solicitud</button>
                </div>

            <!-- Error container -->
            <div id="track-error" class="track-error" style="display: none;">
                No pudimos encontrar ningún reclamo con ese código. Por favor verifica que esté bien escrito.
            </div>

            <!-- Result container -->
            <div id="track-result" style="display: none;">
                <!-- Stepper -->
                <div class="track-stepper">
                    <h3 class="track-stepper-title">Estado de Gestión</h3>
                    
                    <div class="track-stepper-row" id="dynamic-stepper-container">
                        <!-- El Stepper se generará dinámicamente vía Javascript -->
                    </div>
                </div>

                <!-- Admin Response Box -->
                <div class="admin-reply-box" id="admin-reply-box">
                    <h4 class="admin-reply-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--living-moss)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Respuesta del Administrador
                    </h4>
                    <p class="admin-reply-text" id="track-admin-reply">
                        Aún no se ha redactado ninguna respuesta oficial para esta solicitud.
                    </p>
                </div>

                <!-- Info summary -->
                <div class="track-summary-grid">
                    <div class="track-summary-item">
                        <span class="track-summary-label">Ubicación Reportada</span>
                        <strong class="track-summary-value" id="track-direccion">-</strong>
                    </div>
                    <div class="track-summary-item">
                        <span class="track-summary-label">Categoría y Fecha</span>
                        <strong class="track-summary-value" id="track-categoria">-</strong>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Selección de Dirección desde Mapa (Estilo Uber) -->
    <div id="address-map-modal" class="address-map-modal-overlay">
        <div class="address-map-modal-container">
            <div class="address-map-modal-header">
                <h3>Selecciona la ubicación</h3>
                <button type="button" id="address-map-modal-close" class="address-map-modal-close">&times;</button>
            </div>
            <div class="address-map-body">
                <div id="address-map-canvas"></div>
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
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/tramites/reclamos-mapa.js') }}"></script>
    <script src="{{ asset('js/tramites/reclamos.js') }}"></script>
@endsection
