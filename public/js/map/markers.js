/**
 * Componente (Mapa Público): Lógica encargada de la generación, clusterización y renderizado de marcadores.
 */

import { getMap } from './core.js';
import { mostrarDatosArbol } from './api.js';

let mapMarkers = [];
let arboles = [];

export function setArboles(data) {
    arboles = data;
}

export function getArboles() {
    return arboles;
}

// Exponemos la función globalmente para poder inyectarla como string HTML en los popups de Leaflet
window.abrirDetalleArbol = function (arbolId, lat, lng) {
    mostrarDatosArbol(arbolId);
    const map = getMap();
    if (map && lat && lng) {
        map.flyTo([lat, lng], 16, { duration: 0.5 });
    }
};

export function getMarkerStyle(zoom, totalFeatures) {
    let radius, weight;
    // Radio (interpolación lineal)
    if (totalFeatures < 20000) {
        if (zoom <= 10) radius = 2;
        else if (zoom <= 14) radius = 2 + (6 - 2) * ((zoom - 10) / (14 - 10));
        else if (zoom <= 21) radius = 6 + (8 - 6) * ((zoom - 14) / (21 - 14));
        else radius = 8;
    } else {
        if (zoom <= 12) radius = 0.8;
        else if (zoom <= 14) radius = 0.8 + (5 - 0.8) * ((zoom - 12) / (14 - 12));
        else if (zoom <= 21) radius = 5 + (8 - 5) * ((zoom - 14) / (21 - 14));
        else radius = 8;
    }

    // Grosor del borde (stroke-width)
    if (zoom <= 12) weight = 0;
    else if (zoom <= 18) weight = 0 + (1 - 0) * ((zoom - 12) / (18 - 12));
    else weight = 1;

    return { radius, weight };
}

export function updateMarkersSizeOnZoom() {
    const map = getMap();
    if (!map) return;

    const zoom = map.getZoom();
    const style = getMarkerStyle(zoom, arboles.length);

    mapMarkers.forEach(marker => {
        marker.setRadius(style.radius);
        marker.setStyle({ weight: style.weight });
    });
}

export function renderMapMarkers() {
    const map = getMap();
    if (!map) return;

    // Limpiar marcadores viejos del mapa
    mapMarkers.forEach(m => map.removeLayer(m));
    mapMarkers = [];

    // Obtener valores de los filtros
    const filterEspecieElem = document.getElementById('filter-especie');
    const filterAlturaElem = document.getElementById('filter-altura');
    const filterEspecie = filterEspecieElem ? filterEspecieElem.value : '';
    const filterAltura = filterAlturaElem ? filterAlturaElem.value : '';

    arboles.forEach(arbol => {
        const nombreEspecie = arbol.specie ? arbol.specie.common_name : '';
        if (filterEspecie && nombreEspecie !== filterEspecie) return;

        if (filterAltura) {
            const altura = parseFloat(arbol.height);
            if (filterAltura === 'bajo' && altura >= 6) return;
            if (filterAltura === 'medio' && (altura < 6 || altura > 12)) return;
            if (filterAltura === 'alto' && altura <= 12) return;
        }

        const currentZoom = map.getZoom();
        const style = getMarkerStyle(currentZoom, arboles.length);

        const marker = L.circleMarker([arbol.latitude, arbol.longitude], {
            radius: style.radius,
            fillColor: '#2d7a4f',
            color: '#ffffff',
            weight: style.weight,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);

        let direccionBasica = 'Sin dirección';
        if (arbol.park) {
            direccionBasica = arbol.park.park_name;
        } else if (arbol.street) {
            direccionBasica = `${arbol.street.street_name} ${arbol.street.door_plate || arbol.street.street_number || ''}`.trim();
        }
        const nombreEspeciePopup = arbol.specie ? arbol.specie.common_name : 'Desconocida';

        // Aca ponemos el bloque HTML que va a mostrar el popup. 
        // Lo ponemos aca porque si borramos el popup se borra el HTML con el codigo
        const popupHtml = `
            <div class="tree-popup">
                <h4 class="tree-popup-title">${nombreEspeciePopup}</h4>
                <p class="tree-popup-address">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span class="address-text">${direccionBasica}</span>
                </p>
                <div class="tree-popup-id" style="font-size: 0.8rem; color: #666; margin-bottom: 8px;">#${arbol.id}</div>
                <button class="btn-main-cta tree-popup-btn" style="width: 100%;" onclick="window.abrirDetalleArbol(${arbol.id}, ${arbol.latitude}, ${arbol.longitude})">
                    Ver más datos
                </button>
            </div>
        `;

        marker.bindPopup(popupHtml, {
            closeButton: false,
            className: 'custom-tree-popup',
            offset: [0, -5]
        });

        mapMarkers.push(marker);
    });
}
