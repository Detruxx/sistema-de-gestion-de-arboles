/**
 * Map (Dashboard Inspector): Lógica de mapa de calor y geolocalización de reclamos.
 */

import { state } from './state.js';
import { initGenericMap, triggerMapResize as genericTriggerMapResize, updateMapMarkers } from '../shared/map-module.js';

let claimsMapObj = null;

export function initClaimsMap() {
    const mapContainer = document.getElementById('claims-map');
    if (!mapContainer || claimsMapObj) return;

    claimsMapObj = initGenericMap('claims-map');
}

export function triggerMapResize() {
    if (claimsMapObj && claimsMapObj.mapInstance) {
        genericTriggerMapResize(claimsMapObj.mapInstance);
    }
}

export function updateClaimsMapMarkers() {
    if (!claimsMapObj) return;

    updateMapMarkers(claimsMapObj, state.claims, (claim) => {
        let lat = claim.lat || claim.latitude;
        let lng = claim.lng || claim.longitude;
        if (!lat || !lng) {
            const numId = parseInt(claim.id.replace(/\D/g, '')) || 0;
            lat = -34.5700 - (numId % 20) * 0.0015;
            lng = -58.4500 - (numId % 15) * 0.0012;
        }

        const statusObj = state.requestStatuses.find(rs => rs.slug === claim.estado);
        const color = statusObj ? statusObj.color : '#6b7280';

        return { lat, lng, color, id: claim.id };
    }, (id) => {
        if (typeof window.selectClaim === 'function') {
            window.selectClaim(id);
        }
    });
}
