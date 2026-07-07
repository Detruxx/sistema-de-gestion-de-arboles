/**
 * Componente (Dashboard Empresa): Lógica para la gestión y actualización de estado de trabajos asignados.
 */

import { putJobStatus } from './api.js';
import { updateCompanyStats, showNotification } from './ui.js';
import { getCompanyJobs } from './main.js';

const companyRequestStatuses = [
    { status_name: 'Pendiente', slug: 'open', sequence: 1, color: '#eab308' },
    { status_name: 'Relevado', slug: 'relevated', sequence: 2, color: '#ea580c' },
    { status_name: 'Programado', slug: 'scheduled', sequence: 3, color: '#6b21a8' },
    { status_name: 'En curso', slug: 'in_progress', sequence: 4, color: '#2563eb' },
    { status_name: 'Completado', slug: 'resolved', sequence: 5, color: '#22c55e' },
    { status_name: 'Certificado', slug: 'certified', sequence: 6, color: '#15803d' }
];

let selectedJobId = null;

// Exponer al scope global para el onClick de los botones HTML
window.selectCompanyJob = function (id) {
    selectedJobId = id;
    loadCompanyJobsList();
    renderJobDetail(id);
};

window.updateJobStatus = async function (id, newStatus) {
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
        <div class="claim-modal-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px;">
            <!-- COLUMNA IZQUIERDA: DETALLES -->
            <div class="claim-modal-col-left" style="display: flex; flex-direction: column; gap: 15px; border-right: 1px solid var(--admin-border, #e5e7eb); padding-right: 20px;">
                <div>
                    <h3 class="detail-title" style="margin: 0 0 4px 0; color: #0f766e; font-family: var(--font-display, sans-serif); font-size: 1.3rem; font-weight: 700;">${j.request ? j.request.categoria : 'Tarea General'}</h3>
                    <p class="detail-subtitle" style="margin: 0; font-size: 0.82rem; color: var(--admin-text-secondary, #6b7280);">Reclamo ID: <strong>${j.request ? j.request.id : 'N/A'}</strong> | Orden #${j.id}</p>
                </div>

                <div style="border-top: 1px solid var(--admin-border, #e5e7eb); padding-top: 10px;">
                    <label class="detail-label" style="display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-secondary, #6b7280); font-weight: bold; margin-bottom: 2px;">Vecino Solicitante</label>
                    <p class="detail-value" style="margin: 0; font-weight: 500; font-size: 0.9rem;">${j.request ? j.request.vecino : 'Vecino'}</p>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--admin-text-secondary, #6b7280);">${j.request ? j.request.email : ''}</p>
                </div>

                <div>
                    <label class="detail-label" style="display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-secondary, #6b7280); font-weight: bold; margin-bottom: 2px;">Dirección y Especie</label>
                    <p class="detail-value" style="margin: 0; font-weight: 500; font-size: 0.9rem;">${j.request ? j.request.direccion : 'Sin dirección'}</p>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--admin-text-secondary, #6b7280);">Especie: ${j.request ? j.request.especie : 'Sin especificar'}</p>
                </div>

                <div style="flex: 1; min-height: 80px; display: flex; flex-direction: column;">
                    <label class="detail-label" style="display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-secondary, #6b7280); font-weight: bold; margin-bottom: 2px;">Mensaje / Descripción</label>
                    <div class="detail-box" style="flex: 1; font-size: 0.85rem; line-height: 1.4; padding: 10px; background: rgba(0,0,0,0.02); border-radius: 6px; border: 1px solid var(--admin-border, #e5e7eb); overflow-y: auto;">
                        ${j.request ? j.request.descripcion : 'No se incluyeron detalles en el reclamo.'}
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: GESTIÓN DE LA EMPRESA -->
            <div class="claim-modal-col-right" style="display: flex; flex-direction: column; gap: 15px;">
                
                <!-- Progress Tracker dots (Sincronizado) -->
                <div class="status-tracker-container" style="background: rgba(0,0,0,0.01); border: 1px solid var(--admin-border, #e5e7eb); border-radius: 8px; padding: 12px;">
                    <div style="font-size: 0.8rem; font-weight: bold; color: var(--admin-text-primary, #111827); margin-bottom: 8px;">Estado Sincronizado en TreeBA:</div>
                    <div class="status-steps" style="display: flex; justify-content: space-between; gap: 4px; margin-bottom: 5px;">
                        ${(() => {
            const reqStatus = j.request ? j.request.estado : 'open';
            const currentSeq = companyRequestStatuses.find(rs => rs.slug === reqStatus)?.sequence || 0;

            return companyRequestStatuses.map(s => {
                const isCompleted = s.sequence <= currentSeq;
                const isActive = reqStatus === s.slug;
                return `
                                    <div class="status-step ${isCompleted ? 'completed' : ''} ${isActive ? 'active' : ''}" style="flex: 1; display: flex; flex-direction: column; align-items: center; text-align: center;">
                                        <div class="step-circle" style="width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold; border: 2px solid ${isActive ? s.color : (isCompleted ? '#10b981' : '#d1d5db')}; background: ${isActive ? s.color : (isCompleted ? '#10b98120' : '#fff')}; color: ${isActive ? '#fff' : (isCompleted ? '#10b981' : '#9ca3af')}; cursor: default;">
                                            ${s.sequence}
                                        </div>
                                        <div class="step-label" style="font-size: 0.65rem; margin-top: 4px; color: ${isActive ? 'var(--admin-accent)' : '#6b7280'}; line-height: 1.1;">${s.status_name}</div>
                                    </div>
                                `;
            }).join('');
        })()}
                    </div>
                </div>

                <div class="detail-section">
                    <p class="detail-label" style="display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-secondary, #6b7280); font-weight: bold; margin-bottom: 2px;">Especificación Técnica del Trabajo</p>
                    <p class="detail-value" style="margin: 0; font-weight: 600; font-size: 0.95rem; color: var(--admin-accent, #0f766e);">${j.task_description}</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label class="detail-label" style="display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-secondary, #6b7280); font-weight: bold; margin-bottom: 2px;">Orden de Ejecución</label>
                        <p class="detail-value" style="margin: 0; font-weight: 500;">Nivel ${j.execution_order || 1}</p>
                    </div>
                    <div>
                        <label class="detail-label" style="display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-secondary, #6b7280); font-weight: bold; margin-bottom: 2px;">Fecha Programada</label>
                        <p class="detail-value" style="margin: 0; font-weight: 500;">${j.scheduled_date || 'Sin programar'}</p>
                    </div>
                </div>

                <div class="detail-box" style="margin-top: 15px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border, #e5e7eb); border-radius: 8px; padding: 15px;">
                    <h4 style="margin-top: 0; margin-bottom: 10px; color: var(--admin-accent); font-family: var(--font-display); font-size: 0.9rem;">Acciones de Contratista</h4>
                    
                    ${j.work_status === 'Finalizado' ? `
                        <div style="color: #22c55e; font-weight: bold; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; line-height: 1.4;">
                            <span>✔ Tarea Completada. El control ha sido devuelto a la comuna para su certificación y notificación al vecino.</span>
                        </div>
                    ` : `
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            ${j.work_status === 'Asignado' || j.work_status === 'En espera' ? `
                                <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'En Proceso')" style="width: 100%; padding: 8px; font-size: 0.8rem; background-color: #ea580c; border-color: #ea580c; border-radius: 6px; color: #fff; cursor: pointer; font-weight: bold; border: none; text-align: center;">
                                    🚀 Iniciar Tarea (Pasar a "En curso")
                                </button>
                            ` : ''}
                            
                            ${j.work_status === 'En Proceso' ? `
                                <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'Finalizado')" style="width: 100%; padding: 8px; font-size: 0.8rem; background-color: #22c55e; border-color: #22c55e; border-radius: 6px; color: #fff; cursor: pointer; font-weight: bold; border: none; text-align: center;">
                                    ✔ Marcar como Finalizado (Pasar a "Completado")
                                </button>
                            ` : ''}
                        </div>
                    `}
                </div>
            </div>
        </div>
    `;
}
