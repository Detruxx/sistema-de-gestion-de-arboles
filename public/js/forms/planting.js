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

        // Función de geocodificación reversa usando Nominatim
        function reverseGeocode(lat, lng) {
            if(previewText) previewText.textContent = 'Buscando dirección...';
            if(btnConfirmAddress) btnConfirmAddress.disabled = true;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        const road = data.address.road || data.address.pedestrian || data.address.path || '';
                        const number = data.address.house_number || '';
                        const suburb = data.address.suburb || data.address.neighbourhood || '';
                        
                        if (road) {
                            currentCoordsAddress = road + (number ? ' ' + number : '') + (suburb ? ', ' + suburb : '');
                        } else {
                            currentCoordsAddress = data.display_name.split(',').slice(0, 3).join(',').trim();
                        }
                    } else {
                        currentCoordsAddress = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    }
                    if(previewText) previewText.textContent = currentCoordsAddress;
                    if(btnConfirmAddress) btnConfirmAddress.disabled = false;
                })
                .catch(err => {
                    console.error('Nominatim error, usando fallback:', err);
                    // Fallback de simulación en Palermo según cercanía
                    const fallbacks = [
                        { lat: -34.5888, lng: -58.4285, address: 'Costa Rica 4600' },
                        { lat: -34.5795, lng: -58.4148, address: 'Av. Sarmiento 2400' },
                        { lat: -34.6178, lng: -58.3712, address: 'Defensa 850' },
                        { lat: -34.5835, lng: -58.3927, address: 'Plaza Francia 1100' },
                        { lat: -34.5615, lng: -58.4552, address: 'Juramento 1900' }
                    ];
                    
                    let closest = fallbacks[0];
                    let minDist = Infinity;
                    fallbacks.forEach(f => {
                        let dist = Math.pow(f.lat - lat, 2) + Math.pow(f.lng - lng, 2);
                        if (dist < minDist) {
                            minDist = dist;
                            closest = f;
                        }
                    });
                    
                    const simulatedNumber = Math.floor(100 + Math.random() * 800) * 10;
                    const streetName = closest.address.split(' ').slice(0, -1).join(' ') || closest.address.split(' ')[0];
                    currentCoordsAddress = streetName + ' ' + simulatedNumber + ', Palermo, CABA';
                    if(previewText) previewText.textContent = currentCoordsAddress;
                    if(btnConfirmAddress) btnConfirmAddress.disabled = false;
                });
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
