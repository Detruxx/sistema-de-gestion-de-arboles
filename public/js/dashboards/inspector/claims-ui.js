/**
 * UI (Dashboard Inspector): Funciones para la manipulación del DOM y visualización de la interfaz.
 */

import { state } from './state.js';
import { getClaimListCardHtml, getClaimModalHtml } from './claims-template.js';
import { updateStats } from './ui.js';

export function loadClaimsList() {
    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';

    state.claims.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => window.selectClaim(c.id);

        const statusObj = state.requestStatuses.find(rs => rs.slug === c.estado);
        card.innerHTML = getClaimListCardHtml(c, state.selectedClaimId === c.id, statusObj);
        container.appendChild(card);
    });
}

export function selectClaim(id) {
    state.selectedClaimId = id;
    loadClaimsList();

    const claim = state.claims.find(c => c.id === id);
    const modal = document.getElementById('claim-detail-modal');
    const panel = document.getElementById('claim-modal-body-content');

    if (!claim || !panel || !modal) return;

    panel.innerHTML = getClaimModalHtml(claim, state);
    modal.style.display = 'flex';
}

export function filterClaims() {
    const query = document.getElementById('search-claims').value.toLowerCase();
    const statusFilter = document.getElementById('filter-claim-status') ? document.getElementById('filter-claim-status').value : '';
    const categoryFilter = document.getElementById('filter-claim-category') ? document.getElementById('filter-claim-category').value : '';

    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';

    const filtered = state.claims.filter(c => {
        const matchesQuery = c.vecino.toLowerCase().includes(query) ||
            c.direccion.toLowerCase().includes(query) ||
            c.id.toLowerCase().includes(query);
        const matchesStatus = !statusFilter || c.estado === statusFilter;
        const matchesCategory = !categoryFilter || c.categoria === categoryFilter;

        return matchesQuery && matchesStatus && matchesCategory;
    });

    filtered.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => window.selectClaim(c.id);

        const statusObj = state.requestStatuses.find(rs => rs.slug === c.estado);
        card.innerHTML = getClaimListCardHtml(c, state.selectedClaimId === c.id, statusObj);
        container.appendChild(card);
    });
}

export function closeClaimDetailModal() {
    const modal = document.getElementById('claim-detail-modal');
    if(modal) {
        modal.style.display = 'none';
    }
}
