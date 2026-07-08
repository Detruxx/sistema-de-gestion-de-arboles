/**
 * API (Dashboard Administrador): Funciones de conexión al servidor y llamadas AJAX para el panel de administración.
 */

import { getCsrfToken, showNotification } from '../shared/layout.js';
import { state } from '../inspector/state.js';
import * as api from './api.js';

export async function changeUserRole(id, role) {
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

export async function banUserPrompt(id, days) {
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

export async function liftBan(id) {
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


export async function updateAdminStats() {
    try {
        const response = await fetch('/api/admin/stats');
        if (response.ok) {
            const stats = await response.json();
            const elN = document.getElementById('stat-total-residents');
            const elI = document.getElementById('stat-total-inspectors');
            const elC = document.getElementById('stat-total-companies');
            const elP = document.getElementById('stat-pending-postulations');

            if (elN) elN.innerText = stats.residents || 0;
            if (elI) elI.innerText = stats.inspectors || 0;
            if (elC) elC.innerText = stats.companies || 0;
            if (elP) elP.innerText = stats.pendingCompanies || 0;
        }
    } catch (err) {
        console.error("Error fetching stats:", err);
    }
}

export async function fetchResidents() {
    try {
        const response = await fetch('/api/admin/users?role=vecino');
        if (response.ok) {
            const data = await response.json();
            // lo almacena localmente asi las acciones lo encuentran
            state.users = state.users.filter(u => u.role !== 'vecino').concat(data.data);
            if (typeof window.loadresidentsList === 'function') window.loadresidentsList();
        }
    } catch (err) {
        console.error("Error al cargar vecinos:", err);
    }
}

export async function fetchInspectors() {
    try {
        const response = await fetch('/api/admin/users?role=inspector');
        if (response.ok) {
            const data = await response.json();
            state.users = state.users.filter(u => u.role !== 'inspector').concat(data.data);
            if (typeof window.loadInspectorsList === 'function') window.loadInspectorsList();
        }
    } catch (err) {
        console.error("Error al cargar inspectores:", err);
    }
}

export async function fetchCompanies() {
    try {
        const response = await fetch('/api/admin/companies');
        if (response.ok) {
            const data = await response.json();
            state.companies = data.data;
            if (typeof window.loadCompaniesList === 'function') window.loadCompaniesList();
        }
    } catch (err) {
        console.error("Error al cargar empresas:", err);
    }
}

export function showAdminModule(moduleName) {
    // LLamar al showModule base que cambia el CSS y la vista
    if (typeof window.showModule === 'function') window.showModule(moduleName);

    // Cargar los datos específicos de la solapa on-demand
    if (moduleName === 'resumen') updateAdminStats();
    if (moduleName === 'vecinos') fetchResidents();
    if (moduleName === 'inspectores') fetchInspectors();
    if (moduleName === 'empresas') fetchCompanies();
}

export async function loadDataFromServer() {
    // Al cargar la pagina inicialmente
    updateAdminStats();
    fetchResidents(); // Por si estamos en una solapa por defecto
}
