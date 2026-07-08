/**
 * Map (Dashboard Empresa): Lógica de mapa genérico.
 */

import { initGenericMap, triggerMapResize, updateMapMarkers } from '../shared/map-module.js';

let companyMapObj = null;
let stateRef = null;

export function initCompanyMap(stateObj) {
    stateRef = stateObj;
    const mapContainer = document.getElementById('company-jobs-map');
    if (!mapContainer || companyMapObj) return;

    companyMapObj = initGenericMap('company-jobs-map');
}

export function triggerCompanyMapResize() {
    if (companyMapObj && companyMapObj.mapInstance) {
        triggerMapResize(companyMapObj.mapInstance);
    }
}

export function updateCompanyMapMarkers(filteredList = null) {
    if (!companyMapObj || !stateRef) return;

    // Solo mapeamos los que NO están finalizados para la vista principal
    const activeJobs = stateRef.jobs.filter(j => j.work_status !== 'Finalizado');
    const jobsToMap = filteredList || activeJobs;

    updateMapMarkers(companyMapObj, jobsToMap, (job) => {
        let lat = null;
        let lng = null;

        // Intentar sacar lat/lng del request si existe
        if (job.request && (job.request.lat || job.request.latitude)) {
            lat = job.request.lat || job.request.latitude;
            lng = job.request.lng || job.request.longitude;
        }

        // Dummy coords si no hay reales
        if (!lat || !lng) {
            const numId = parseInt(job.id) || 0;
            lat = -34.5700 - (numId % 20) * 0.0015;
            lng = -58.4500 - (numId % 15) * 0.0012;
        }

        let color = '#3498db'; // Asignado
        if (job.work_status === 'En Proceso') color = '#e67e22';
        else if (job.work_status === 'En espera') color = '#95a5a6';

        return { lat, lng, color, id: job.id };
    }, (id) => {
        if (typeof window.selectJob === 'function') {
            window.selectJob(id);
        }
    });
}
