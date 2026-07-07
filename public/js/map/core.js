/**
 * Núcleo (Mapa Público): Configuración inicial y lógica base de inicialización del mapa y la capa principal.
 */

// ================= LÓGICA DEL MAPA - CORE =================
let mapInstance = null;

export function initMap() {
    const mapElement = document.getElementById('tree-map');
    if (!mapElement) return null;

    // Crear un renderizador Canvas con alta "tolerancia" para clics (15px extra de área invisible)
    const canvasRenderer = L.canvas({ padding: 0.5, tolerance: 15 });

    // Inicializar el mapa centrado en Palermo, CABA
    mapInstance = L.map('tree-map', {
        zoomControl: false, // Desactivamos el default para no chocar con el panel
        renderer: canvasRenderer,
        zoomSnap: 0.1,        // Permite niveles de zoom intermedios (ej. 13.1, 13.2)
        zoomDelta: 0.25,      // Cada "clic" de la rueda del ratón avanza un cuarto de nivel
        wheelPxPerZoomLevel: 120 // Hace que el giro físico de la rueda se sienta más lento/pesado
    }).setView([-34.5888, -58.4285], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapInstance);

    // Mover controles de zoom al extremo inferior derecho
    L.control.zoom({ position: 'bottomright' }).addTo(mapInstance);

    // Geolocalizar al usuario automáticamente al cargar el mapa (salto instantáneo)
    mapInstance.locate({ setView: true, maxZoom: 16 });

    mapInstance.on('locationerror', function(e) {
        console.warn('No se pudo acceder a la ubicación: ' + e.message);
        // Si falla (ej. bloqueado por HTTP o permisos), nos quedamos en Palermo
    });

    mapInstance.on('locationfound', function(e) {
        const myLocationIcon = L.divIcon({
            className: 'my-location-pin',
            html: `
                <div style="display: flex; flex-direction: column; align-items: center; pointer-events: none;">
                    <svg width="20" height="28" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.3));">
                        <path d="M12 0C5.37 0 0 5.37 0 12C0 21 12 32 12 32C12 32 24 21 24 12C24 5.37 18.63 0 12 0ZM12 16.5C9.51 16.5 7.5 14.49 7.5 12C7.5 9.51 9.51 7.5 12 7.5C14.49 7.5 16.5 9.51 16.5 12C16.5 14.49 14.49 16.5 12 16.5Z" fill="#D32F2F"/>
                    </svg>
                    <span style="background: white; padding: 3px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; font-family: var(--font-body, sans-serif); box-shadow: 0 1px 4px rgba(0,0,0,0.25); margin-top: 4px; color: #333; white-space: nowrap;">Te encuentras aquí</span>
                </div>
            `,
            iconSize: [120, 55],
            iconAnchor: [60, 28] // Anclar la base del pin (no el texto)
        });
        L.marker(e.latlng, { icon: myLocationIcon, zIndexOffset: 1000 }).addTo(mapInstance);
    });

    return mapInstance;
}

export function getMap() {
    return mapInstance;
}
