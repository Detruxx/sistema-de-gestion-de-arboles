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

export async function updateClaimsMapMarkers() {
    if (!claimsMapObj) return;

    try {
        const response = await fetch('/api/reclamos/pines');
        if (!response.ok) return;

        const result = await response.json();
        if (result.status !== 'success' || !result.data) return;

        updateMapMarkers(claimsMapObj, result.data, (claim) => {
            return {
                lat: claim.latitude,
                lng: claim.longitude,
                color: claim.estado_color || '#6b7280',
                id: claim.tracking_code
            };
        }, (tracking_code) => {
            if (typeof window.selectClaim === 'function') {
                window.selectClaim(tracking_code);
            }
        });
    } catch (error) {
        console.error('Error al cargar pines de reclamos en el mapa del inspector:', error);
    }
}
