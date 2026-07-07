/**
 * Principal (Mapa Público): Punto de entrada y orquestador general del mapa público.
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

    // 3. Cargar datos iniciales y centrar en el árbol si se recibe un id por url query param (?id=...)
    loadTreesFromDatabase().then(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const treeId = urlParams.get('id');
        if (treeId) {
            const arboles = getArboles();
            const matched = arboles.find(a => a.id == treeId);
            if (matched) {
                mostrarDatosArbol(matched.id);
                map.flyTo([matched.latitude, matched.longitude], 18, { duration: 1.2 });
            }
        }
    });

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

        // Escuchar cuando el buscador inteligente devuelve coordenadas (Nominatim)
        searchInput.addEventListener('addressGeocoded', (e) => {
            const { lat, lng } = e.detail;
            
            // Volar hacia la direccion
            map.flyTo([lat, lng], 17, { duration: 1.5 });
        });
    }

    // 6. Recalcular tamaños de pines al hacer zoom
    map.on('zoomend', updateMarkersSizeOnZoom);
});
