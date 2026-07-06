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

    return mapInstance;
}

export function getMap() {
    return mapInstance;
}
