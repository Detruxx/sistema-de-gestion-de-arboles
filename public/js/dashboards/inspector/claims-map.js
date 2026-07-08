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

export async function updateClaimsMapMarkers(filteredList = null) {
    if (!claimsMapObj) return;

    try {
        // Filtrar reclamos que tienen coordenadas
        const sourceList = filteredList || state.claims;
        const claimsWithCoords = sourceList.filter(c => c.latitude && c.longitude);

        updateMapMarkers(claimsMapObj, claimsWithCoords, (claim) => {
            // Buscar el color configurado para este estado
            const statusObj = state.requestStatuses.find(s => s.slug === claim.estado);
            const color = statusObj ? statusObj.color : '#6b7280';

            return {
                lat: claim.latitude,
                lng: claim.longitude,
                color: color,
                id: claim.id
            };
        }, (tracking_code) => {
            if (typeof window.selectClaim === 'function') {
                window.selectClaim(tracking_code);
            }
        });
    } catch (error) {
        console.error('Error al actualizar pines de reclamos en el mapa del inspector:', error);
    }
}
