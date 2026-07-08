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

        const isBanned = u.user_status_id === 3 || u.user_status_id === 4;
        let badgeColor = '#22c55e'; // Verde para activo
        let badgeLabel = 'Activo';
        
        if (u.user_status_id === 3) {
            badgeColor = '#ea580c';
            badgeLabel = 'Suspendido';
        } else if (u.user_status_id === 4) {
            badgeColor = '#ef4444';
            badgeLabel = 'Bloqueado';
        }

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

    let banText = 'Activo';
    let badgeColor = '#22c55e';
    
    if (u.user_status_id === 3) {
        banText = 'Suspendido';
        badgeColor = '#ea580c';
    } else if (u.user_status_id === 4) {
        banText = 'Bloqueado Permanente';
        badgeColor = '#ef4444';
    }

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${u.name} ${u.last_name || ''}</h3>
                <p class="detail-subtitle">ID Vecino: <strong style="color:var(--admin-text-primary);">${u.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor};">${banText}</span>
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
                ${u.user_status_id !== 3 ? `
                    <button class="btn-secondary" onclick="window.updateResidentStatus(${u.id}, 3)" style="color: #ea580c; border-color: #ea580c;">
                        Suspender Temporalmente
                    </button>
                ` : ''}
                ${u.user_status_id !== 4 ? `
                    <button class="btn-primary" onclick="window.updateResidentStatus(${u.id}, 4)" style="background-color: #ef4444; border-color: #ef4444;">
                        Bloquear (Ban Permanente)
                    </button>
                ` : ''}
                ${(u.user_status_id === 3 || u.user_status_id === 4) ? `
                    <button class="btn-primary" onclick="window.updateResidentStatus(${u.id}, 1)" style="background-color: #22c55e; border-color: #22c55e;">
                        Levantar Suspensión/Bloqueo
                    </button>
                ` : ''}
            </div>
        </div>
    `;
};

export function filterresidents () {
    loadresidentsList();
};

window.updateResidentStatus = async function (userId, newStatusId) {
    if (!confirm('¿Estás seguro de que deseas cambiar el estado de este vecino?')) return;
    
    try {
        const response = await fetch('/api/admin/users/' + userId + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ user_status_id: newStatusId })
        });

        if (!response.ok) throw new Error('Error al actualizar estado');

        showNotification('Estado del vecino actualizado correctamente.');
        
        // Refrescar lista principal
        if (typeof window.fetchUsers === 'function') {
            await window.fetchUsers();
            selectresident(state.selectedresidentId);
        }
    } catch (err) {
        console.error(err);
        showNotification('No se pudo actualizar el estado del vecino.');
    }
};

