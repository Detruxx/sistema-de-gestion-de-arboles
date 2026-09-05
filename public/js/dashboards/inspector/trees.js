/**
 * Componente (Dashboard Inspector): Lógica para la inspección, registro y seguimiento de árboles.
 */

import { state } from './state.js';

// Genera el HTML de una tarjeta de arbol para la lista del inventario
function buildTreeCard(tree) {
    const card = document.createElement('div');
    card.className = `list-item-card ${state.selectedTreeId === tree.id ? 'active' : ''}`;
    card.onclick = () => selectTree(tree.id);

    let stateColor = '#94a3b8';
    const estadoLower = tree.estado.toLowerCase();
    if (estadoLower.includes('saludable')) {
        stateColor = '#10b981';
    } else if (estadoLower.includes('enfermo')) {
        stateColor = '#ea580c';
    } else if (estadoLower.includes('dañado') || estadoLower.includes('muerto') || estadoLower.includes('urgente')) {
        stateColor = '#ef4444';
    }
    card.style.setProperty('border-left', `5px solid ${stateColor}`, 'important');

    // Subtitulo con calle y referencia (si existe)
    const subtitleParts = [tree.calle];
    if (tree.referencia) {
        subtitleParts.push(tree.referencia);
    }

    card.innerHTML = `
        <div class="list-item-header">
            <span class="list-item-id">#ID ${tree.id}</span>
            <span style="font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; background: var(--admin-accent-light); color: var(--admin-accent);">${tree.estado}</span>
        </div>
        <div class="list-item-title">${tree.especie}</div>
        <div class="list-item-subtitle">${subtitleParts.join(' - ')}</div>
    `;
    return card;
}

// Carga la lista de arboles en el panel izquierdo del inventario
export function loadTreesList() {
    const container = document.getElementById('trees-list-container');
    if(!container) return;
    container.innerHTML = '';

    state.trees.forEach(tree => {
        container.appendChild(buildTreeCard(tree));
    });
};

// Selecciona un arbol y muestra la ficha tecnica completa en el panel derecho
export async function selectTree(id) {
    state.selectedTreeId = id;
    loadTreesList();

    const panel = document.getElementById('tree-detail-panel');
    if (!panel) return;

    panel.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--admin-text-secondary);">Cargando detalles técnicos del ejemplar...</div>';

    try {
        const response = await fetch(`/api/arboles/${id}`);
        if (!response.ok) throw new Error('Error al cargar detalles');
        const result = await response.json();
        const tree = result.data;
        
        // Procesamiento de vitalidad (puede ser array, objeto o string)
        let estadoStr = 'Saludable';
        if (Array.isArray(tree.vitality)) {
            estadoStr = tree.vitality.join(', ');
        } else if (typeof tree.vitality === 'object' && tree.vitality !== null) {
            estadoStr = Object.values(tree.vitality).join(', ');
        } else if (tree.vitality) {
            estadoStr = String(tree.vitality);
        }

        let especieStr = tree.specie ? tree.specie.common_name : 'Desconocida';
        let calleStr = tree.street ? `${tree.street.street_name} ${tree.street.street_number}` : 'Sin ubicación';
        let parqueStr = tree.park ? tree.park.park_name : null;
        
        // Color del indicador segun el estado de salud
        let colorEstado = '#2ecc71';
        if (estadoStr.includes('Muerto') || estadoStr.includes('Urgente') || estadoStr.includes('Seco')) colorEstado = '#e74c3c';
        else if (!estadoStr.includes('Saludable') && !estadoStr.includes('Completo')) colorEstado = '#f39c12';

        panel.innerHTML = `
            <div class="detail-header-panel">
                <h3 class="detail-title">Ficha Técnica del Ejemplar</h3>
                <p class="detail-subtitle">ID Árbol: <strong style="color:var(--admin-text-primary);">${tree.id}</strong></p>
            </div>

            <div class="tree-detail-grid">
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Especie</div>
                    <div class="tree-detail-field-value">${especieStr}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Ubicación / Calle</div>
                    <div class="tree-detail-field-value">${calleStr}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Referencia</div>
                    <div class="tree-detail-field-value">${tree.reference || 'N/D'}</div>
                </div>
                ${parqueStr ? `
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Parque / Espacio Verde</div>
                    <div class="tree-detail-field-value">${parqueStr}</div>
                </div>` : ''}
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Estado de Salud</div>
                    <div class="tree-detail-field-value" style="color: ${colorEstado}; font-weight: bold;">${estadoStr}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Altura</div>
                    <div class="tree-detail-field-value">${tree.height ? tree.height + ' m' : 'N/D'}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Edad Estimada</div>
                    <div class="tree-detail-field-value">${tree.years ? tree.years + ' años' : 'N/D'}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">DAP / Circunferencia</div>
                    <div class="tree-detail-field-value">${tree.dap ? tree.dap + ' cm' : 'N/D'}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Estructura</div>
                    <div class="tree-detail-field-value">${tree.structure || 'N/D'}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Grado de Inclinación</div>
                    <div class="tree-detail-field-value">${tree.degree !== null && tree.degree !== undefined ? tree.degree + '°' : 'N/D'}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Estado de Mantenimiento</div>
                    <div class="tree-detail-field-value">${tree.maintenance_status || 'N/D'}</div>
                </div>
                <div class="tree-detail-field tree-detail-field-full">
                    <div class="tree-detail-field-label">Observaciones</div>
                    <div class="tree-detail-field-value">${tree.observations || 'Sin observaciones'}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Latitud</div>
                    <div class="tree-detail-field-value">${tree.latitude || 'N/D'}</div>
                </div>
                <div class="tree-detail-field">
                    <div class="tree-detail-field-label">Longitud</div>
                    <div class="tree-detail-field-value">${tree.longitude || 'N/D'}</div>
                </div>
            </div>

            <div class="mt-25" style="display: flex; justify-content: flex-end; gap: 10px;">
                <button onclick="window.openEditTreeModal(${tree.id})" class="btn-primary" style="background-color: var(--admin-accent); padding: 8px 15px; border-radius: 8px; color: white; display:flex; align-items:center; gap:5px; border:none; cursor:pointer;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Editar Datos
                </button>
                <a href="/mapa?id=${tree.id}" target="_blank" class="btn-primary sidebar-btn-link btn-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                    </svg>
                    Localizar en el Mapa
                </a>
            </div>
        `;
    } catch (err) {
        console.error(err);
        panel.innerHTML = '<div style="padding: 20px; color: red;">Ocurrió un error al cargar los detalles del árbol.</div>';
    }
};

// Filtra la lista de arboles segun los criterios de busqueda del usuario
export function filterTrees() {
    const filterIdInput = document.getElementById('filter-tree-id');
    const filterSpeciesSelect = document.getElementById('filter-tree-species');
    const filterStateSelect = document.getElementById('filter-tree-state');
    
    const idQuery = filterIdInput ? filterIdInput.value.trim() : '';
    const speciesFilter = filterSpeciesSelect ? filterSpeciesSelect.value : '';
    const stateFilter = filterStateSelect ? filterStateSelect.value : '';
    
    const container = document.getElementById('trees-list-container');
    if(!container) return;
    container.innerHTML = '';

    const filtered = state.trees.filter(tree => {
        const matchesId = idQuery === '' || tree.id.toString().includes(idQuery);
        const matchesSpecies = speciesFilter === '' || tree.especie === speciesFilter;
        const matchesState = stateFilter === '' || tree.estado.includes(stateFilter);
        return matchesId && matchesSpecies && matchesState;
    });

    filtered.forEach(tree => {
        container.appendChild(buildTreeCard(tree));
    });
};

// Carga los arboles del servidor y arma el listado del inventario
export async function loadTreesFromServer() {
    try {
        // Obtenemos los pines livianos para la lista (evita saturar memoria)
        const response = await fetch('/api/arboles/pines');
        if (response.ok) {
            const result = await response.json();
            state.trees.length = 0;
            result.data.forEach(t => {
                state.trees.push({
                    id: t.id,
                    especie: t.specie ? t.specie.common_name : 'Desconocida',
                    calle: t.street ? `${t.street.street_name} ${t.street.street_number}` : 'Sin calle',
                    referencia: t.reference || null,
                    estado: 'Ver ficha' // Los pines no traen estado, se carga al hacer click
                });
            });
        }
    } catch (err) {
        console.error("Error al cargar árboles del servidor:", err);
    }

    // Ordenar los árboles por ID de forma numérica ascendente (mejora de Nacho)
    state.trees.sort((a, b) => parseInt(a.id) - parseInt(b.id));

    loadTreesList();
    
    const totalTreesEl = document.getElementById('stat-total-trees');
    if (totalTreesEl) {
        totalTreesEl.innerText = state.trees.length.toLocaleString('es-AR');
    }

    if (state.selectedTreeId) {
        selectTree(state.selectedTreeId);
    }
};

// Funcion para registrar un nuevo arbol desde el modal de creacion del inspector.
// Recolecta los datos del formulario, los envia al backend via POST y refresca la lista.
window.submitCreateTree = async function (e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    if(btn) {
        btn.disabled = true;
        btn.innerText = 'Registrando...';
    }

    // Recolectar datos
    const data = {
        latitude: document.getElementById('new-tree-lat').value,
        longitude: document.getElementById('new-tree-lng').value,
        address: document.getElementById('new-tree-address').value,
        specie: document.getElementById('new-tree-especie').value,
        vitality: [document.getElementById('new-tree-estado').value], // Lo enviamos como array porque el backend espera array
        height: document.getElementById('new-tree-altura').value,
        dap: document.getElementById('new-tree-circunferencia').value,
        years: document.getElementById('new-tree-edad').value
    };

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch('/api/arboles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            const err = await response.json();
            throw new Error(err.message || 'Error al crear el árbol');
        }

        alert('Árbol registrado exitosamente.');
        
        // Cerrar modal
        const modal = document.getElementById('create-tree-modal');
        if(modal) modal.classList.remove('active');
        e.target.reset();
        
        // Refrescar lista de arboles
        loadTreesFromServer();

    } catch (error) {
        console.error("Error al guardar árbol:", error);
        alert(error.message || 'Ocurrió un error de conexión');
    } finally {
        if(btn) {
            btn.disabled = false;
            btn.innerText = 'Registrar Ejemplar';
        }
    }
};

// Abre el modal de edicion de datos del arbol.
// Hace fetch del arbol completo y precarga todos los campos del formulario.
window.openEditTreeModal = async function(id) {
    document.getElementById('edit-tree-id').value = id;

    try {
        const response = await fetch(`/api/arboles/${id}`);
        if (!response.ok) throw new Error('Error al obtener datos del árbol');
        const result = await response.json();
        const tree = result.data;

        // Precargar especie
        const especieSelect = document.getElementById('edit-tree-especie');
        if (especieSelect && tree.specie) {
            especieSelect.value = tree.specie.common_name;
        }

        // Precargar referencia
        const referenciaInput = document.getElementById('edit-tree-referencia');
        if (referenciaInput) referenciaInput.value = tree.reference || '';

        // Precargar altura, DAP, edad
        const alturaInput = document.getElementById('edit-tree-altura');
        if (alturaInput) alturaInput.value = tree.height || '';

        const dapInput = document.getElementById('edit-tree-dap');
        if (dapInput) dapInput.value = tree.dap || '';

        const edadInput = document.getElementById('edit-tree-edad');
        if (edadInput) edadInput.value = tree.years || '';

        // Precargar estructura y grado
        const estructuraSelect = document.getElementById('edit-tree-estructura');
        if (estructuraSelect) estructuraSelect.value = tree.structure || '';

        const gradoInput = document.getElementById('edit-tree-grado');
        if (gradoInput) gradoInput.value = tree.degree || '';

        // Precargar mantenimiento
        const mantenimientoSelect = document.getElementById('edit-tree-mantenimiento');
        if (mantenimientoSelect) mantenimientoSelect.value = tree.maintenance_status || '';

        // Precargar vitalidad (follaje y plagas)
        const follajeSelect = document.getElementById('edit-tree-follaje');
        const plagasSelect = document.getElementById('edit-tree-plagas');

        if (typeof tree.vitality === 'object' && tree.vitality !== null && !Array.isArray(tree.vitality)) {
            if (follajeSelect) follajeSelect.value = tree.vitality.follaje || 'Completo';
            if (plagasSelect) plagasSelect.value = tree.vitality.plagas || 'Ninguna';
        } else {
            if (follajeSelect) follajeSelect.value = 'Completo';
            if (plagasSelect) plagasSelect.value = 'Ninguna';
        }

        // Precargar observaciones
        const observacionesTextarea = document.getElementById('edit-tree-observaciones');
        if (observacionesTextarea) observacionesTextarea.value = tree.observations || '';

    } catch (error) {
        console.error('Error al precargar datos del árbol:', error);
    }

    document.getElementById('edit-tree-modal').style.display = 'flex';
};

// Cierra el modal de edicion de datos del arbol
window.closeEditTreeModal = function() {
    document.getElementById('edit-tree-modal').style.display = 'none';
};

// Envia los cambios de todos los datos editables del arbol al backend via PUT.
// Actualiza los datos y refresca la interfaz.
window.submitEditTree = async function(event) {
    event.preventDefault();

    const id = document.getElementById('edit-tree-id').value;
    const follaje = document.getElementById('edit-tree-follaje').value;
    const plagas = document.getElementById('edit-tree-plagas').value;

    // Recolectar todos los campos editables del formulario
    const data = {
        specie: document.getElementById('edit-tree-especie').value,
        reference: document.getElementById('edit-tree-referencia').value || null,
        height: document.getElementById('edit-tree-altura').value,
        dap: document.getElementById('edit-tree-dap').value,
        years: document.getElementById('edit-tree-edad').value || null,
        structure: document.getElementById('edit-tree-estructura').value || null,
        degree: document.getElementById('edit-tree-grado').value || null,
        maintenance_status: document.getElementById('edit-tree-mantenimiento').value || null,
        vitality: { follaje: follaje, plagas: plagas },
        observations: document.getElementById('edit-tree-observaciones').value || null
    };

    try {
        const response = await fetch(`/api/arboles/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            closeEditTreeModal();
            
            // Mostrar notificacion de exito
            const banner = document.getElementById('notification-banner');
            const text = document.getElementById('notification-text');
            if (banner && text) {
                text.innerText = 'Datos del árbol actualizados con éxito';
                banner.style.display = 'flex';
                setTimeout(() => { banner.style.display = 'none'; }, 3000);
            }

            // Recargar detalles y lista
            selectTree(id);
            loadTreesFromServer();
        } else {
            const errorData = await response.json();
            alert(errorData.message || 'Error al actualizar los datos del árbol');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión con el servidor');
    }
};
