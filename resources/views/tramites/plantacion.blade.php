@extends('layouts.app')

@section('title', 'Solicitud de Plantación | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-plantacion', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/plantacion.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endsection

@section('content')
    <main class="tramites-page-container">
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Solicitud de Plantación</h1>
            <p class="section-subtitle">
                Solicita la plantación de un nuevo ejemplar en la vereda de tu hogar. La comuna evaluará y proveerá la especie adecuada.
            </p>
        </section>

        <section class="plantacion-form-container reveal delay-1">
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
        </section>
    </main>

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
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputDireccion = document.getElementById('direccion-solicitud');
            
            // --- LÓGICA DEL SELECTOR DE MAPA ESTILO UBER ---
            const btnSelectMap = document.getElementById('btn-select-map');
            const mapModal = document.getElementById('address-map-modal');
            const mapModalClose = document.getElementById('address-map-modal-close');
            const btnConfirmAddress = document.getElementById('btn-confirm-address');
            const previewText = document.getElementById('address-preview-text');
            const addressMapBody = document.querySelector('.address-map-body');
            
            let selectorMap = null;
            let currentCoordsAddress = '';
            let debounceTimer = null;

            function initSelectorMap() {
                if (selectorMap) return;

                // Centrar en Plaza Armenia, Palermo (-34.5888, -58.4285)
                selectorMap = L.map('address-map-canvas-plantacion', {
                    zoomControl: false
                }).setView([-34.5888, -58.4285], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(selectorMap);

                L.control.zoom({ position: 'topright' }).addTo(selectorMap);

                // Función de geocodificación reversa usando Nominatim
                function reverseGeocode(lat, lng) {
                    previewText.textContent = 'Buscando dirección...';
                    btnConfirmAddress.disabled = true;

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.address) {
                                const road = data.address.road || data.address.pedestrian || data.address.path || '';
                                const number = data.address.house_number || '';
                                const suburb = data.address.suburb || data.address.neighbourhood || '';
                                
                                if (road) {
                                    currentCoordsAddress = road + (number ? ' ' + number : '') + (suburb ? ', ' + suburb : '');
                                } else {
                                    currentCoordsAddress = data.display_name.split(',').slice(0, 3).join(',').trim();
                                }
                            } else {
                                currentCoordsAddress = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                            }
                            previewText.textContent = currentCoordsAddress;
                            btnConfirmAddress.disabled = false;
                        })
                        .catch(err => {
                            console.error('Nominatim error, usando fallback:', err);
                            // Fallback de simulación en Palermo según cercanía
                            const fallbacks = [
                                { lat: -34.5888, lng: -58.4285, address: 'Costa Rica 4600' },
                                { lat: -34.5795, lng: -58.4148, address: 'Av. Sarmiento 2400' },
                                { lat: -34.6178, lng: -58.3712, address: 'Defensa 850' },
                                { lat: -34.5835, lng: -58.3927, address: 'Plaza Francia 1100' },
                                { lat: -34.5615, lng: -58.4552, address: 'Juramento 1900' }
                            ];
                            
                            let closest = fallbacks[0];
                            let minDist = Infinity;
                            fallbacks.forEach(f => {
                                let dist = Math.pow(f.lat - lat, 2) + Math.pow(f.lng - lng, 2);
                                if (dist < minDist) {
                                    minDist = dist;
                                    closest = f;
                                }
                            });
                            
                            const simulatedNumber = Math.floor(100 + Math.random() * 800) * 10;
                            const streetName = closest.address.split(' ').slice(0, -1).join(' ') || closest.address.split(' ')[0];
                            currentCoordsAddress = streetName + ' ' + simulatedNumber + ', Palermo, CABA';
                            previewText.textContent = currentCoordsAddress;
                            btnConfirmAddress.disabled = false;
                        });
                }

                // Cargar dirección inicial
                const initialCenter = selectorMap.getCenter();
                reverseGeocode(initialCenter.lat, initialCenter.lng);

                // Añadir efectos físicos de salto al pin
                selectorMap.on('movestart', () => {
                    addressMapBody.classList.add('map-moving');
                });

                selectorMap.on('moveend', () => {
                    addressMapBody.classList.remove('map-moving');
                    
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const center = selectorMap.getCenter();
                        reverseGeocode(center.lat, center.lng);
                    }, 500);
                });
            }

            btnSelectMap.addEventListener('click', () => {
                mapModal.classList.add('active');
                setTimeout(() => {
                    initSelectorMap();
                    if (selectorMap) {
                        selectorMap.invalidateSize();
                    }
                }, 100);
            });

            mapModalClose.addEventListener('click', () => {
                mapModal.classList.remove('active');
            });

            btnConfirmAddress.addEventListener('click', () => {
                if (currentCoordsAddress) {
                    inputDireccion.value = currentCoordsAddress;
                }
                mapModal.classList.remove('active');
            });
        });
    </script>
@endsection
