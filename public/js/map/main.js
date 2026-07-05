/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { initMap, getMap } from './core.js';
import { setupUI } from './ui.js';
import { loadTreesFromDatabase, mostrarDatosArbol } from './api.js';
import { renderMapMarkers, updateMarkersSizeOnZoom, getArboles } from './markers.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicializar el Mapa Base
    const map = initMap();
    if (!map) return; // Si no hay mapa en esta página, salimos.

    // 2. Configurar Eventos de UI
    setupUI();

    // 3. Cargar datos iniciales
    loadTreesFromDatabase();

    // 4. Configurar listeners de filtros
    const filterEspecie = document.getElementById('filter-especie');
    const filterAltura = document.getElementById('filter-altura');
    if (filterEspecie) filterEspecie.addEventListener('change', renderMapMarkers);
    if (filterAltura) filterAltura.addEventListener('change', renderMapMarkers);

    // 5. Configurar búsqueda
    const searchBtn = document.getElementById('map-search-btn');
    const searchInput = document.getElementById('map-search-input');

    function searchAddress() {
        const query = searchInput.value.toLowerCase().trim();
        if (!query) return;

        const arboles = getArboles();
        const matched = arboles.find(a => {
            const nombreEspecie = a.specie ? a.specie.common_name.toLowerCase() : '';
            const nombreCalle = a.street ? a.street.street_name.toLowerCase() : '';
            const nombrePlaza = a.park ? a.park.park_name.toLowerCase() : '';
            return nombreCalle.includes(query) || nombrePlaza.includes(query) || nombreEspecie.includes(query);
        });

        if (matched) {
            mostrarDatosArbol(matched.id);
            map.flyTo([matched.latitude, matched.longitude], 16, { duration: 0.5 });
        } else {
            alert('No se encontró ningún árbol que coincida con la búsqueda.');
        }
    }

    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', searchAddress);
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') searchAddress();
        });
    }

    // 6. Recalcular tamaños de pines al hacer zoom
    map.on('zoomend', updateMarkersSizeOnZoom);
});
