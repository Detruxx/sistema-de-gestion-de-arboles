/**
 * Componente (Mapa Público): Lógica encargada de la generación, clusterización y renderizado de marcadores.
 */

import { getMap } from './core.js';
import { mostrarDatosArbol } from './api.js';

let arboles = [];
let reclamos = [];

// Grupos de capas
let treeClusterGroup = null;
let treeRawGroup = null;
let claimsGroup = null;
let rawMarkersArray = []; // Para poder recalcular radios si el clustering está apagado

export function setArboles(data) {
    arboles = data;
}

export function getArboles() {
    return arboles;
}

export function setReclamos(data) {
    reclamos = data;
}

export function getReclamos() {
    return reclamos;
}

// Inicializa los grupos de capas una sola vez
function initLayerGroups(map) {
    if (!treeClusterGroup) {
        // Configuramos el cluster group
        treeClusterGroup = L.markerClusterGroup({
            chunkedLoading: true, // Importante para muchos marcadores
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            disableClusteringAtZoom: 18 // Se expanden individualmente cuando hay mucho zoom
        });
    }
    if (!treeRawGroup) {
        treeRawGroup = L.layerGroup();
    }
    if (!claimsGroup) {
        claimsGroup = L.layerGroup();
    }
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

    // Solo actualizamos el tamaño si el clustering está APAGADO.
    // Si el clustering está encendido, usamos un tamaño constante o dejamos que Leaflet se encargue.
    const toggleClusteringElem = document.getElementById('toggle-clustering');
    const isClusteringEnabled = toggleClusteringElem ? toggleClusteringElem.checked : true;

    if (isClusteringEnabled) return;

    const zoom = map.getZoom();
    const style = getMarkerStyle(zoom, arboles.length);

    rawMarkersArray.forEach(marker => {
        marker.setRadius(style.radius);
        marker.setStyle({ weight: style.weight });
    });
}

export function renderMapMarkers() {
    const map = getMap();
    if (!map) return;

    initLayerGroups(map);

    // Limpiar capas del mapa y vaciar grupos
    if (map.hasLayer(treeClusterGroup)) map.removeLayer(treeClusterGroup);
    if (map.hasLayer(treeRawGroup)) map.removeLayer(treeRawGroup);
    if (map.hasLayer(claimsGroup)) map.removeLayer(claimsGroup);
    
    treeClusterGroup.clearLayers();
    treeRawGroup.clearLayers();
    claimsGroup.clearLayers();
    rawMarkersArray = [];

    // Obtener valores de los filtros
    const filterEspecieElem = document.getElementById('filter-especie');
    const filterAlturaElem = document.getElementById('filter-altura');
    const filterDisplayElem = document.getElementById('filter-display-type');
    const toggleClusteringElem = document.getElementById('toggle-clustering');

    const filterEspecie = filterEspecieElem ? filterEspecieElem.value : '';
    const filterAltura = filterAlturaElem ? filterAlturaElem.value : '';
    const displayType = filterDisplayElem ? filterDisplayElem.value : 'both';
    const isClusteringEnabled = toggleClusteringElem ? toggleClusteringElem.checked : true;

    // Mostrar/ocultar alerta de rendimiento
    const warningElem = document.getElementById('clustering-warning');
    if (warningElem) {
        warningElem.style.display = isClusteringEnabled ? 'none' : 'block';
    }

    const currentZoom = map.getZoom();
    const style = getMarkerStyle(currentZoom, arboles.length);

    // --- RENDERIZAR ÁRBOLES ---
    if (displayType === 'both' || displayType === 'trees') {
        const markersToAdd = [];

        arboles.forEach(arbol => {
            const nombreEspecie = arbol.specie ? arbol.specie.common_name : '';
            if (filterEspecie && nombreEspecie !== filterEspecie) return;

            if (filterAltura) {
                const altura = parseFloat(arbol.height);
                if (filterAltura === 'bajo' && altura >= 6) return;
                if (filterAltura === 'medio' && (altura < 6 || altura > 12)) return;
                if (filterAltura === 'alto' && altura <= 12) return;
            }

            // Si usamos clustering, el radius fijo es suficiente.
            // Si usamos raw (sin clustering), usamos el style dinámico calculado.
            const markerRadius = isClusteringEnabled ? 5 : style.radius;
            const markerWeight = isClusteringEnabled ? 1 : style.weight;

            const marker = L.circleMarker([arbol.latitude, arbol.longitude], {
                radius: markerRadius,
                fillColor: '#2d7a4f',
                color: '#ffffff',
                weight: markerWeight,
                opacity: 1,
                fillOpacity: 0.8
            });

            let direccionBasica = 'Sin dirección';
            if (arbol.park) {
                direccionBasica = arbol.park.park_name;
            } else if (arbol.street) {
                direccionBasica = `${arbol.street.street_name} ${arbol.street.door_plate || arbol.street.street_number || ''}`.trim();
            }
            const nombreEspeciePopup = arbol.specie ? arbol.specie.common_name : 'Desconocida';

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

            if (isClusteringEnabled) {
                markersToAdd.push(marker);
            } else {
                treeRawGroup.addLayer(marker);
                rawMarkersArray.push(marker);
            }
        });

        if (isClusteringEnabled) {
            treeClusterGroup.addLayers(markersToAdd);
            map.addLayer(treeClusterGroup);
        } else {
            map.addLayer(treeRawGroup);
        }
    }

    // --- RENDERIZAR RECLAMOS ---
    if (displayType === 'both' || displayType === 'claims') {
        reclamos.forEach(claim => {
            const claim_color = claim.estado_color || '#ef4444';
            const claimIcon = L.divIcon({
                className: 'claim-marker-icon',
                html: `<div style="width: 12px; height: 12px; background: ${claim_color}; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 6px rgba(0,0,0,0.5);"></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            const claim_marker = L.marker([claim.latitude, claim.longitude], {
                icon: claimIcon,
                zIndexOffset: 500
            });

            const popup_html = `
                <div class="tree-popup" style="min-width: 180px;">
                    <h4 class="tree-popup-title" style="font-size: 0.95rem; margin-bottom: 4px;">${claim.categoria}</h4>
                    <p class="tree-popup-address" style="margin: 4px 0; font-size: 0.85rem; color: #555;">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" style="vertical-align: middle;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>${claim.direccion}</span>
                    </p>
                    <div style="font-size: 0.8rem; color: #666; margin-bottom: 6px;">${claim.tracking_code}</div>
                    <div style="display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; color: white; background: ${claim_color};">${claim.estado}</div>
                </div>
            `;

            claim_marker.bindPopup(popup_html, {
                closeButton: false,
                className: 'custom-tree-popup',
                offset: [0, -5]
            });

            claimsGroup.addLayer(claim_marker);
        });

        map.addLayer(claimsGroup);
    }
}
