/**
 * Template (Dashboard Empresa): Funciones para generar HTML modular de trabajos.
 */

import { getProgressTrackerHtml } from '../shared/ui-components.js';

export function getJobListCardHtml(j, isSelected) {
    let statusColor = '#3498db';
    if (j.work_status === 'En Proceso') statusColor = '#e67e22';
    else if (j.work_status === 'Finalizado') statusColor = '#22c55e';
    else if (j.work_status === 'En espera') statusColor = '#95a5a6';

    return `
        <div class="list-item-header">
            <span class="list-item-id">Orden #${j.id}</span>
            <span class="badge-status" style="background-color: ${statusColor}20; color: ${statusColor}; border: 1px solid ${statusColor};">${j.work_status}</span>
        </div>
        <div class="list-item-title">${j.task_description}</div>
        <div class="list-item-subtitle" style="display: flex; justify-content: space-between;">
            <span>Reclamo: ${j.request ? j.request.id : 'N/A'}</span>
            <span>Programado: ${j.scheduled_date || 'N/A'}</span>
        </div>
    `;
}

export function getJobModalHtml(j) {
    const jobStatuses = [
        { label: 'En espera', sequence: 1, color: '#9ca3af', slug: 'En espera' },
        { label: 'Asignado', sequence: 2, color: '#3b82f6', slug: 'Asignado' },
        { label: 'En Proceso', sequence: 3, color: '#eab308', slug: 'En Proceso' },
        { label: 'Finalizado', sequence: 4, color: '#22c55e', slug: 'Finalizado' }
    ];

    const currentSeq = jobStatuses.find(s => s.slug === j.work_status)?.sequence || 1;

    let statusColor = '#3498db';
    if (j.work_status === 'En Proceso') statusColor = '#e67e22';
    else if (j.work_status === 'Finalizado') statusColor = '#22c55e';
    else if (j.work_status === 'En espera') statusColor = '#95a5a6';

    let html = `<div class="claim-modal-grid">`;

    // --- COLUMNA IZQUIERDA: DETALLES ---
    html += `<div class="claim-modal-col-left">
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${j.task_description}</h3>
                <p class="detail-subtitle">Orden de Trabajo #${j.id} | Asignado el ${j.created_at || 'Recientemente'}</p>
            </div>
            <span class="badge-status" style="background-color: ${statusColor}20; color: ${statusColor}; border: 1px solid ${statusColor};">${j.work_status}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Ubicación del Reclamo Comunal</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${j.request ? j.request.direccion || 'Av. Cabildo 2800' : 'Av. Cabildo 2800'} (${j.request ? j.request.especie : 'Jacarandá'})</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Detalles del Reclamo / Especificaciones</p>
            <p class="detail-value">${j.request ? j.request.descripcion || 'Poda preventiva de ramas con peligro de caída.' : 'Poda preventiva de ramas con peligro de caída.'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Orden de Ejecución Secuencial</p>
            <p class="detail-value">Intervención Nivel: <strong>${j.execution_order || 1}</strong></p>
        </div>
    </div> <!-- FIN COLUMNA IZQUIERDA -->`;

    // --- COLUMNA DERECHA: GESTIÓN ---
    html += `<div class="claim-modal-col-right">
        <div class="status-tracker-title" style="margin-bottom: 10px;">Progreso de la Tarea:</div>`;

    html += getProgressTrackerHtml(jobStatuses, currentSeq, j.work_status);

    html += `
        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Actualizar Estado del Trabajo</h4>
            
            ${j.work_status === 'Asignado' ? `
                <!-- TODO: Queda pendiente hacer una función automática que si se programó una fecha, automáticamente el estado cumplida esa fecha pase a en proceso -->
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; background: white; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--admin-border);">
                    <span style="font-size: 0.9rem; font-weight: 500; color: var(--admin-text-secondary); white-space: nowrap;">Programar para:</span>
                    <input type="date" id="job-schedule-date-${j.id}" class="admin-input" value="${j.scheduled_date || ''}" style="padding: 4px 8px; font-size: 0.9rem; height: 32px; flex: 1;">
                    <button class="btn-secondary" onclick="window.updateJobSchedule(${j.id})" style="padding: 6px 12px; font-size: 0.85rem; height: 32px; display: flex; align-items: center;">Guardar</button>
                </div>
            ` : (j.scheduled_date ? `
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; background: white; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--admin-border);">
                    <span style="font-size: 0.9rem; font-weight: 500; color: var(--admin-text-secondary); white-space: nowrap;">Programado para el:</span>
                    <span style="font-size: 0.9rem; color: var(--admin-text-primary); font-weight: 600;">${j.scheduled_date.split('-').reverse().join('/')}</span>
                </div>
            ` : '')}

            ${j.work_status === 'Finalizado' ? `
                <div style="color: #22c55e; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    <span>✔ Tarea Completada. El control ha sido devuelto al inspector de la comuna para validar el pago.</span>
                </div>
            ` : `
                
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    ${j.work_status === 'En Proceso' ? `
                        <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'Asignado')" style="background-color: transparent; color: #ef4444; border: 1px solid #ef4444;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: text-bottom; margin-right: 4px;"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            Suspender/Demorar
                        </button>
                    ` : ''}

                    ${j.work_status === 'Asignado' || j.work_status === 'En espera' ? `
                        <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'En Proceso')" style="background-color: #eab308; border-color: #ca8a04;">
                            Iniciar Tarea (En Proceso)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: text-bottom; margin-left: 4px;"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    ` : ''}
                    
                    ${j.work_status === 'En Proceso' ? `
                        <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'Finalizado')" style="background-color: #22c55e; border-color: #16a34a;">
                            Marcar como Finalizado
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: text-bottom; margin-left: 4px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </button>
                    ` : ''}
                </div>
            `}
        </div>
    </div> <!-- FIN COLUMNA DERECHA -->`;

    html += `</div> <!-- FIN GRID -->`;
    return html;
}
