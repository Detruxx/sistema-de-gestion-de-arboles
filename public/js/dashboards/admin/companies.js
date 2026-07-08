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

        const isVerified = c.user_status_id === 1;
        const badgeColor = isVerified ? '#22c55e' : '#eab308'; // Verde si Verificada, Amarillo si No Verificada
        const statusText = isVerified ? 'Verificada' : 'No Verificada';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Empresa #${c.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor}; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; border-radius: 8px;">${statusText}</span>
            </div>
            <div class="list-item-title">${c.name || 'Sin Nombre'}</div>
            <div class="list-item-subtitle" style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-top: 5px;">
                <span>${c.location || 'Sin ubicación'}</span>
                <span>${c.email || ''}</span>
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

    const isVerified = c.user_status_id === 1;
    const badgeColor = isVerified ? '#22c55e' : '#eab308';

    // Botón visual de estado de empresa (switch visual)
    const verificationButton = isVerified 
        ? `<button class="btn-primary" onclick="window.toggleCompanyVerification(${c.id})" style="background-color: #22c55e; border-color: #22c55e; color: white; display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            Verificada (Click para Deshabilitar)
           </button>`
        : `<button class="btn-primary" onclick="window.toggleCompanyVerification(${c.id})" style="background-color: #eab308; border-color: #eab308; color: white; display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            No Verificada (Click para Verificar)
           </button>`;

    // Listado de usuarios de la empresa
    let usersListHtml = '';
    if (c.users && c.users.length > 0) {
        usersListHtml = `
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--admin-border); text-align: left; color: var(--admin-text-secondary);">
                        <th style="padding: 8px;">Nombre</th>
                        <th style="padding: 8px;">Email</th>
                        <th style="padding: 8px;">Estado</th>
                        <th style="padding: 8px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${c.users.map(u => {
                        let uBadgeColor = '#22c55e'; // Habilitado
                        if (u.user_status_id === 2) uBadgeColor = '#9ca3af'; // Deshabilitado
                        if (u.user_status_id === 3) uBadgeColor = '#ea580c'; // Suspendido
                        if (u.user_status_id === 4) uBadgeColor = '#ef4444'; // Bloqueado
                        
                        return `
                        <tr style="border-bottom: 1px solid var(--admin-border);">
                            <td style="padding: 8px; font-weight: bold;">${u.name} ${u.last_name || ''}</td>
                            <td style="padding: 8px;">${u.email}</td>
                            <td style="padding: 8px;"><span class="badge-status" style="font-size: 0.75rem; padding: 2px 6px; background-color: ${uBadgeColor}20; color: ${uBadgeColor}; border: 1px solid ${uBadgeColor};">${u.status ? u.status.name : 'Desconocido'}</span></td>
                            <td style="padding: 8px; display:flex; gap:5px;">
                                <select onchange="window.updateCompanyUserStatus(${u.id}, this.value)" style="padding: 4px; border-radius: 4px; border: 1px solid var(--admin-border); font-size: 0.8rem;">
                                    <option value="">Cambiar Estado...</option>
                                    <option value="1">Habilitar</option>
                                    <option value="2">Deshabilitar</option>
                                    <option value="3">Suspender</option>
                                    <option value="4">Bloquear (Ban)</option>
                                </select>
                                <button onclick="window.removeCompanyUserRole(${u.id})" style="background: none; border: 1px solid #ef4444; color: #ef4444; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; cursor: pointer;">Quitar Rol</button>
                            </td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        `;
    } else {
        usersListHtml = `<p style="color: var(--admin-text-secondary); font-style: italic;">No hay usuarios asociados a esta empresa aún.</p>`;
    }

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${c.name || 'Sin Nombre'}</h3>
                <p class="detail-subtitle">Razón Social: <strong>${c.business_name || '-'}</strong> | ID: <strong style="color:var(--admin-text-primary);">${c.id}</strong></p>
            </div>
            <div style="display:flex; flex-direction:column; gap:5px; align-items:flex-end;">
                ${verificationButton}
            </div>
        </div>

        <div class="detail-section">
            <p class="detail-label">CUIT</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary); font-weight: bold;">${c.cuit || '-'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Representante / Email de contacto</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${c.email || '-'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Dirección Fiscal / Operativa</p>
            <p class="detail-value">${c.location || '-'}</p>
        </div>

        <div style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
            <h4 style="font-family: var(--font-display); color: var(--admin-accent); margin-bottom: 15px;">Usuarios y Accesos de la Empresa</h4>
            
            <!-- Formulario Inline -->
            <div style="background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <h5 style="margin-top: 0; margin-bottom: 10px; font-family: var(--font-base);">Crear Nuevo Usuario para la Empresa</h5>
                <form onsubmit="window.createCompanyUser(event, ${c.id})" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                    <div style="flex: 1; min-width: 120px;">
                        <label style="font-size: 0.8rem; font-weight: bold;">Nombre</label>
                        <input type="text" id="new-cuser-name" required style="width: 100%; padding: 6px; border: 1px solid var(--admin-border); border-radius: 4px;">
                    </div>
                    <div style="flex: 1; min-width: 120px;">
                        <label style="font-size: 0.8rem; font-weight: bold;">Apellido</label>
                        <input type="text" id="new-cuser-lastname" required style="width: 100%; padding: 6px; border: 1px solid var(--admin-border); border-radius: 4px;">
                    </div>
                    <div style="flex: 1.5; min-width: 180px;">
                        <label style="font-size: 0.8rem; font-weight: bold;">Email</label>
                        <input type="email" id="new-cuser-email" required style="width: 100%; padding: 6px; border: 1px solid var(--admin-border); border-radius: 4px;">
                    </div>
                    <div style="flex: 1; min-width: 120px;">
                        <label style="font-size: 0.8rem; font-weight: bold;">Contraseña</label>
                        <input type="text" id="new-cuser-pass" required style="width: 100%; padding: 6px; border: 1px solid var(--admin-border); border-radius: 4px;">
                    </div>
                    <button type="submit" class="btn-primary" style="padding: 6px 12px; height: 32px;">Agregar</button>
                </form>
            </div>

            ${usersListHtml}
        </div>

        <div style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
            <h4 style="font-family: var(--font-display); color: var(--admin-accent); margin-bottom: 15px;">Historial de Tareas Realizadas</h4>
            <div style="background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 15px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--admin-border); text-align: left; color: var(--admin-text-secondary);">
                            <th style="padding: 8px;">ID Tarea</th>
                            <th style="padding: 8px;">Descripción</th>
                            <th style="padding: 8px;">Fecha Prog.</th>
                            <th style="padding: 8px;">Estado</th>
                            <th style="padding: 8px;">Costo ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${(c.work_orders && c.work_orders.length > 0) ? c.work_orders.map(w => `
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 8px; font-weight: bold;">#${w.id}</td>
                                <td style="padding: 8px; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${w.task_description || 'Sin descripción'}</td>
                                <td style="padding: 8px;">${w.scheduled_date ? new Date(w.scheduled_date).toLocaleDateString() : '-'}</td>
                                <td style="padding: 8px;">
                                    <span style="font-size: 0.75rem; padding: 2px 6px; background-color: #3498db20; color: #3498db; border: 1px solid #3498db; border-radius: 4px;">
                                        ${w.work_status}
                                    </span>
                                </td>
                                <td style="padding: 8px;">$${parseFloat(w.cost || 0).toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            </tr>
                        `).join('') : `<tr><td colspan="5" style="padding: 15px; text-align: center; color: var(--admin-text-secondary);">No hay tareas registradas para esta empresa.</td></tr>`}
                    </tbody>
                </table>
            </div>
        </div>
    `;
};

window.toggleCompanyVerification = async function (id) {
    try {
        const response = await fetch('/api/admin/companies/' + id + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        if (!response.ok) throw new Error('Error al actualizar el estado de la empresa');

        const result = await response.json();
        
        const c = state.companies.find(item => item.id === id);
        if (c) {
            c.user_status_id = result.user_status_id;
        }

        showNotification(result.message || 'Empresa actualizada');
        loadCompaniesList();
        selectCompany(id);
        if (typeof window.updateAdminStats === 'function') window.updateAdminStats();
    } catch (err) {
        console.error("Error al actualizar estado de la empresa:", err);
        showNotification('Error al cambiar el estado de la empresa');
    }
};

window.createCompanyUser = async function (e, companyId) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerText = '...';

    const data = {
        name: document.getElementById('new-cuser-name').value,
        last_name: document.getElementById('new-cuser-lastname').value,
        email: document.getElementById('new-cuser-email').value,
        password: document.getElementById('new-cuser-pass').value
    };

    try {
        const response = await fetch('/api/admin/companies/' + companyId + '/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            const errData = await response.json();
            throw new Error(errData.message || 'Error al crear usuario');
        }

        const result = await response.json();
        
        const c = state.companies.find(item => item.id === companyId);
        if (c) {
            if (!c.users) c.users = [];
            c.users.push(result.data);
        }

        showNotification('Usuario creado y vinculado correctamente.');
        selectCompany(companyId); // Refresca el panel
    } catch (err) {
        console.error("Error al crear usuario:", err);
        showNotification(err.message || 'Error al crear el usuario');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Agregar';
    }
};

window.updateCompanyUserStatus = async function (userId, newStatusId) {
    if (!newStatusId) return; // Si seleccionó "Cambiar Estado..."
    
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

        showNotification('Estado del usuario actualizado.');
        
        // Recargar datos globalmente para mantener sincronía
        if (typeof window.fetchCompanies === 'function') {
            await window.fetchCompanies(); 
            selectCompany(state.selectedCompanyId); // Refresca panel
        }
    } catch (err) {
        console.error(err);
        showNotification('No se pudo cambiar el estado del usuario');
    }
};

window.removeCompanyUserRole = async function (userId) {
    if (!confirm('¿Estás seguro de quitarle el rol de empresa a este usuario? Pasará a ser un vecino común.')) return;
    
    try {
        const response = await fetch('/api/admin/users/' + userId + '/role', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ role: 'vecino' })
        });

        if (!response.ok) throw new Error('Error al remover rol');

        showNotification('Rol removido exitosamente.');
        
        // Recargar empresas para actualizar la lista de usuarios
        if (typeof window.fetchCompanies === 'function') {
            await window.fetchCompanies(); 
            selectCompany(state.selectedCompanyId); // Refresca panel
        }
    } catch (err) {
        console.error(err);
        showNotification('Error al remover el rol.');
    }
};

