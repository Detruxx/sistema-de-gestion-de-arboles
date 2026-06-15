@extends('layouts.app')

@section('title', 'Reclamos | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-reclamos', 'active')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endsection

@section('content')
    <main class="tramites-page-container">
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Registro de Reclamos</h1>
            <p class="section-subtitle">
                Reporta incidencias, árboles caídos, ramas peligrosas o raíces que afecten la infraestructura pública.
            </p>
        </section>

        <section style="max-width: 800px; margin: 0 auto; position: relative; z-index: 10;" class="reveal delay-1">
            <!-- Banner de información de árbol preseleccionado -->
            <div id="selected-tree-banner" style="display: none; background-color: rgba(91, 191, 140, 0.15); border: 2px solid var(--living-moss); border-radius: 12px; padding: 15px; margin-bottom: 25px; color: var(--forest-night); align-items: center; gap: 15px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--living-moss)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <div>
                    <strong style="display: block; font-family: var(--font-display); color: var(--deep-canopy); font-size: 1.05rem; margin-bottom: 4px;">Árbol Seleccionado del Mapa</strong>
                    <span id="selected-tree-text" style="font-size: 0.95rem;"></span>
                </div>
            </div>

            <form class="contact-form" onsubmit="event.preventDefault(); alert('Reclamo registrado con éxito (Simulación).');">
                <div class="form-group">
                    <label for="tipo-reclamo">Tipo de Incidencia</label>
                    <select id="tipo-reclamo" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%;" required>
                        <option value="">Selecciona una opción...</option>
                        <option value="caido">Árbol o rama de gran porte caído</option>
                        <option value="seco">Árbol seco con riesgo de caída</option>
                        <option value="ramas">Ramas obstruyendo cables o alumbrado</option>
                        <option value="raices">Raíces levantando la acera</option>
                        <option value="otro">Otros daños o plagas</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="arbol-id">ID del Árbol (Opcional)</label>
                    <input type="number" id="arbol-id" placeholder="Ej: 1001 (Si lo conoces y deseas vincularlo)" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%;">
                    <small id="arbol-id-help" style="display: none; color: #b73235; margin-top: 6px; font-size: 0.85rem; font-weight: 500;"></small>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="direccion">Dirección / Ubicación aproximada</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="direccion" placeholder="Ej: Av. Santa Fe 2500, Palermo" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; flex-grow: 1; transition: all 0.3s ease;" required>
                        <button type="button" id="btn-select-map" class="btn-main-cta" style="flex-shrink: 0; padding: 15px 20px; border-radius: 8px; font-size: 0.95rem; text-transform: none; display: flex; align-items: center; justify-content: center; gap: 8px; background-color: var(--deep-canopy); border: 1px solid var(--living-moss); box-shadow: 0 4px 10px rgba(45, 122, 79, 0.15);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--spring-leaf)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Seleccionar en Mapa
                        </button>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="descripcion">Detalles del Reclamo</label>
                    <textarea id="descripcion" placeholder="Describe brevemente la situación para ayudar a los inspectores..." required rows="4" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%; resize: vertical;"></textarea>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-main-cta">Enviar Reclamo</button>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Base de datos de árboles simulada para validación
            const arboles = [
                { id: 1001, especie: 'Jacarandá', direccion: 'Plaza Armenia, Palermo, CABA' },
                { id: 1002, especie: 'Ceibo', direccion: 'Av. Sarmiento 2400, Palermo, CABA' },
                { id: 1003, especie: 'Fresno', direccion: 'Defensa 850, San Telmo, CABA' },
                { id: 1004, especie: 'Palo Borracho', direccion: 'Plaza Francia, Recoleta, CABA' },
                { id: 1005, especie: 'Tilo', direccion: 'Juramento 1900, Belgrano, CABA' },
                { id: 1006, especie: 'Liquidámbar', direccion: 'Av. Del Libertador 3200, Palermo, CABA' },
                { id: 1007, especie: 'Jacarandá', direccion: 'Plaza Cortazar, Palermo, CABA' },
                { id: 1008, especie: 'Fresno', direccion: 'Av. Cabildo 2100, Belgrano, CABA' },
                { id: 1009, especie: 'Tilo', direccion: 'Bolívar 600, San Telmo, CABA' },
                { id: 1010, especie: 'Ceibo', direccion: 'Parque Rivadavia, Caballito, CABA' },
                { id: 1011, especie: 'Liquidámbar', direccion: 'Juana Manso 1100, Puerto Madero, CABA' },
                { id: 1012, especie: 'Palo Borracho', direccion: 'Av. 9 de Julio 1200, San Nicolás, CABA' }
            ];

            const inputArbolId = document.getElementById('arbol-id');
            const inputDireccion = document.getElementById('direccion');
            const banner = document.getElementById('selected-tree-banner');
            const bannerText = document.getElementById('selected-tree-text');
            const helpText = document.getElementById('arbol-id-help');

            function setSeleccionArbol(arbol) {
                if (arbol) {
                    inputDireccion.value = arbol.direccion;
                    inputDireccion.readOnly = true;
                    inputDireccion.style.backgroundColor = 'rgba(235, 245, 238, 0.5)';
                    inputDireccion.style.cursor = 'not-allowed';

                    banner.style.display = 'flex';
                    bannerText.innerHTML = `Estás registrando un reclamo para el árbol <strong>ID #${arbol.id} (${arbol.especie})</strong> ubicado en <strong>${arbol.direccion}</strong>.`;
                    helpText.style.display = 'none';
                } else {
                    inputDireccion.readOnly = false;
                    inputDireccion.style.backgroundColor = 'var(--paper-white)';
                    inputDireccion.style.cursor = 'text';

                    banner.style.display = 'none';
                    bannerText.textContent = '';
                }
            }

            // 1. Verificar si viene con ID preseleccionado del mapa
            const urlParams = new URLSearchParams(window.location.search);
            const arbolIdParam = urlParams.get('arbol_id');

            if (arbolIdParam) {
                inputArbolId.value = arbolIdParam;
                inputArbolId.readOnly = true;
                inputArbolId.style.backgroundColor = 'rgba(235, 245, 238, 0.5)';
                inputArbolId.style.cursor = 'not-allowed';
                
                const matched = arboles.find(a => a.id == arbolIdParam);
                if (matched) {
                    setSeleccionArbol(matched);
                }
            }

            // 2. Controlar ingreso manual de ID
            inputArbolId.addEventListener('input', () => {
                if (inputArbolId.readOnly) return;

                const typedVal = inputArbolId.value.trim();
                if (!typedVal) {
                    setSeleccionArbol(null);
                    helpText.style.display = 'none';
                    return;
                }

                const matched = arboles.find(a => a.id == typedVal);
                if (matched) {
                    setSeleccionArbol(matched);
                } else {
                    setSeleccionArbol(null);
                    helpText.style.display = 'block';
                    helpText.textContent = 'El ID ingresado no corresponde a ningún árbol del censo. El reclamo se registrará por ubicación manual.';
                }
            });

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
                selectorMap = L.map('address-map-canvas', {
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
                    inputDireccion.readOnly = false;
                    inputDireccion.style.backgroundColor = 'var(--paper-white)';
                    inputDireccion.style.cursor = 'text';
                    inputArbolId.value = ''; 
                    banner.style.display = 'none';
                    helpText.style.display = 'none';
                }
                mapModal.classList.remove('active');
            });
        });
    </script>
@endsection
