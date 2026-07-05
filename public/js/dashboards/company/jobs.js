/**
 * Componente (Dashboard Empresa): Lógica para la gestión y actualización de estado de trabajos asignados.
 */

import { putJobStatus } from './api.js';
import { updateCompanyStats, showNotification } from './ui.js';
import { getCompanyJobs } from './main.js';

let selectedJobId = null;

// Exponer al scope global para el onClick de los botones HTML
window.selectCompanyJob = function(id) {
    selectedJobId = id;
    loadCompanyJobsList();
    renderJobDetail(id);
};

window.updateJobStatus = async function(id, newStatus) {
    const jobs = getCompanyJobs();
    const j = jobs.find(item => item.id === id);
    if (!j) return;

    try {
        await putJobStatus(id, newStatus);
        j.work_status = newStatus;
        if (newStatus === 'Finalizado') {
            showNotification(`Trabajo #${id} finalizado. Control devuelto al Inspector.`);
        } else {
            showNotification(`Trabajo #${id} actualizado a: ${newStatus}`);
        }
        window.selectCompanyJob(id);
        loadCompanyJobsList();
        updateCompanyStats();
    } catch (err) {
        console.error("Error al actualizar estado del trabajo:", err);
    }
};

export function loadCompanyJobsList() {
    const container = document.getElementById('company-jobs-list-container');
    if (!container) return;
    container.innerHTML = '';

    const jobs = getCompanyJobs();

    if (jobs.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No tienes trabajos asignados.</p></div>';
        return;
    }

    jobs.forEach(j => {
        const card = document.createElement('div');
        card.className = `list-item-card ${selectedJobId === j.id ? 'active' : ''}`;
        card.onclick = () => window.selectCompanyJob(j.id);

        let statusColor = '#3498db';
        if (j.work_status === 'En Proceso') statusColor = '#e67e22';
        else if (j.work_status === 'Finalizado') statusColor = '#22c55e';
        else if (j.work_status === 'En espera') statusColor = '#95a5a6';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Orden #${j.id}</span>
                <span class="badge-status" style="background-color: ${statusColor}20; color: ${statusColor}; border: 1px solid ${statusColor};">${j.work_status}</span>
            </div>
            <div class="list-item-title">${j.task_description}</div>
            <div class="list-item-subtitle">Programado: ${j.scheduled_date || 'Sin fecha asignada'}</div>
        `;
        container.appendChild(card);
    });
}

function renderJobDetail(id) {
    const jobs = getCompanyJobs();
    const j = jobs.find(item => item.id === id);
    const panel = document.getElementById('company-job-detail-panel');
    if (!j || !panel) return;

    let statusColor = '#3498db';
    if (j.work_status === 'En Proceso') statusColor = '#e67e22';
    else if (j.work_status === 'Finalizado') statusColor = '#22c55e';
    else if (j.work_status === 'En espera') statusColor = '#95a5a6';

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${j.task_description}</h3>
                <p class="detail-subtitle">Orden de Trabajo #${j.id} | Asignado el ${j.created_at || 'Recientemente'}</p>
            </div>
            <span class="badge-status" style="background-color: ${statusColor}20; color: ${statusColor}; border: 1px solid ${statusColor}; font-size: 1rem; padding: 6px 12px;">${j.work_status}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Ubicación del Reclamo Comunal</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${j.request ? j.request.direccion || 'Av. Cabildo 2800' : 'Av. Cabildo 2800'} (Jacarandá)</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Detalles del Reclamo</p>
            <p class="detail-value">${j.request ? j.request.descripcion || 'Poda preventiva de ramas con peligro de caída.' : 'Poda preventiva de ramas con peligro de caída.'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Orden de Ejecución Secuencial</p>
            <p class="detail-value">Intervención Nivel: <strong>${j.execution_order || 1}</strong></p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Actualizar Estado del Trabajo</h4>
            
            ${j.work_status === 'Finalizado' ? `
                <div style="color: #22c55e; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    <span>✔ Tarea Completada. El control ha sido devuelto al inspector de la comuna para continuar hablando con el vecino.</span>
                </div>
            ` : `
                <div style="display: flex; gap: 10px;">
                    ${j.work_status === 'Asignado' || j.work_status === 'En espera' ? `
                        <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'En Proceso')" style="background-color: #ea580c; border-color: #ea580c;">
                            Iniciar Tarea (En Proceso)
                        </button>
                    ` : ''}
                    
                    ${j.work_status === 'En Proceso' ? `
                        <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'Finalizado')" style="background-color: #22c55e; border-color: #22c55e;">
                            Marcar como Finalizado
                        </button>
                    ` : ''}
                </div>
            `}
        </div>
    `;
}
