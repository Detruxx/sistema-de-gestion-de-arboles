/**
 * Componente (Dashboard Administrador): Lógica para la gestión de empresas (listado, aprobación, etc.).
 */

import { getCsrfToken, showNotification } from '../shared/layout.js';
import { state } from '../inspector/state.js';
import * as api from './api.js';

export function loadCompaniesList () {
    const container = document.getElementById('companies-list-container');
    if (!container) return;
    container.innerHTML = '';

    if (state.companies.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No se encontraron empresas asociadas</p></div>';
        return;
    }

    state.companies.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedCompanyId === c.id ? 'active' : ''}`;
        card.onclick = () => selectCompany(c.id);

        let badgeColor = '#22c55e'; // Activo
        if (c.status === 'De baja') badgeColor = '#ef4444';
        if (c.status === 'Pendiente') badgeColor = '#ea580c';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Empresa #${c.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor}; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; border-radius: 8px;">${c.status || 'Activo'}</span>
            </div>
            <div class="list-item-title">${c.company_name}</div>
            <div class="list-item-subtitle" style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-top: 5px;">
                <span>${c.address || 'Buenos Aires, CABA'}</span>
                <span>${c.contact_email || ''}</span>
            </div>
        `;
        container.appendChild(card);
    });
};

export function selectCompany (id) {
    state.selectedCompanyId = id;
    loadCompaniesList();

    const c = state.companies.find(item => item.id === id);
    const panel = document.getElementById('company-detail-panel');
    if (!c || !panel) return;

    let badgeColor = '#22c55e'; // Activo
    if (c.status === 'De baja') badgeColor = '#ef4444';
    if (c.status === 'Pendiente') badgeColor = '#ea580c';

    // Buttons
    let actionButtons = '';
    if (c.status === 'Pendiente') {
        actionButtons = `
            <button class="btn-primary" onclick="updateCompanyStatus(${c.id}, 'Activo')" style="background-color: #22c55e; border-color: #22c55e; color: white;">
                Aprobar y Dar de Alta
            </button>
            <button class="btn-secondary" onclick="updateCompanyStatus(${c.id}, 'De baja')" style="color: #ef4444; border-color: #ef4444;">
                Rechazar Postulación
            </button>
        `;
    } else if (c.status === 'Activo') {
        actionButtons = `
            <button class="btn-secondary" onclick="updateCompanyStatus(${c.id}, 'De baja')" style="color: #ef4444; border-color: #ef4444; font-weight: 600;">
                Dar de Baja Empresa
            </button>
        `;
    } else if (c.status === 'De baja') {
        actionButtons = `
            <button class="btn-primary" onclick="updateCompanyStatus(${c.id}, 'Activo')" style="background-color: #22c55e; border-color: #22c55e; color: white;">
                Dar de Alta Empresa
            </button>
        `;
    }

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${c.company_name}</h3>
                <p class="detail-subtitle">ID Empresa: <strong style="color:var(--admin-text-primary);">${c.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor}; font-weight: bold;">${c.status || 'Activo'}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">CUIT</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary); font-weight: bold;">${c.cuit || '30-99999999-1'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Representante / Email de contacto</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${c.contact_email || 'contacto@empresa.com.ar'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Dirección Fiscal / Operativa</p>
            <p class="detail-value">${c.address || 'Av. de Mayo 800, CABA'}</p>
        </div>

        ${c.services && c.services.length > 0 ? `
        <div class="detail-section">
            <p class="detail-label">Servicios Declarados</p>
            <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-top: 5px;">
                ${c.services.map(s => `<span style="background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500;">${s}</span>`).join('')}
            </div>
        </div>
        ` : ''}

        <div class="detail-box" style="margin-top: 25px; border-left: 4px solid var(--admin-accent); padding: 15px; background: rgba(45, 122, 79, 0.03);">
            <span style="font-size: 0.85rem; color: var(--admin-text-secondary); display: block; text-transform: uppercase;">Órdenes de Trabajo Asignadas</span>
            <strong style="font-size: 1.8rem; color: var(--admin-text-primary); font-family: var(--font-display);">${(c.work_orders || []).length}</strong>
        </div>

        <!-- Acciones de Control de Empresa -->
        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Acciones de Control de Empresa</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                ${actionButtons}
            </div>
        </div>

        <div style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
            <h4 style="font-family: var(--font-display); color: var(--admin-accent); margin-bottom: 15px;">Historial de Tareas Realizadas</h4>
            ${c.work_orders && c.work_orders.length > 0 ? `
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--admin-border); text-align: left; color: var(--admin-text-secondary);">
                        <th style="padding: 8px;">ID Orden</th>
                        <th style="padding: 8px;">Tarea</th>
                        <th style="padding: 8px;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    ${c.work_orders.map(w => `
                        <tr style="border-bottom: 1px solid var(--admin-border);">
                            <td style="padding: 8px; font-weight: bold;">#${w.id}</td>
                            <td style="padding: 8px;">${w.description || w.task_description}</td>
                            <td style="padding: 8px;"><span class="badge-status" style="font-size: 0.75rem; padding: 2px 6px; background-color: #22c55e20; color: #22c55e; border: 1px solid #22c55e;">${w.status || w.work_status}</span></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            ` : `<p style="color: var(--admin-text-secondary); font-style: italic;">No hay tareas registradas para esta empresa.</p>`}
        </div>
    `;
};

export async function updateCompanyStatus (id, newStatus) {
    try {
        const response = await fetch(`/api/admin/companies/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!response.ok) {
            throw new Error('Error al actualizar el estado de la empresa');
        }

        const c = state.companies.find(item => item.id === id);
        if (c) {
            c.status = newStatus;
        }

        showNotification(`Empresa #${id} cambiada al estado: ${newStatus}`);
        loadCompaniesList();
        selectCompany(id);
        updateAdminStats();
    } catch (err) {
        console.error("Error al actualizar estado de la empresa:", err);
        showNotification(`Error al cambiar el estado de la empresa #${id}`);
    }
};

