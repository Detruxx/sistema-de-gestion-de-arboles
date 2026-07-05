/**
 * Componente (Dashboard Inspector): Lógica para la inspección, registro y seguimiento de árboles.
 */

import { state } from './state.js';
export function loadTreesList() {
    const container = document.getElementById('trees-list-container');
    if(!container) return;
    container.innerHTML = '';

    state.trees.forEach(t => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedTreeId === t.id ? 'active' : ''}`;
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
};

export function selectTree(id) {
    state.selectedTreeId = id;
    loadTreesList();

    const tree = state.trees.find(t => t.id === id);
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
};

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

    const filtered = state.trees.filter(t => {
        const matchesId = idQuery === '' || t.id.toString().includes(idQuery);
        const matchesSpecies = speciesFilter === '' || t.especie === speciesFilter;
        const matchesState = stateFilter === '' || t.estado.includes(stateFilter);
        return matchesId && matchesSpecies && matchesState;
    });

    filtered.forEach(t => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedTreeId === t.id ? 'active' : ''}`;
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
};

export async function loadTreesFromServer() {
    try {
        const response = await fetch('/api/admin/arboles');
        if (response.ok) {
            const result = await response.json();
            state.trees.length = 0;
            result.data.forEach(t => state.trees.push(t));
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
