/**
 * Formulario: Lógica de validación e interacción para el formulario de solicitud de plantación.
 */
import { submitClaim } from './claims/api.js';

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

        selectorMap.on('locationfound', function (e) {
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
            if (previewText) previewText.textContent = 'Buscando dirección...';
            if (btnConfirmAddress) btnConfirmAddress.disabled = true;

            if (typeof window.reverseGeocodeService === 'function') {
                const address = await window.reverseGeocodeService(lat, lng);
                currentCoordsAddress = address;

                if (previewText) previewText.textContent = currentCoordsAddress;
                if (btnConfirmAddress) btnConfirmAddress.disabled = false;
            } else {
                console.error('El servicio reverseGeocodeService no está disponible.');
                currentCoordsAddress = `Ubicación: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                if (previewText) previewText.textContent = currentCoordsAddress;
                if (btnConfirmAddress) btnConfirmAddress.disabled = false;
            }
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

    if (btnSelectMap) {
        btnSelectMap.addEventListener('click', () => {
            if (mapModal) mapModal.classList.add('active');
            setTimeout(() => {
                initSelectorMap();
                if (selectorMap) {
                    selectorMap.invalidateSize();
                }
            }, 100);
        });
    }

    if (mapModalClose) {
        mapModalClose.addEventListener('click', () => {
            if (mapModal) mapModal.classList.remove('active');
        });
    }

    if (btnConfirmAddress) {
        btnConfirmAddress.addEventListener('click', () => {
            if (currentCoordsAddress) {
                if (inputDireccion) inputDireccion.value = currentCoordsAddress;
            }
            if (mapModal) mapModal.classList.remove('active');
        });
    }

    const plantacionForm = document.getElementById('plantacion-form');
    if (plantacionForm) {
        plantacionForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '';

            const formData = new FormData();
            formData.append('request_type_id', '4'); // ID tipo de reclamo para plantación
            formData.append('address', inputDireccion ? inputDireccion.value.trim() : '');

            let descriptionText = '';
            const cazuelaEl = document.getElementById('cazuela-estado');
            if (cazuelaEl && cazuelaEl.value) {
                const mapCazuela = {
                    'si': 'Sí, está abierta y con tierra suelta',
                    'cemento': 'No, la vereda está completamente cementada',
                    'tocon': 'No, hay un tronco/muñón viejo que debe extraerse primero'
                };
                const estadoTexto = mapCazuela[cazuelaEl.value] || cazuelaEl.value;
                descriptionText += `Estado de la cazuela: ${estadoTexto}.\n`;
            }

            const descriptionInput = document.getElementById('descripcion-plantacion');
            if (descriptionInput && descriptionInput.value.trim() !== '') {
                descriptionText += `Detalles/Motivo: ${descriptionInput.value.trim()}`;
            }

            formData.append('description', descriptionText.trim());

            // Validar e ingresar fotos si existen
            const fileInput = document.getElementById('foto-plantacion');
            if (fileInput && fileInput.files.length > 0) {
                if (fileInput.files.length > 3) {
                    alert('Solo se permite adjuntar un máximo de 3 fotos.');
                    return;
                }

                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    const maxSize = 10 * 1024 * 1024; // 10 MB
                    if (file.size > maxSize) {
                        alert(`La foto "${file.name}" supera el tamaño máximo permitido de 10MB.`);
                        return;
                    }

                    const allowedExtensions = ['.jpg', '.jpeg', '.png', '.webp', '.heic'];
                    const fileName = file.name.toLowerCase();
                    const isValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));

                    if (!isValidExtension) {
                        alert(`El archivo "${file.name}" no es válido. Sube únicamente imágenes (JPG, PNG, WEBP).`);
                        return;
                    }

                    formData.append('foto[]', file);
                }
            }

            try {
                const result = await submitClaim(formData, token);
                if (typeof window.openAlertModal === 'function') {
                    const modalSuccess = document.getElementById('alert-modal-success');
                    if (modalSuccess) {
                        const msgEl = modalSuccess.querySelector('.alert-modal-message');
                        if (msgEl) msgEl.textContent = `Tu solicitud de plantación ha sido registrada correctamente con el código: ${result.data.tracking_code}`;
                    }
                    window.openAlertModal('alert-modal-success');
                } else {
                    alert(`Solicitud registrada con éxito bajo el código: ${result.data.tracking_code}`);
                }
                plantacionForm.reset();

                // Reiniciar el texto del archivo adjunto
                const labelFile = document.getElementById('foto-plantacion-name');
                if (labelFile) labelFile.textContent = 'Ningún archivo seleccionado';
            } catch (err) {
                console.error('Submit error:', err);
                if (typeof window.openAlertModal === 'function') {
                    const modalError = document.getElementById('alert-modal-error');
                    if (modalError) {
                        const msgEl = modalError.querySelector('.alert-modal-message');
                        if (msgEl) msgEl.textContent = err.message || 'Ocurrió un error al procesar tu solicitud.';
                    }
                    window.openAlertModal('alert-modal-error');
                } else {
                    alert(err.message || 'Error al intentar registrar la solicitud de plantación.');
                }
            }
        });
    }
});
