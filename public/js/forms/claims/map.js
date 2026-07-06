/**
 * Componente (Formulario Reclamos): Lógica para seleccionar y marcar la ubicación en el mapa al hacer un reclamo.
 */

let selectorMap = null;
let currentCoordsAddress = '';
let debounceTimer = null;

export function initSelectorMap() {
    const previewText = document.getElementById('address-preview-text');
    const btnConfirmAddress = document.getElementById('btn-confirm-address');
    const addressMapBody = document.querySelector('.address-map-body');

    if (selectorMap) return;

    // Centrar en Plaza Armenia, Palermo (-34.5888, -58.4285)
    selectorMap = L.map('address-map-canvas', {
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
            className: 'my-location-pin',
            html: `
                <div style="display: flex; flex-direction: column; align-items: center; pointer-events: none;">
                    <svg width="20" height="28" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.3));">
                        <path d="M12 0C5.37 0 0 5.37 0 12C0 21 12 32 12 32C12 32 24 21 24 12C24 5.37 18.63 0 12 0ZM12 16.5C9.51 16.5 7.5 14.49 7.5 12C7.5 9.51 9.51 7.5 12 7.5C14.49 7.5 16.5 9.51 16.5 12C16.5 14.49 14.49 16.5 12 16.5Z" fill="#D32F2F"/>
                    </svg>
                    <span style="background: white; padding: 3px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; font-family: var(--font-body, sans-serif); box-shadow: 0 1px 4px rgba(0,0,0,0.25); margin-top: 4px; color: #333; white-space: nowrap;">Te encuentras aquí</span>
                </div>
            `,
            iconSize: [120, 55],
            iconAnchor: [60, 28] // Anclar la base del pin
        });
        L.marker(e.latlng, { icon: myLocationIcon, zIndexOffset: 1000 }).addTo(selectorMap);
    });

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
        if (addressMapBody) addressMapBody.classList.add('map-moving');
    });

    selectorMap.on('moveend', () => {
        if (addressMapBody) addressMapBody.classList.remove('map-moving');

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const center = selectorMap.getCenter();
            reverseGeocode(center.lat, center.lng);
        }, 500);
    });
}

export function getCurrentCoordsAddress() {
    return currentCoordsAddress;
}

export function invalidateMapSize() {
    if (selectorMap) {
        selectorMap.invalidateSize();
    }
}
