/**
 * API (Dashboard Administrador): Funciones de conexión al servidor y llamadas AJAX para el panel de administración.
 */

import { getCsrfToken, showNotification } from '../shared/layout.js';
import { state } from '../inspector/state.js';
import * as api from './api.js';

export async function changeUserRole (id, role) {
    const user = state.users.find(u => u.id === id);
    if (!user) return;

    try {
        const response = await fetch(`/api/admin/users/${id}/role`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ role: role })
        });

        if (!response.ok) throw new Error('Error al actualizar rol');

        user.role = role;
        showNotification(`Usuario #${id} cambiado al rol: ${role}`);
        updateAdminStats();
        
        // Reset panels
        if (role === 'inspector') {
            state.selectedresidentId = null;
            loadresidentsList();
            document.getElementById('resident-detail-panel').innerHTML = `<div class="empty-state-panel"><p>Vecino promovido a Inspector.</p></div>`;
            loadInspectorsList();
        } else if (role === 'vecino') {
            state.selectedInspectorId = null;
            loadInspectorsList();
            document.getElementById('inspector-detail-panel').innerHTML = `<div class="empty-state-panel"><p>Rol removido del inspector.</p></div>`;
            loadresidentsList();
        }

    } catch (err) {
        console.error("Error al actualizar rol:", err);
    }
};

export async function banUserPrompt (id, days) {
    const user = state.users.find(u => u.id === id);
    if (!user) return;

    const date = new Date();
    date.setDate(date.getDate() + days);
    const bannedUntil = date.toISOString();

    try {
        const response = await fetch(`/api/admin/users/${id}/ban`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ banned_until: bannedUntil })
        });

        if (!response.ok) throw new Error('Error al suspender usuario');

        user.banned_until = bannedUntil;
        showNotification(`Vecino #${id} suspendido por ${days} días.`);
        selectresident(id);
    } catch (err) {
        console.error("Error al banear usuario:", err);
    }
};

export async function liftBan (id) {
    const user = state.users.find(u => u.id === id);
    if (!user) return;

    try {
        const response = await fetch(`/api/admin/users/${id}/ban`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ banned_until: null })
        });

        if (!response.ok) throw new Error('Error al levantar suspensión');

        user.banned_until = null;
        showNotification(`Baneo de vecino #${id} levantado.`);
        selectresident(id);
    } catch (err) {
        console.error("Error al levantar baneo:", err);
    }
};


export function updateAdminStats () {
    const residentsCount = state.users.filter(u => u.role === 'vecino').length;
    const inspectorsCount = state.users.filter(u => u.role === 'inspector').length;
    const companiesCount = state.companies.length;
    
    const pendingPostulations = state.companies.filter(c => c.status === 'Pendiente').length;

    const elN = document.getElementById('stat-total-residents');
    const elI = document.getElementById('stat-total-inspectors');
    const elC = document.getElementById('stat-total-companies');
    const elP = document.getElementById('stat-pending-postulations');

    if (elN) elN.innerText = residentsCount;
    if (elI) elI.innerText = inspectorsCount;
    if (elC) elC.innerText = companiesCount;
    if (elP) elP.innerText = pendingPostulations;
};
export async function loadAdminData (searchQuery = '') {
    try {
        const url = searchQuery ? `/api/admin/users?search=${encodeURIComponent(searchQuery)}` : '/api/admin/users';
        const userRes = await fetch(url);
        if (userRes.ok) {
            const data = await userRes.json();
            state.users = data.data || [];
        } else {
            state.users = [];
        }
    } catch (err) {
        console.error("Error al cargar usuarios:", err);
        state.users = [];
    }

    try {
        const compRes = await fetch('/api/admin/companies');
        if (compRes.ok) {
            const data = await compRes.json();
            state.companies = data.data;
        } else {
            state.companies = [];
        }
    } catch (err) {
        console.error("Error al cargar empresas:", err);
        state.companies = [];
    }

    loadresidentsList();
    loadInspectorsList();
    loadCompaniesList();
    updateAdminStats();
};

document.addEventListener('DOMContentLoaded', () => {
    if (state.currentUserRole === 'admin') {
        loadAdminData();
    }
});


