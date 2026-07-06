@extends('layouts.app')

@section('title', 'Reclamos | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-reclamos', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/forms/reclamos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboards/dynamic-status.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endsection

@section('content')
    <main class="tramites-page-container" style="position: relative; overflow: hidden;">
        <div class="bg-blurred-image reclamos-bg"></div>
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
        <div id="section-create" class="tab-content" style="display: block;">
            <x-layouts.alert-modal 
                type="success" 
                title="¡Trámite Enviado!" 
                message="Tu sugerencia/reclamo ha sido registrado correctamente."
                image="{{ asset('img/components/success-tree.webp') }}"
            />
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

                @auth
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
                        <label>Adjuntar Foto (Opcional)</label>
                        <div class="custom-file-upload">
                            <label for="foto" class="file-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-paperclip"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                <span>Seleccionar archivo</span>
                            </label>
                            <input type="file" id="foto" accept="image/*" multiple class="foto-input" style="display: none;" onchange="
                                const count = this.files.length;
                                const label = document.getElementById('foto-name');
                                if(count === 0) label.textContent = 'Ningún archivo seleccionado';
                                else if(count === 1) label.textContent = this.files[0].name;
                                else label.textContent = count + ' archivos seleccionados (Máx 3)';
                            ">
                            <span class="file-name" id="foto-name">Ningún archivo seleccionado</span>
                        </div>
                        <small class="foto-help-text">Formatos: JPG, PNG, WEBP. Hasta 3 fotos (Máx 10MB c/u).</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-main-cta">Enviar Reclamo</button>
                    </div>
                </form>
                @else
                <div class="contact-login-card">
                    <span class="lock-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <p>Para registrar un reclamo, por favor inicia sesión en tu cuenta de vecino.</p>
                    <a href="/login" class="btn-main-cta">Iniciar Sesión</a>
                </div>
                @endauth
            </section>
        </div>

        <!-- TAB: TRACK COMPLAINT -->
        <div id="section-track" class="tab-content" style="display: none;">
            <div class="track-container">
                <h2 class="track-title">Consulta tu Reclamo</h2>
                <p class="track-subtitle">Ingresa el código identificador de tu solicitud (ej: REC-2026-001) para ver el progreso actual y la respuesta de la Comuna.</p>
                
                <div class="track-input-group" style="display: flex; gap: 15px; align-items: stretch; justify-content: flex-start; flex-wrap: wrap;">
                    
                    <!-- Contenedor visual que simula ser un solo input -->
                    <div style="display: flex; align-items: center; gap: 8px; background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 0 15px; flex-grow: 1; max-width: 320px; transition: all 0.3s ease;">
                        
                        <input type="text" id="track-part1" maxlength="3" placeholder="REC" style="width: 50px; border: none; background: transparent; outline: none; text-align: left; font-size: 1.1rem; color: var(--forest-night); text-transform: uppercase; font-family: var(--font-body); padding: 15px 0;">
                        
                        <span style="color: rgba(45, 122, 79, 0.5); font-weight: bold;">-</span>
                        
                        <input type="text" id="track-part2" maxlength="4" placeholder="2026" style="width: 60px; border: none; background: transparent; outline: none; text-align: left; font-size: 1.1rem; color: var(--forest-night); font-family: var(--font-body); padding: 15px 0;">
                        
                        <span style="color: rgba(45, 122, 79, 0.5); font-weight: bold;">-</span>
                        
                        <input type="text" id="track-part3" maxlength="3" placeholder="001" style="width: 50px; border: none; background: transparent; outline: none; text-align: left; font-size: 1.1rem; color: var(--forest-night); font-family: var(--font-body); padding: 15px 0;">
                        
                        <!-- Input oculto (ESQUELETO BACKEND): Almacena el valor completo (Ej: REC-2026-001) para que el backend o el JS existente lo procese sin tener que cambiar nada en su lógica -->
                        <input type="hidden" id="track-id-input" class="track-input">
                    </div>

                    <button type="button" class="btn-main-cta track-btn" onclick="trackComplaint()" style="white-space: nowrap;">Buscar Solicitud</button>
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
    <script type="module" src="{{ asset('js/forms/claims/main.js') }}"></script>

    <!-- Lógica Frontend para el formato automático de código (Sin necesidad de Backend) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const p1 = document.getElementById('track-part1');
            const p2 = document.getElementById('track-part2');
            const p3 = document.getElementById('track-part3');
            const hiddenInput = document.getElementById('track-id-input');

            // Función para unificar los 3 campos en el input oculto que usa el sistema/backend
            function updateHiddenInput() {
                const val1 = p1.value.toUpperCase();
                const val2 = p2.value;
                const val3 = p3.value;
                if (val1 || val2 || val3) {
                    hiddenInput.value = `${val1}-${val2}-${val3}`;
                } else {
                    hiddenInput.value = '';
                }
            }

            function setupAutoAdvance(current, next, prev, maxLength) {
                // Evento al escribir
                current.addEventListener('input', function(e) {
                    // Limpieza: Solo letras en la 1ra parte, solo números en las demás
                    if (current.id === 'track-part1') {
                        current.value = current.value.replace(/[^a-zA-Z]/g, '');
                    } else {
                        current.value = current.value.replace(/[^0-9]/g, '');
                    }

                    updateHiddenInput();

                    // Pasar al siguiente input si ya completó los caracteres
                    if (current.value.length >= maxLength && next) {
                        next.focus();
                    }
                });

                // Eventos de teclado (Retroceso y Enter)
                current.addEventListener('keydown', function(e) {
                    // Volver al input anterior al borrar si está vacío
                    if (e.key === 'Backspace' && current.value === '' && prev) {
                        prev.focus();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (typeof trackComplaint === 'function') {
                            trackComplaint();
                        }
                    }
                });
                
                // Manejar Pegado (Paste) de un código completo (ej: REC-2026-001 o REC2026001)
                current.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let pasted = (e.clipboardData || window.clipboardData).getData('text');
                    pasted = pasted.replace(/[^a-zA-Z0-9]/g, ''); // Remover guiones
                    
                    if (pasted.length > 0) {
                        p1.value = pasted.substring(0, 3).toUpperCase();
                        p2.value = pasted.substring(3, 7);
                        p3.value = pasted.substring(7, 10);
                        updateHiddenInput();
                        
                        if (pasted.length <= 3) p1.focus();
                        else if (pasted.length <= 7) p2.focus();
                        else p3.focus();
                    }
                });
            }

            if (p1 && p2 && p3) {
                setupAutoAdvance(p1, p2, null, 3);
                setupAutoAdvance(p2, p3, p1, 4);
                setupAutoAdvance(p3, null, p2, 3);
            }
        });
    </script>
@endsection

