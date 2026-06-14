@extends('layouts.app')

@section('title', 'Reclamos | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-reclamos', 'active')

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
                    <input type="text" id="direccion" placeholder="Ej: Av. Santa Fe 2500, Palermo" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%; transition: all 0.3s ease;" required>
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
@endsection

@section('scripts')
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
        });
    </script>
@endsection
