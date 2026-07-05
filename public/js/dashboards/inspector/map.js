/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { state } from './state.js';
export function openCreateTreeModal() {
    document.getElementById('create-tree-modal').classList.add('active');
    
    setTimeout(() => {
        if (!state.adminMap) {
            const mapCanvas = document.getElementById('admin-tree-map-canvas');
            if(mapCanvas) {
                state.adminMap = L.map('admin-tree-map-canvas', {
                    zoomControl: false
                }).setView([-34.5888, -58.4285], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(state.adminMap);

                L.control.zoom({ position: 'bottomright' }).addTo(state.adminMap);

                state.adminMap.on('click', (e) => {
                    setMarkerPosition(e.latlng.lat, e.latlng.lng);
                });
            }
        } else {
            state.adminMap.invalidateSize();
        }

        const latInput = document.getElementById('new-tree-lat');
        if (latInput && !latInput.value) {
            setMarkerPosition(-34.5888, -58.4285);
        }
    }, 200);
};

export function closeCreateTreeModal() {
    const modal = document.getElementById('create-tree-modal');
    if(modal) modal.classList.remove('active');
};

export function setMarkerPosition(lat, lng) {
    const latInput = document.getElementById('new-tree-lat');
    const lngInput = document.getElementById('new-tree-lng');
    
    if(latInput) latInput.value = lat.toFixed(6);
    if(lngInput) lngInput.value = lng.toFixed(6);

    if (state.adminMarker) {
        state.adminMarker.setLatLng([lat, lng]);
    } else {
        if(state.adminMap) {
            state.adminMarker = L.marker([lat, lng], { draggable: true }).addTo(state.adminMap);
            state.adminMarker.on('dragend', () => {
                const position = state.adminMarker.getLatLng();
                if(latInput) latInput.value = position.lat.toFixed(6);
                if(lngInput) lngInput.value = position.lng.toFixed(6);
            });
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const formCreateTree = document.getElementById('form-create-tree');
    if (formCreateTree) {
        formCreateTree.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/admin/arboles', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert('Árbol registrado exitosamente.');
                    closeCreateTreeModal();
                    e.target.reset();
                    if (state.adminMarker) {
                        state.adminMap.removeLayer(state.adminMarker);
                        state.adminMarker = null;
                    }
                    if (typeof state.loadTreesFromServer === 'function') {
                        loadTreesFromServer();
                    }
                } else {
                    alert('Error al registrar el árbol.');
                }
            } catch (err) {
                console.error('Error saving tree:', err);
                alert('Error de conexión.');
            }
        });
    }
});
