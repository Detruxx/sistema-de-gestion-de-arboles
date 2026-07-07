/**
 * Map Module genérico basado en Leaflet para reutilizarse en los dashboards.
 */

export function initGenericMap(containerId, startLat = -34.5888, startLng = -58.4285, startZoom = 14) {
    const mapContainer = document.getElementById(containerId);
    if (!mapContainer) return null;

    const mapInstance = L.map(containerId, {
        zoomControl: false
    }).setView([startLat, startLng], startZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapInstance);

    L.control.zoom({ position: 'bottomright' }).addTo(mapInstance);

    // Render group
    const markersGroup = L.layerGroup().addTo(mapInstance);

    setTimeout(() => {
        mapInstance.invalidateSize();
    }, 200);

    return { mapInstance, markersGroup };
}

export function triggerMapResize(mapInstance) {
    if (mapInstance) {
        setTimeout(() => {
            mapInstance.invalidateSize();
        }, 150);
    }
}

/**
 * 
 * @param {Object} mapObj - { mapInstance, markersGroup } retornado por initGenericMap
 * @param {Array} items - Arreglo de datos (pueden ser claims o jobs)
 * @param {Function} configCb - Función callback que recibe el item y devuelve { lat, lng, color, id }
 * @param {Function} clickCb - Función a llamar cuando hacen click en el marcador
 */
export function updateMapMarkers(mapObj, items, configCb, clickCb) {
    if (!mapObj || !mapObj.mapInstance || !mapObj.markersGroup) return;

    mapObj.markersGroup.clearLayers();
    const bounds = [];

    items.forEach(item => {
        const config = configCb(item);
        if (!config || !config.lat || !config.lng) return;

        const markerHtml = `
            <div style="background-color: ${config.color || '#6b7280'}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
        `;

        const customIcon = L.divIcon({
            html: markerHtml,
            className: 'custom-map-marker',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const marker = L.marker([config.lat, config.lng], { icon: customIcon }).addTo(mapObj.markersGroup);

        marker.on('click', () => {
            if (clickCb) clickCb(config.id);
        });

        bounds.push([config.lat, config.lng]);
    });

    if (bounds.length > 0) {
        mapObj.mapInstance.fitBounds(bounds, { padding: [30, 30] });
    }
}
