/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { getCsrfToken, showNotification } from '../shared/layout.js';
import { state } from '../inspector/state.js';
import * as api from './api.js';

export function loadInspectorsList () {
    const container = document.getElementById('inspectors-list-container');
    if (!container) return;
    container.innerHTML = '';

    const searchVal = document.getElementById('search-inspectors')?.value.toLowerCase() || '';
    const inspectors = state.users.filter(u => u.role === 'inspector' && (u.name.toLowerCase().includes(searchVal) || u.email.toLowerCase().includes(searchVal)));

    if (inspectors.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No se encontraron inspectores</p></div>';
        return;
    }

    inspectors.forEach(u => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedInspectorId === u.id ? 'active' : ''}`;
        card.onclick = () => selectInspector(u.id);

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Inspector #${u.id}</span>
                <span class="badge-status" style="background-color: #3498db20; color: #3498db; border: 1px solid #3498db;">Activo</span>
            </div>
            <div class="list-item-title">${u.name} ${u.last_name || ''}</div>
            <div class="list-item-subtitle">${u.email}</div>
        `;
        container.appendChild(card);
    });
};

export function selectInspector (id) {
    state.selectedInspectorId = id;
    loadInspectorsList();

    const u = state.users.find(item => item.id === id);
    const panel = document.getElementById('inspector-detail-panel');
    if (!u || !panel) return;

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${u.name} ${u.last_name || ''}</h3>
                <p class="detail-subtitle">ID Inspector: <strong style="color:var(--admin-text-primary);">${u.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: #3498db20; color: #3498db; border: 1px solid #3498db;">Activo</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Correo Comunal</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${u.email}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Jurisdicción / Rol</p>
            <p class="detail-value">Personal Técnico Autorizado (Inspector Comunal)</p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: #ef4444; font-family: var(--font-display);">Remover Inspector</h4>
            <p style="font-size: 0.9rem; color: var(--admin-text-secondary); margin-bottom: 15px;">Al remover el rol de inspector, este usuario volverá a ser un vecino común y perderá todos sus accesos administrativos.</p>
            <button class="btn-primary" onclick="changeUserRole(${u.id}, 'vecino')" style="background-color: #ef4444; border-color: #ef4444;">
                Remover Rol de Inspector
            </button>
        </div>
    `;
};

export function filterInspectors () {
    loadInspectorsList();
};

