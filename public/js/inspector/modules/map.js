// --- Lógica del Mapa de Árboles ---

window.adminMap = null;
window.adminMarker = null;

window.openCreateTreeModal = function() {
    document.getElementById('create-tree-modal').classList.add('active');
    
    setTimeout(() => {
        if (!window.adminMap) {
            const mapCanvas = document.getElementById('admin-tree-map-canvas');
            if(mapCanvas) {
                window.adminMap = L.map('admin-tree-map-canvas', {
                    zoomControl: false
                }).setView([-34.5888, -58.4285], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(window.adminMap);

                L.control.zoom({ position: 'bottomright' }).addTo(window.adminMap);

                window.adminMap.on('click', (e) => {
                    window.setMarkerPosition(e.latlng.lat, e.latlng.lng);
                });
            }
        } else {
            window.adminMap.invalidateSize();
        }

        const latInput = document.getElementById('new-tree-lat');
        if (latInput && !latInput.value) {
            window.setMarkerPosition(-34.5888, -58.4285);
        }
    }, 200);
};

window.closeCreateTreeModal = function() {
    const modal = document.getElementById('create-tree-modal');
    if(modal) modal.classList.remove('active');
};

window.setMarkerPosition = function(lat, lng) {
    const latInput = document.getElementById('new-tree-lat');
    const lngInput = document.getElementById('new-tree-lng');
    
    if(latInput) latInput.value = lat.toFixed(6);
    if(lngInput) lngInput.value = lng.toFixed(6);

    if (window.adminMarker) {
        window.adminMarker.setLatLng([lat, lng]);
    } else {
        if(window.adminMap) {
            window.adminMarker = L.marker([lat, lng], { draggable: true }).addTo(window.adminMap);
            window.adminMarker.on('dragend', () => {
                const position = window.adminMarker.getLatLng();
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
                        'X-CSRF-TOKEN': window.getCsrfToken()
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert('Árbol registrado exitosamente.');
                    window.closeCreateTreeModal();
                    e.target.reset();
                    if (window.adminMarker) {
                        window.adminMap.removeLayer(window.adminMarker);
                        window.adminMarker = null;
                    }
                    if (typeof window.loadTreesFromServer === 'function') {
                        window.loadTreesFromServer();
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
