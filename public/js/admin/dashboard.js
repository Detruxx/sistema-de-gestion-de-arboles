// --- Base de Datos Dinámica ---
let claims = [];
let requestStatuses = [];

const trees = [
    { id: '10045', especie: 'Jacarandá', altura: 'Media (8-12m)', estado: 'Saludable', calle: 'Av. Cabildo 2900', edad: 'Adulto (15 años)', circun: '95cm' },
    { id: '20512', especie: 'Ceibo', altura: 'Baja (4-8m)', estado: 'Enfermo (Pulgones)', calle: 'Mendoza 1400', edad: 'Joven (5 años)', circun: '45cm' },
    { id: '30291', especie: 'Fresno', altura: 'Alta (>12m)', estado: 'Saludable', calle: 'Vuelta de Obligado 2100', edad: 'Adulto (25 años)', circun: '140cm' },
    { id: '40102', especie: 'Palo Borracho', altura: 'Alta (>12m)', estado: 'Dañado (Seco)', calle: 'La Pampa 1850', edad: 'Adulto (20 años)', circun: '110cm' },
    { id: '50981', especie: 'Tilo', altura: 'Media (8-12m)', estado: 'Saludable', calle: 'Amenábar 2300', edad: 'Adulto (12 años)', circun: '75cm' },
    { id: '60882', especie: 'Liquidámbar', altura: 'Baja (4-8m)', estado: 'Saludable', calle: 'Echeverría 1600', edad: 'Joven (3 años)', circun: '30cm' }
];

let selectedClaimId = null;
let selectedTreeId = null;

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// --- Cambio de Módulo ---
function showModule(moduleName) {
    document.querySelectorAll('.dashboard-module').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar-btn').forEach(el => el.classList.remove('active'));

    const moduleEl = document.getElementById(`module-${moduleName}`);
    if(moduleEl) moduleEl.classList.add('active');
    
    const menuEl = document.getElementById(`menu-${moduleName}`);
    if(menuEl) menuEl.classList.add('active');
}
window.showModule = showModule;

// --- Cargar Reclamos ---
function loadClaimsList() {
    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';
    
    claims.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => selectClaim(c.id);

        const statusObj = requestStatuses.find(rs => rs.slug === c.estado);
        let statusLabel = statusObj ? statusObj.status_name : c.estado.toUpperCase();
        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">${c.id}</span>
                <span class="badge-status ${c.estado}">${statusLabel}</span>
            </div>
            <div class="list-item-title">${c.categoria}</div>
            <div class="list-item-subtitle">${c.direccion}</div>
            <div style="font-size: 0.75rem; text-align: right; color: rgba(245,249,246,0.4); margin-top: 5px;">${c.fecha}</div>
        `;
        container.appendChild(card);
    });
}
window.loadClaimsList = loadClaimsList;

function selectClaim(id) {
    selectedClaimId = id;
    loadClaimsList();
    
    const claim = claims.find(c => c.id === id);
    const panel = document.getElementById('claim-detail-panel');

    if (!claim || !panel) return;

    // Renderizar panel detallado
    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${claim.categoria}</h3>
                <p class="detail-subtitle">Reclamo ID: <strong style="color:var(--admin-text-primary);">${claim.id}</strong> | Enviado el ${claim.fecha}</p>
            </div>
            <span class="badge-status ${claim.estado}" id="detail-badge-status">${claim.estado.toUpperCase()}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Vecino Solicitante</p>
            <p class="detail-value">${claim.vecino} (${claim.email})</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Dirección / Especie</p>
            <p class="detail-value">${claim.direccion} — Especie involucrada: ${claim.especie}</p>
        </div>

        <div class="detail-box">
            <p class="detail-label">Mensaje / Descripción del problema</p>
            <p class="detail-box-desc">${claim.descripcion}</p>
        </div>

        <!-- Progress Tracker -->
        <div class="status-tracker-container">
            <div class="status-tracker-title">Progreso del Reclamo (Haz clic en un paso para cambiar el estado)</div>
            <div class="status-steps">
                ${requestStatuses.map(s => {
                    const currentSeq = requestStatuses.find(rs => rs.slug === claim.estado)?.sequence || 0;
                    const isCompleted = s.sequence && s.sequence <= currentSeq;
                    const isActive = claim.estado === s.slug;
                    // Mostrar si tiene secuencia o si es el estado final activo
                    if (s.sequence || isActive) {
                        return `
                        <div class="status-step ${isCompleted ? 'completed' : ''} ${isActive ? 'active' : ''}" onclick="setClaimStatus('${s.slug}')">
                            <div class="step-circle" style="background-color: ${isActive ? 'var(--bs-' + s.color + ')' : ''}; border-color: ${isActive ? 'var(--bs-' + s.color + ')' : ''}">${s.sequence || '!'}</div>
                            <div class="step-label">${s.status_name}</div>
                        </div>`;
                    }
                    return '';
                }).join('')}
            </div>
        </div>

        <div class="response-section">
            <h4 class="detail-title" style="font-size: 1.2rem;">Responder al Vecino</h4>
            <div class="template-selector">
                <button class="template-btn" onclick="applyTemplate('info')">Pedir más info</button>
                <button class="template-btn" onclick="applyTemplate('relevated')">Avisar Inspección</button>
                <button class="template-btn" onclick="applyTemplate('scheduled')">Avisar Poda</button>
                <button class="template-btn" onclick="applyTemplate('resolved')">Informar Resolución</button>
                <button class="template-btn" onclick="applyTemplate('denied')">Rechazar</button>
            </div>
            <textarea id="response-text" class="response-textarea" placeholder="Escribe un mensaje personalizado para enviar al correo del vecino..."></textarea>
            <div class="action-row">
                <button class="btn-secondary" onclick="clearResponse()">Limpiar</button>
                <button class="btn-primary" onclick="sendResponse()">Enviar Respuesta y Actualizar</button>
            </div>
        </div>
    `;
}
window.selectClaim = selectClaim;

function applyTemplate(type) {
    const claim = claims.find(c => c.id === selectedClaimId);
    if (!claim) return;

    const textarea = document.getElementById('response-text');
    
    let text = '';
    if (type === 'open' || type === 'info') {
        text = `Estimado/a ${claim.vecino},\n\nHemos recibido su solicitud ID ${claim.id} sobre "${claim.categoria}". Un inspector del área técnica estará evaluando la situación a la brevedad. Si posee más imágenes del estado actual del ejemplar, por favor adjúntelas respondiendo a este correo.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'relevated') {
        text = `Estimado/a ${claim.vecino},\n\nLe informamos que su solicitud ID ${claim.id} se encuentra en etapa de Inspección Técnica. Personal calificado visitará la dirección ${claim.direccion} dentro de los próximos 3 días hábiles para diagnosticar el árbol (${claim.especie}) y planificar el plan de acción.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'scheduled' || type === 'in_progress') {
        text = `Estimado/a ${claim.vecino},\n\nTras la inspección realizada en ${claim.direccion}, se ha planificado la intervención correspondiente para el día [Fecha]. Se realizará un saneamiento/poda de despeje preventivo para resguardar la seguridad pública.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'resolved') {
        text = `Estimado/a ${claim.vecino},\n\nNos complace informarle que la solicitud ID ${claim.id} ha sido completada de manera exitosa. Las tareas operativas y el despeje final en la zona han concluido.\n\nMuchas gracias por colaborar con el mantenimiento del arbolado de la Ciudad.\n\nAtentamente,\nGobierno de la Ciudad de Buenos Aires - Comuna 13.`;
    } else if (type === 'denied') {
        text = `Estimado/a ${claim.vecino},\n\nTras la evaluación técnica de su solicitud ID ${claim.id}, lamentamos informarle que la misma ha sido rechazada por no cumplir con los criterios de intervención de la Ley de Arbolado.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'vinculated') {
        text = `Estimado/a ${claim.vecino},\n\nLe informamos que su solicitud ID ${claim.id} ha sido vinculada a un trámite preexistente sobre el mismo ejemplar o incidencia.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    }

    if (textarea) textarea.value = text;
}
window.applyTemplate = applyTemplate;

function clearResponse() {
    const textarea = document.getElementById('response-text');
    if (textarea) textarea.value = '';
}
window.clearResponse = clearResponse;

async function sendResponse() {
    const claim = claims.find(c => c.id === selectedClaimId);
    if (!claim) return;

    const responseText = document.getElementById('response-text').value;
    if (!responseText.trim()) {
        alert('Por favor escribe un mensaje de respuesta antes de enviar.');
        return;
    }

    try {
        const response = await fetch(`/requests/update-status/${selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ respuesta: responseText })
        });

        if (response.ok) {
            // Actualizar reclamo en memoria
            claim.respuesta_admin = responseText;
            
            // Mostrar banner de notificación
            const banner = document.getElementById('notification-banner');
            const text = document.getElementById('notification-text');
            if(text) text.innerText = `Respuesta enviada a ${claim.vecino} (${claim.email}) y guardada en el sistema.`;
            if(banner) banner.style.display = 'flex';

            setTimeout(() => {
                if(banner) banner.style.display = 'none';
            }, 5000);

            // Reset text area
            clearResponse();
        } else {
            alert('Error al guardar la respuesta en el servidor.');
        }
    } catch (err) {
        console.error("Error sending response:", err);
        alert('Error de conexión.');
    }
}
window.sendResponse = sendResponse;

function filterClaims() {
    const query = document.getElementById('search-claims').value.toLowerCase();
    const container = document.getElementById('claims-list-container');
    if(!container) return;
    container.innerHTML = '';

    const filtered = claims.filter(c => 
        c.vecino.toLowerCase().includes(query) || 
        c.direccion.toLowerCase().includes(query) ||
        c.id.toLowerCase().includes(query)
    );

    filtered.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => selectClaim(c.id);

        const statusObj = requestStatuses.find(rs => rs.slug === c.estado);
        let statusLabel = statusObj ? statusObj.status_name : c.estado.toUpperCase();
        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">${c.id}</span>
                <span class="badge-status ${c.estado}">${statusLabel}</span>
            </div>
            <div class="list-item-title">${c.categoria}</div>
            <div class="list-item-subtitle">${c.direccion}</div>
            <div style="font-size: 0.75rem; text-align: right; color: var(--admin-text-secondary); margin-top: 5px;">${c.fecha}</div>
        `;
        container.appendChild(card);
    });
}
window.filterClaims = filterClaims;

function updateStats() {
    const elTotal = document.getElementById('stat-total-claims');
    const elResueltos = document.getElementById('stat-resolved-claims');
    const elPendientes = document.getElementById('stat-pending-claims');
    const elUnread = document.getElementById('unread-count-badge');
    
    const isTerminal = (slug) => {
        const s = requestStatuses.find(rs => rs.slug === slug);
        return s ? s.is_terminal : false;
    };
    
    if(elTotal) elTotal.innerText = claims.length;
    if(elResueltos) elResueltos.innerText = claims.filter(c => isTerminal(c.estado)).length;
    if(elPendientes) elPendientes.innerText = claims.filter(c => !isTerminal(c.estado)).length;
    if(elUnread) elUnread.innerText = claims.filter(c => !isTerminal(c.estado)).length;
}
window.updateStats = updateStats;

// --- Cargar Árboles ---
function loadTreesList() {
    const container = document.getElementById('trees-list-container');
    if(!container) return;
    container.innerHTML = '';

    trees.forEach(t => {
        const card = document.createElement('div');
        card.className = `list-item-card ${selectedTreeId === t.id ? 'active' : ''}`;
        card.onclick = () => selectTree(t.id);

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">#ID ${t.id}</span>
                <span style="font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; background: var(--admin-accent-light); color: var(--admin-accent);">${t.estado}</span>
            </div>
            <div class="list-item-title">${t.especie}</div>
            <div class="list-item-subtitle">${t.calle}</div>
        `;
        container.appendChild(card);
    });
}
window.loadTreesList = loadTreesList;

function selectTree(id) {
    selectedTreeId = id;
    loadTreesList();

    const tree = trees.find(t => t.id === id);
    const panel = document.getElementById('tree-detail-panel');

    if (!tree || !panel) return;

    panel.innerHTML = `
        <div class="detail-header-panel">
            <h3 class="detail-title">Ficha Técnica del Ejemplar</h3>
            <p class="detail-subtitle">ID Árbol: <strong style="color:var(--admin-text-primary);">${tree.id}</strong></p>
        </div>

        <div class="tree-detail-grid">
            <div class="tree-detail-field">
                <div class="tree-detail-field-label">Especie</div>
                <div class="tree-detail-field-value">${tree.especie}</div>
            </div>
            <div class="tree-detail-field">
                <div class="tree-detail-field-label">Ubicación / Calle</div>
                <div class="tree-detail-field-value">${tree.calle}</div>
            </div>
            <div class="tree-detail-field">
                <div class="tree-detail-field-label">Estado de Salud</div>
                <div class="tree-detail-field-value" style="color: ${tree.estado.includes('Saludable') ? '#2ecc71' : '#f39c12'}">${tree.estado}</div>
            </div>
            <div class="tree-detail-field">
                <div class="tree-detail-field-label">Altura Promedio</div>
                <div class="tree-detail-field-value">${tree.altura}</div>
            </div>
            <div class="tree-detail-field">
                <div class="tree-detail-field-label">Edad Estimada</div>
                <div class="tree-detail-field-value">${tree.edad}</div>
            </div>
            <div class="tree-detail-field">
                <div class="tree-detail-field-label">Circunferencia del Tronco</div>
                <div class="tree-detail-field-value">${tree.circun}</div>
            </div>
        </div>

        <div class="mt-25" style="display: flex; justify-content: flex-end;">
            <a href="/mapa?id=${tree.id}" target="_blank" class="btn-primary sidebar-btn-link btn-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                </svg>
                Localizar en el Mapa
            </a>
        </div>
    `;
}
window.selectTree = selectTree;

function filterTrees() {
    const filterIdInput = document.getElementById('filter-tree-id');
    const filterSpeciesSelect = document.getElementById('filter-tree-species');
    const filterStateSelect = document.getElementById('filter-tree-state');
    
    const idQuery = filterIdInput ? filterIdInput.value.trim() : '';
    const speciesFilter = filterSpeciesSelect ? filterSpeciesSelect.value : '';
    const stateFilter = filterStateSelect ? filterStateSelect.value : '';
    
    const container = document.getElementById('trees-list-container');
    if(!container) return;
    container.innerHTML = '';

    const filtered = trees.filter(t => {
        const matchesId = idQuery === '' || t.id.includes(idQuery);
        const matchesSpecies = speciesFilter === '' || t.especie === speciesFilter;
        const matchesState = stateFilter === '' || t.estado.includes(stateFilter);
        return matchesId && matchesSpecies && matchesState;
    });

    filtered.forEach(t => {
        const card = document.createElement('div');
        card.className = `list-item-card ${selectedTreeId === t.id ? 'active' : ''}`;
        card.onclick = () => selectTree(t.id);

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">#ID ${t.id}</span>
                <span style="font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; background: var(--admin-accent-light); color: var(--admin-accent);">${t.estado}</span>
            </div>
            <div class="list-item-title">${t.especie}</div>
            <div class="list-item-subtitle">${t.calle}</div>
        `;
        container.appendChild(card);
    });
}
window.filterTrees = filterTrees;

// --- API Integrations ---
async function loadStatusesFromServer() {
    try {
        const response = await fetch('/api/request-statuses');
        if (response.ok) {
            const result = await response.json();
            requestStatuses = result.data;
        }
    } catch (err) {
        console.error("Error al cargar estados:", err);
    }
}

async function loadClaimsFromServer() {
    if (requestStatuses.length === 0) {
        await loadStatusesFromServer();
    }
    try {
        const response = await fetch('/requests', {
            headers: {
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            const result = await response.json();
            claims = result.data;
            updateStats();
            loadClaimsList();
            if (selectedClaimId) {
                selectClaim(selectedClaimId);
            }
        }
    } catch (err) {
        console.error("Error al cargar reclamos del servidor:", err);
    }
}
window.loadClaimsFromServer = loadClaimsFromServer;

async function setClaimStatus(newStatus) {
    const claim = claims.find(c => c.id === selectedClaimId);
    if (claim) {
        try {
            const response = await fetch(`/requests/update-status/${selectedClaimId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ estado: newStatus })
            });

            if (response.ok) {
                claim.estado = newStatus;
                selectClaim(selectedClaimId);
                updateStats();
                applyTemplate(newStatus);
                loadClaimsList();
            } else {
                alert('Error al actualizar el estado en el servidor.');
            }
        } catch (err) {
            console.error("Error al actualizar estado:", err);
        }
    }
}
window.setClaimStatus = setClaimStatus;

async function loadTreesFromServer() {
    try {
        const response = await fetch('/api/admin/arboles');
        if (response.ok) {
            const result = await response.json();
            trees.length = 0;
            result.data.forEach(t => trees.push(t));
            loadTreesList();
            if (selectedTreeId) {
                selectTree(selectedTreeId);
            }
        }
    } catch (err) {
        console.error("Error al cargar árboles:", err);
    }
}
window.loadTreesFromServer = loadTreesFromServer;

// --- Modal Registrar Nuevo Árbol ---
let adminMap = null;
let adminMarker = null;

window.openCreateTreeModal = function() {
    document.getElementById('create-tree-modal').classList.add('active');
    
    setTimeout(() => {
        if (!adminMap) {
            const mapCanvas = document.getElementById('admin-tree-map-canvas');
            if(mapCanvas) {
                adminMap = L.map('admin-tree-map-canvas', {
                    zoomControl: false
                }).setView([-34.5888, -58.4285], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(adminMap);

                L.control.zoom({ position: 'bottomright' }).addTo(adminMap);

                adminMap.on('click', (e) => {
                    setMarkerPosition(e.latlng.lat, e.latlng.lng);
                });
            }
        } else {
            adminMap.invalidateSize();
        }

        const latInput = document.getElementById('new-tree-lat');
        if (latInput && !latInput.value) {
            setMarkerPosition(-34.5888, -58.4285);
        }
    }, 200);
};

window.closeCreateTreeModal = function() {
    const modal = document.getElementById('create-tree-modal');
    if(modal) modal.classList.remove('active');
};

function setMarkerPosition(lat, lng) {
    const latInput = document.getElementById('new-tree-lat');
    const lngInput = document.getElementById('new-tree-lng');
    
    if(latInput) latInput.value = lat.toFixed(6);
    if(lngInput) lngInput.value = lng.toFixed(6);

    if (adminMarker) {
        adminMarker.setLatLng([lat, lng]);
    } else {
        if(adminMap) {
            adminMarker = L.marker([lat, lng], { draggable: true }).addTo(adminMap);
            adminMarker.on('dragend', () => {
                const position = adminMarker.getLatLng();
                if(latInput) latInput.value = position.lat.toFixed(6);
                if(lngInput) lngInput.value = position.lng.toFixed(6);
            });
        }
    }
    if(adminMap) adminMap.panTo([lat, lng]);
}
window.setMarkerPosition = setMarkerPosition;

window.submitCreateTree = async function(e) {
    e.preventDefault();
    const data = {
        especie: document.getElementById('new-tree-especie').value,
        latitude: parseFloat(document.getElementById('new-tree-lat').value),
        longitude: parseFloat(document.getElementById('new-tree-lng').value),
        calle_nombre: document.getElementById('new-tree-calle').value.trim(),
        calle_numero: parseInt(document.getElementById('new-tree-nro').value),
        estado: document.getElementById('new-tree-estado').value,
        altura: parseFloat(document.getElementById('new-tree-altura').value),
        circunferencia: document.getElementById('new-tree-circunferencia').value,
        edad: parseInt(document.getElementById('new-tree-edad').value)
    };

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
            alert('Árbol registrado con éxito en el sistema.');
            closeCreateTreeModal();
            document.getElementById('create-tree-form').reset();
            if (adminMarker) {
                adminMap.removeLayer(adminMarker);
                adminMarker = null;
            }
            loadTreesFromServer();
        } else {
            alert('Error al registrar el árbol.');
        }
    } catch (err) {
        console.error('Error saving tree:', err);
        alert('Error de conexión.');
    }
};

// --- Inicialización ---
document.addEventListener('DOMContentLoaded', () => {
    loadClaimsFromServer();
    loadTreesFromServer();
});
