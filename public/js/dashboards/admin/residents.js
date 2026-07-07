/**
 * Componente (Dashboard Administrador): Lógica para la gestión de vecinos y usuarios regulares.
 */

import { getCsrfToken, showNotification } from '../shared/layout.js';
import { state } from '../inspector/state.js';
import * as api from './api.js';

export function loadresidentsList () {
    const container = document.getElementById('residents-list-container');
    if (!container) return;
    container.innerHTML = '';

    const searchVal = document.getElementById('search-residents')?.value.toLowerCase() || '';
    const residents = state.users.filter(u => u.role === 'vecino' && (u.name.toLowerCase().includes(searchVal) || u.email.toLowerCase().includes(searchVal)));

    if (residents.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No se encontraron vecinos</p></div>';
        return;
    }

    residents.forEach(u => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedresidentId === u.id ? 'active' : ''}`;
        card.onclick = () => selectresident(u.id);

        const isBanned = u.banned_until && new Date(u.banned_until) > new Date();
        const badgeColor = isBanned ? '#ef4444' : '#22c55e';
        const badgeLabel = isBanned ? 'Suspendido' : 'Activo';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">#${u.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor};">${badgeLabel}</span>
            </div>
            <div class="list-item-title">${u.name} ${u.last_name || ''}</div>
            <div class="list-item-subtitle">${u.email}</div>
        `;
        container.appendChild(card);
    });
};

export function selectresident (id) {
    state.selectedresidentId = id;
    loadresidentsList();

    const u = state.users.find(item => item.id === id);
    const panel = document.getElementById('resident-detail-panel');
    if (!u || !panel) return;

    const isBanned = u.banned_until && new Date(u.banned_until) > new Date();
    const banText = isBanned ? `Suspendido hasta: ${new Date(u.banned_until).toLocaleDateString()}` : 'Activo';

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${u.name} ${u.last_name || ''}</h3>
                <p class="detail-subtitle">ID Vecino: <strong style="color:var(--admin-text-primary);">${u.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: ${isBanned ? '#ef4444' : '#22c55e'}20; color: ${isBanned ? '#ef4444' : '#22c55e'}; border: 1px solid ${isBanned ? '#ef4444' : '#22c55e'};">${banText}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Correo Electrónico</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${u.email}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Domicilio Declarado</p>
            <p class="detail-value">${u.address || 'Av. Cabildo 2200, CABA'}</p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Acciones de Control de Vecino</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <button class="btn-primary" onclick="changeUserRole(${u.id}, 'inspector')" style="background-color: #3498db; border-color: #3498db;">
                    Promover a Inspector
                </button>
                <button class="btn-secondary" onclick="banUserPrompt(${u.id}, 7)" style="color: #ea580c; border-color: #ea580c;">
                    Suspender 7 Días
                </button>
                <button class="btn-secondary" onclick="banUserPrompt(${u.id}, 30)" style="color: #e11d48; border-color: #e11d48;">
                    Suspender 30 Días
                </button>
                ${isBanned ? `
                    <button class="btn-primary" onclick="liftBan(${u.id})" style="background-color: #22c55e; border-color: #22c55e;">
                        Levantar Suspensión
                    </button>
                ` : `
                    <button class="btn-primary" onclick="banUserPrompt(${u.id}, 365)" style="background-color: #ef4444; border-color: #ef4444;">
                        Baneo Permanente
                    </button>
                `}
            </div>
        </div>
    `;
};

export async function filterresidents () {
    const searchVal = document.getElementById('search-residents')?.value || '';
    await api.loadAdminData(searchVal);
    loadresidentsList();
};

