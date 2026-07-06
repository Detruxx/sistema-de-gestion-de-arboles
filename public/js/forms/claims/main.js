/**
 * Principal (Formulario Reclamos): Punto de entrada y orquestador del flujo del formulario de reclamos.
 */

import { fetchRequestTypes, submitClaim, fetchClaimDetails, fetchRequestStatuses } from './api.js';
import { populateRequestTypes, setSeleccionArbolUI, initTabSwitching, renderTrackingResult, showTrackError, hideTrackMessages } from './ui.js';
import { initTreeSelectionLogic } from './trees.js';
import { initSelectorMap, getCurrentCoordsAddress, invalidateMapSize } from './map.js';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Iniciar Tabs
    initTabSwitching();

    // 2. Cargar Tipos de Reclamo
    fetchRequestTypes()
        .then(types => populateRequestTypes(types))
        .catch(err => console.error('Error al cargar tipos de reclamo:', err));

    // 3. Iniciar Lógica de Selección de Árbol
    initTreeSelectionLogic(setSeleccionArbolUI);

    // 4. Iniciar Lógica del Mapa Selector
    const btnSelectMap = document.getElementById('btn-select-map');
    const mapModal = document.getElementById('address-map-modal');
    const mapModalClose = document.getElementById('address-map-modal-close');
    const btnConfirmAddress = document.getElementById('btn-confirm-address');

    const inputDireccion = document.getElementById('direccion');
    const inputArbolId = document.getElementById('arbol-id');

    if (btnSelectMap) {
        btnSelectMap.addEventListener('click', () => {
            if (mapModal) mapModal.classList.add('active');
            setTimeout(() => {
                initSelectorMap();
                invalidateMapSize();
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
            const currentCoordsAddress = getCurrentCoordsAddress();
            if (currentCoordsAddress) {
                if (inputDireccion) {
                    inputDireccion.value = currentCoordsAddress;
                    inputDireccion.readOnly = false;
                    inputDireccion.classList.remove('readonly-input');
                }
                if (inputArbolId) inputArbolId.value = '';
                setSeleccionArbolUI(null); // Limpia los banners
            }
            if (mapModal) mapModal.classList.remove('active');
        });
    }

    // 5. Iniciar Envío del Formulario
    const reclamoForm = document.getElementById('reclamo-form');
    if (reclamoForm) {
        reclamoForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '';

            const formData = new FormData();
            formData.append('request_type_id', document.getElementById('tipo-reclamo').value);

            if (inputArbolId && inputArbolId.value.trim() !== '') {
                formData.append('tree_id', inputArbolId.value.trim());
            }

            formData.append('address', inputDireccion ? inputDireccion.value.trim() : '');
            formData.append('description', document.getElementById('descripcion') ? document.getElementById('descripcion').value.trim() : '');

            // Aca revisamos las imagenes que subio el usuario y revisamos si son validas
            const fileInput = document.getElementById('foto');
            if (fileInput && fileInput.files.length > 0) {
                if (fileInput.files.length > 3) {
                    alert('Solo se permite adjuntar un máximo de 3 fotos por reclamo.');
                    return;
                }

                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];

                    // Validación de tamaño (Máx 10MB)
                    const maxSize = 10 * 1024 * 1024; // 10 MB
                    if (file.size > maxSize) {
                        alert(`La foto "${file.name}" supera el tamaño máximo permitido de 10MB.`);
                        return;
                    }

                    // Lista blanca de extensiones de imagen permitidas
                    const allowedExtensions = ['.jpg', '.jpeg', '.png', '.webp', '.heic'];
                    const fileName = file.name.toLowerCase();
                    const isValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));

                    if (!isValidExtension) {
                        alert(`El archivo "${file.name}" no es válido. Sube únicamente imágenes (JPG, PNG, WEBP).`);
                        return;
                    }

                    // Se envía como un arreglo (foto[]) para que el backend lo reciba como array
                    formData.append('foto[]', file);
                }
            }

            try {
                const result = await submitClaim(formData, token);
                if (typeof window.showSuccessModal === 'function') {
                    window.showSuccessModal('¡Reclamo Enviado!', `El reclamo se registró con éxito bajo el código de seguimiento: ${result.data.tracking_code}`);
                } else {
                    alert(`Reclamo registrado con éxito bajo el ID: ${result.data.tracking_code}`);
                }
                reclamoForm.reset();
                setSeleccionArbolUI(null);
            } catch (err) {
                console.error('Submit error:', err);
                alert('Error al intentar registrar el reclamo. Verifique consola.');
            }
        });
    }
});

// 6. Exponer Tracker
window.trackComplaint = async function () {
    const inputEl = document.getElementById('track-id-input');
    if (!inputEl) return;

    const inputVal = inputEl.value.trim().toUpperCase();
    if (!inputVal) {
        alert('Por favor ingresa un código de reclamo.');
        return;
    }

    hideTrackMessages();

    try {
        const result = await fetchClaimDetails(inputVal);
        const claim = result.data;

        const statusResult = await fetchRequestStatuses();
        const requestStatuses = statusResult.data;

        renderTrackingResult(claim, requestStatuses);
    } catch (err) {
        console.error("Error tracking claim:", err);
        showTrackError();
    }
};
