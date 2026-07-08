/**
 * Formulario: Lógica de validación e interacción para el formulario de solicitud de plantación.
 */

// Lógica para Trámite de Plantación
document.addEventListener('DOMContentLoaded', () => {
    const inputDireccion = document.getElementById('direccion-solicitud');
    if (!inputDireccion) return;
    const btnSelectMap = document.getElementById('btn-select-map');
    const mapModal = document.getElementById('address-map-modal');
    const mapModalClose = document.getElementById('address-map-modal-close');
    const btnConfirmAddress = document.getElementById('btn-confirm-address');
    const previewText = document.getElementById('address-preview-text');
    const addressMapBody = document.querySelector('.address-map-body');
    
    let selectorMap = null;
    let currentCoordsAddress = '';
    let debounceTimer = null;

    function initSelectorMap() {
        if (selectorMap) return;

        // Centrar en Plaza Armenia, Palermo (-34.5888, -58.4285)
        selectorMap = L.map('address-map-canvas-plantacion', {
            zoomControl: false
        }).setView([-34.5888, -58.4285], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(selectorMap);

        L.control.zoom({ position: 'topright' }).addTo(selectorMap);

        // Geolocalizar al usuario automáticamente al cargar el mapa (salto instantáneo)
        selectorMap.locate({ setView: true, maxZoom: 17 });

        selectorMap.on('locationfound', function(e) {
            const myLocationIcon = L.divIcon({
                className: 'my-location-marker',
                html: `
                    <div style="width: 16px; height: 16px; background: #3b82f6; border: 2.5px solid white; border-radius: 50%; box-shadow: 0 0 8px rgba(0,0,0,0.4);"></div>
                `,
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });
            L.marker(e.latlng, { icon: myLocationIcon, zIndexOffset: 1000 }).addTo(selectorMap);
        });

        // Función de geocodificación reversa delegada al servicio compartido
        async function reverseGeocode(lat, lng) {
            if(previewText) previewText.textContent = 'Buscando dirección...';
            if(btnConfirmAddress) btnConfirmAddress.disabled = true;

            if (typeof window.reverseGeocodeService === 'function') {
                const address = await window.reverseGeocodeService(lat, lng);
                currentCoordsAddress = address;
                
                if(previewText) previewText.textContent = currentCoordsAddress;
                if(btnConfirmAddress) btnConfirmAddress.disabled = false;
            } else {
                console.error('El servicio reverseGeocodeService no está disponible.');
                currentCoordsAddress = `Ubicación: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                if(previewText) previewText.textContent = currentCoordsAddress;
                if(btnConfirmAddress) btnConfirmAddress.disabled = false;
            }
        }

        // Cargar dirección inicial
        const initialCenter = selectorMap.getCenter();
        reverseGeocode(initialCenter.lat, initialCenter.lng);

        // Añadir efectos físicos de salto al pin
        selectorMap.on('movestart', () => {
            if(addressMapBody) addressMapBody.classList.add('map-moving');
        });

        selectorMap.on('moveend', () => {
            if(addressMapBody) addressMapBody.classList.remove('map-moving');
            
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const center = selectorMap.getCenter();
                reverseGeocode(center.lat, center.lng);
            }, 500);
        });
    }

    if(btnSelectMap) {
        btnSelectMap.addEventListener('click', () => {
            if(mapModal) mapModal.classList.add('active');
            setTimeout(() => {
                initSelectorMap();
                if (selectorMap) {
                    selectorMap.invalidateSize();
                }
            }, 100);
        });
    }

    if(mapModalClose) {
        mapModalClose.addEventListener('click', () => {
            if(mapModal) mapModal.classList.remove('active');
        });
    }

    if(btnConfirmAddress) {
        btnConfirmAddress.addEventListener('click', () => {
            if (currentCoordsAddress) {
                if(inputDireccion) inputDireccion.value = currentCoordsAddress;
            }
            if(mapModal) mapModal.classList.remove('active');
        });
    }
});
