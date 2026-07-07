/**
 * Map (Dashboard Inspector): Lógica de mapa de calor y geolocalización de reclamos.
 */

import { state } from './state.js';

let claimsMapInstance = null;
let claimsMarkersGroup = null;

export function initClaimsMap() {
    const mapContainer = document.getElementById('claims-map');
    if (!mapContainer || claimsMapInstance) return;

    claimsMapInstance = L.map('claims-map', {
        zoomControl: false
    }).setView([-34.5888, -58.4285], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(claimsMapInstance);

    L.control.zoom({ position: 'bottomright' }).addTo(claimsMapInstance);
    claimsMarkersGroup = L.layerGroup().addTo(claimsMapInstance);

    setTimeout(() => {
        claimsMapInstance.invalidateSize();
    }, 200);
}

export function triggerMapResize() {
    if (claimsMapInstance) {
        setTimeout(() => {
            claimsMapInstance.invalidateSize();
        }, 150);
    }
}

export function updateClaimsMapMarkers() {
    if (!claimsMapInstance || !claimsMarkersGroup) return;

    claimsMarkersGroup.clearLayers();
    const bounds = [];

    state.claims.forEach(claim => {
        let lat = claim.lat || claim.latitude;
        let lng = claim.lng || claim.longitude;
        if (!lat || !lng) {
            const numId = parseInt(claim.id.replace(/\D/g, '')) || 0;
            lat = -34.5700 - (numId % 20) * 0.0015;
            lng = -58.4500 - (numId % 15) * 0.0012;
        }

        const statusObj = state.requestStatuses.find(rs => rs.slug === claim.estado);
        const color = statusObj ? statusObj.color : '#6b7280';

        const markerHtml = `
            <div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
        `;

        const customIcon = L.divIcon({
            html: markerHtml,
            className: 'custom-claim-marker',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(claimsMarkersGroup);

        marker.on('click', () => {
            if (typeof window.selectClaim === 'function') {
                window.selectClaim(claim.id);
            }
        });

        bounds.push([lat, lng]);
    });

    if (bounds.length > 0) {
        claimsMapInstance.fitBounds(bounds, { padding: [30, 30] });
    }
}
