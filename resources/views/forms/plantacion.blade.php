@extends('layouts.app')

@section('title', 'Solicitud de Plantación | Arborea')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-plantacion', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/forms/plantacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/autocomplete.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endsection

@section('content')
    <main class="tramites-page-container" style="position: relative; overflow: hidden;">
        <div class="bg-blurred-image plantacion-bg"></div>
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Solicitud de Plantación</h1>
            <p class="section-subtitle">
                Solicita la plantación de un nuevo ejemplar en la vereda de tu hogar. La comuna evaluará y proveerá la especie adecuada.
            </p>
        </section>

        <section class="plantacion-form-container reveal delay-1">
            @auth
                <form class="contact-form" id="plantacion-form" >


                    <div class="form-group">
                        <label for="cazuela-estado">¿La plantera (espacio de tierra) está disponible?</label>
                        <select id="cazuela-estado" name="cazuela_estado" class="form-control" required>
                            <option value="">Selecciona una opción...</option>
                            <option value="si">Sí, está abierta y con tierra suelta</option>
                            <option value="cemento">No, la vereda está completamente cementada</option>
                            <option value="tocon">No, hay un tronco/muñón viejo que debe extraerse primero</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="direccion-solicitud">Dirección Exacta</label>
                        <div class="input-with-button">
                            <input type="text" id="direccion-solicitud" name="address" class="form-control" placeholder="Ej: Av. Rivadavia 4800, Caballito" required>
                            <button type="button" id="btn-select-map" class="btn-main-cta">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--spring-leaf)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Seleccionar en Mapa
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion-plantacion">Descripción / Motivo (Opcional)</label>
                        <textarea id="descripcion-plantacion" name="description" class="form-control" rows="3" placeholder="Explica brevemente por qué solicitas la plantación (ej. sombra, reemplazo de árbol seco, etc.)."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Adjuntar Foto del lugar (Opcional)</label>
                        <div class="custom-file-upload">
                            <label for="foto-plantacion" class="file-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-paperclip"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                <span>Seleccionar archivo</span>
                            </label>
                            <input type="file" id="foto-plantacion" name="foto" accept="image/*" class="foto-input" style="display: none;" onchange="document.getElementById('foto-plantacion-name').textContent = this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado'">
                            <span class="file-name" id="foto-plantacion-name">Ningún archivo seleccionado</span>
                        </div>
                        <small class="foto-help-text">Formatos soportados: JPG, PNG. Tamaño máximo: 5MB.</small>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="compromiso" name="compromiso" required oninvalid="this.setCustomValidity('Es un campo obligatorio.')" oninput="this.setCustomValidity('')">
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
    <x-layouts.alert-modal 
        type="success" 
        title="¡Trámite Enviado!" 
        message="Tu solicitud de plantación ha sido registrada correctamente."
        image="{{ asset('img/components/success-tree.webp') }}"
    />
    <x-layouts.alert-modal 
        type="error" 
        title="Error" 
        message="Ocurrió un error al procesar tu solicitud."
        image="{{ asset('img/components/error-tree.webp') }}"
    />
    @endauth
@endsection

@section('scripts')
    @auth
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/shared/geocoder.js') }}"></script>
    <script src="{{ asset('js/shared/address-autocomplete.js') }}"></script>
    <script type="module" src="{{ asset('js/forms/planting.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initAddressAutocomplete === 'function') {
                initAddressAutocomplete('direccion-solicitud');
            }
        });
    </script>
    @endauth
@endsection

