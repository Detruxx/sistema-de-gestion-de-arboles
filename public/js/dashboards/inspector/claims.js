/**
 * Componente (Dashboard Inspector): Controlador principal de reclamos.
 * Orquesta la UI, el Mapa, las Acciones y la inicialización.
 */

import { state } from './state.js';
import { fetchClaims, fetchRequestStatuses, fetchActiveCompanies } from './api.js';
import { updateStats } from './ui.js';

// Importar submódulos
import * as ui from './claims-ui.js';
import * as actions from './claims-actions.js';
import * as claimsMap from './claims-map.js';

// Exponer todo globalmente para que funcionen los onclick del HTML
Object.assign(window, ui);
Object.assign(window, actions);
Object.assign(window, claimsMap);

// Exportar todo para main.js
export * from './claims-ui.js';
export * from './claims-actions.js';
export * from './claims-map.js';

// --- Funciones de Inicialización ---

export async function loadStatusesFromServer() {
    try {
        const result = await fetchRequestStatuses();
        state.requestStatuses = result.data || result;
    } catch (err) {
        console.error("Error al cargar estados:", err);
    }
}

export async function loadActiveCompanies() {
    try {
        const data = await fetchActiveCompanies();
        state.activeCompanies = Array.isArray(data) ? data : (data.data || []);
    } catch (err) {
        console.error("Error al cargar empresas activas:", err);
    }
}

export async function loadClaimsFromServer() {
    await loadActiveCompanies();
    if (state.requestStatuses.length === 0) {
        await loadStatusesFromServer();
    }

    // Poblar selectores de filtros dinámicamente
    const statusSelect = document.getElementById('filter-claim-status');
    if (statusSelect && statusSelect.options.length <= 1) {
        state.requestStatuses.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.slug;
            opt.textContent = s.status_name;
            statusSelect.appendChild(opt);
        });
    }

    try {
        const result = await fetchClaims();
        state.claims = (result.data || result).sort((a, b) => {
            const getPriorityWeight = (p) => {
                if (!p) return 0;
                const lower = String(p).toLowerCase();
                if (lower === 'auto-alta' || lower === 'urgent' || lower === 'urgente') return 2;
                if (lower === 'auto-media' || lower === 'high' || lower === 'alta') return 1;
                return 0;
            };
            const wA = getPriorityWeight(a.priority);
            const wB = getPriorityWeight(b.priority);
            if (wA !== wB) {
                return wB - wA;
            }
            return a.raw_request_id - b.raw_request_id;
        });

        updateStats();
        ui.loadClaimsList();
        claimsMap.initClaimsMap();
        claimsMap.updateClaimsMapMarkers();

        // Poblar categorías dinámicamente
        const catSelect = document.getElementById('filter-claim-category');
        if (catSelect && catSelect.options.length <= 1) {
            const uniqueCategories = [...new Set(state.claims.map(c => c.categoria))];
            uniqueCategories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                catSelect.appendChild(opt);
            });
        }

        if (state.selectedClaimId) {
            ui.selectClaim(state.selectedClaimId);
        }
    } catch (err) {
        console.error("Error al cargar reclamos del servidor:", err);
    }
}

// Exponer también las funciones de carga
window.loadStatusesFromServer = loadStatusesFromServer;
window.loadActiveCompanies = loadActiveCompanies;
window.loadClaimsFromServer = loadClaimsFromServer;
