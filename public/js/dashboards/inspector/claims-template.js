/**
 * Template (Dashboard Inspector): Funciones para generar HTML modular de los reclamos.
 */

import { getProgressTrackerHtml } from '../shared/ui-components.js';

export function getClaimListCardHtml(c, isSelected, statusObj) {
    const statusLabel = statusObj ? statusObj.status_name : c.estado.toUpperCase();
    const statusHex = statusObj ? statusObj.color : '#6b7280';
    
    let priorityBadge = '';
    if (c.priority === 'auto-alta') {
        priorityBadge = `<span style="background-color: #fef2f2; color: #dc2626; border: 1px solid #dc2626; padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: bold; white-space: nowrap;">URGENTE</span>`;
    } else if (c.priority === 'auto-media') {
        priorityBadge = `<span style="background-color: #fffbeb; color: #d97706; border: 1px solid #d97706; padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: bold; white-space: nowrap;">PRECAUCIÓN</span>`;
    }

    return `
        <div class="list-item-header" style="align-items: flex-start;">
            <span class="list-item-id" style="margin-right: 5px; word-break: break-all;">${c.id}</span>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;">
                <span class="badge-status" style="background-color: ${statusHex}20; color: ${statusHex}; border: 1px solid ${statusHex}; white-space: nowrap;">${statusLabel}</span>
                ${priorityBadge}
            </div>
        </div>
        <div class="list-item-title">${c.categoria}</div>
        <div class="list-item-subtitle">${c.direccion}</div>
        <div style="font-size: 0.75rem; text-align: right; color: rgba(245,249,246,0.4); margin-top: 5px;">${c.fecha}</div>
    `;
}

export function getClaimModalHtml(claim, state) {
    const statusObj = state.requestStatuses.find(rs => rs.slug === claim.estado);
    const statusLabel = statusObj ? statusObj.status_name : claim.estado.toUpperCase();
    const statusHex = statusObj ? statusObj.color : '#6b7280';

    const currentSeq = state.requestStatuses.find(rs => rs.slug === claim.estado)?.sequence || 0;
    const showThirdColumn = ['relevated', 'scheduled', 'in_progress'].includes(claim.estado);

    let html = `<div class="claim-modal-grid" style="${!showThirdColumn ? 'grid-template-columns: 0.85fr 1.95fr !important;' : ''}">`;

    // --- COLUMNA IZQUIERDA: DETALLES ---
    html += `<div class="claim-modal-col-left">`;

    if (claim.suggested_duplicate_id) {
        html += `
            <div class="smart-alert-box">
                <div>
                    <strong class="smart-alert-title">⚠️ Alerta de Sistema Inteligente</strong>
                    <span class="smart-alert-text">Este reclamo podría ser un duplicado del reclamo <strong>#${claim.suggested_duplicate_id}</strong> (misma cuadra y tipo de problema).</span>
                </div>
                <div class="smart-alert-actions">
                    <button onclick="window.resolveDuplicate(true, ${claim.suggested_duplicate_id})" class="btn-smart-link">✅ Vincular Automáticamente</button>
                    <button onclick="window.resolveDuplicate(false)" class="btn-smart-ignore">❌ Ignorar</button>
                </div>
            </div>`;
    }

    if (claim.linked_to) {
        html += `
            <div class="linked-alert-box">
                <strong class="linked-alert-title">🔗 Reclamo Vinculado</strong>
                <span class="linked-alert-text">Este trámite es un duplicado y está anexado al reclamo principal <strong>#${claim.linked_to}</strong>.</span>
            </div>`;
    }

    html += `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${claim.categoria}</h3>
                <p class="detail-subtitle">
                    Reclamo ID: <strong style="color:var(--admin-text-primary);">${claim.id}</strong> | Enviado el ${claim.fecha}
                </p>
    `;

    if (claim.priority === 'auto-alta' || claim.priority === 'auto-media') {
        const isHigh = claim.priority === 'auto-alta';
        html += `
            <div class="priority-box ${isHigh ? 'high' : 'medium'}">
                <strong class="priority-title ${isHigh ? 'high' : 'medium'}">
                    Elevado automáticamente por el Sistema Inteligente
                </strong>
                <p class="priority-text">
                    El algoritmo de Procesamiento de Lenguaje detectó palabras clave en la solicitud.<br>
                    Score de Riesgo Calculado: <strong>${claim.risk_score || 'Pendiente'}/100</strong>
                </p>
            </div>
        `;
    }

    html += `
            </div>
            <span class="badge-status" id="detail-badge-status" style="background-color: ${statusHex}20; color: ${statusHex}; border: 1px solid ${statusHex};">${statusLabel}</span>
        </div>
        <div class="detail-section">
            <p class="detail-label">Vecino Solicitante</p>
            <p class="detail-value">${claim.vecino} (${claim.email})</p>
        </div>
        <div class="detail-section">
            <p class="detail-label">Dirección / Especie</p>
            <p class="detail-value">${claim.direccion} — Especie involucrada: ${claim.especie}</p>
        </div>
        <div class="detail-box">
            <p class="detail-label">Mensaje / Descripción del problema</p>
            <p class="detail-box-desc">${claim.descripcion}</p>
        </div>
    </div> <!-- FIN COLUMNA IZQUIERDA -->`;

    // --- COLUMNA DERECHA: GESTIÓN ---
    html += `<div class="claim-modal-col-right">
        <div class="status-tracker-title" style="margin-bottom: 10px;">Progreso del Reclamo actual:</div>`;

    const mappedSteps = state.requestStatuses.map(s => ({
        label: s.status_name,
        sequence: s.sequence,
        color: s.color,
        slug: s.slug
    }));

    html += getProgressTrackerHtml(mappedSteps, currentSeq, claim.estado);

    let rawPriority = claim.priority?.priority_name || claim.priority || 'Baja';
    if (typeof rawPriority === 'string') {
        const lowerP = rawPriority.toLowerCase();
        if (lowerP === 'low') rawPriority = 'Baja';
        if (lowerP === 'medium') rawPriority = 'Media';
        if (lowerP === 'high') rawPriority = 'Alta';
        if (lowerP === 'urgent') rawPriority = 'Urgente';
    }

    html += `
        <div class="status-tracker-container" style="margin-top: 8px;">
            <div class="right-panel-header">
                <h4 class="detail-title" style="font-size: 1.05rem; margin-bottom: 0;">Actualizar Trámite</h4>
                <div class="priority-selector-wrapper">
                    <label class="priority-selector-label">Prioridad:</label>
                    <input type="text" id="new-priority-select" list="priority-options" class="priority-selector-input" value="${rawPriority}" placeholder="Escribir...">
                    <datalist id="priority-options">
                        <option value="Baja">
                        <option value="Media">
                        <option value="Alta">
                        <option value="Urgente">
                    </datalist>
                </div>
            </div>
            
            <div class="status-selector-wrapper">
                <label class="status-selector-label">Cambiar Estado A:</label>
                <select id="new-status-select" class="status-selector-select" onchange="if(this.value === 'vinculated') alert('Se te pedirá el ID a vincular al guardar.')">`;

    state.requestStatuses.forEach(s => {
        if (!['in_progress', 'resolved'].includes(s.slug) || claim.estado === s.slug) {
            html += `<option value="${s.slug}" ${claim.estado === s.slug ? 'selected' : ''}>${s.status_name}</option>`;
        }
    });

    html += `</select>
            </div>

            <label class="response-label">Mensaje / Respuesta (Opcional):</label>
            <div class="template-selector template-buttons">
                <button class="template-btn" onclick="window.applyTemplate('info')">Pedir más info</button>
                <button class="template-btn" onclick="window.applyTemplate('relevated')">Avisar Inspección</button>
                <button class="template-btn" onclick="window.applyTemplate('scheduled')">Avisar Poda</button>
                <button class="template-btn" onclick="window.applyTemplate('resolved')">Informar Resolución</button>
                <button class="template-btn denied" onclick="window.applyTemplate('denied')">Denegar</button>
                <button class="template-btn vinculated" onclick="window.applyTemplate('vinculated')">Vincular</button>
            </div>
            <textarea id="response-text" class="response-textarea" rows="3" style="min-height: 60px;" placeholder="Escribe un correo de respuesta al vecino (Si se deja vacío, solo se actualizará el estado interno)..."></textarea>
            
            <div class="action-row" style="display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                <button class="btn-secondary" onclick="window.clearResponse()">Limpiar</button>
                <button class="btn-primary" onclick="window.smartUpdateClaim()">Guardar y Actualizar Estado</button>
            </div>
        </div>
    </div> <!-- FIN COLUMNA DERECHA -->`;

    // --- COLUMNA TERCERA: TAREAS (Si aplica) ---
    if (showThirdColumn) {
        html += `<div class="claim-modal-col-third">
            <div class="response-section third-col-section">
                <h4 class="detail-title" style="font-size: 1.05rem; margin-bottom: 8px;">Asignación de Tareas Externas</h4>
                <p class="third-col-desc">Genera órdenes de trabajo (Poda, Extracción, etc.) para las empresas contratistas.</p>
                
                <div class="task-form-group">
                    <label class="task-form-label">Tipo de Tarea:</label>
                    <select id="new-task-type-select" class="task-form-select">
                        <option value="">-- Seleccionar Tarea --</option>`;
        
        const uniqueCategories = [...new Set(state.claims.map(c => c.categoria))].filter(c => c !== 'Otro' && c !== 'Arbol no mapeado');
        uniqueCategories.forEach(cat => {
            html += `<option value="${cat}">${cat}</option>`;
        });

        html += `</select>
                </div>
                
                <div class="task-form-group">
                    <label class="task-form-label">Empresa Contratista:</label>
                    <select id="new-task-company-select" class="task-form-select">
                        <option value="">-- Seleccionar Empresa --</option>`;
        
        (state.activeCompanies || []).forEach(c => {
            html += `<option value="${c.id}">${c.name || c.company_name}</option>`;
        });

        html += `</select>
                </div>

                <button class="btn-primary btn-assign-work" onclick="window.queueWorkOrder(${claim.raw_request_id || claim.id})">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Asignar Trabajo
                </button>
            </div>

            <div style="margin-top: 20px;">
                <h4 class="assigned-tasks-header">Tareas Asignadas (${(claim.work_orders || []).length + (claim.pending_work_orders || []).length})</h4>
                <div class="assigned-tasks-list">`;

        const allTasks = (claim.work_orders || []).concat(claim.pending_work_orders || []);
        if (allTasks.length > 0) {
            allTasks.forEach(wo => {
                const isPending = wo.status === 'Pendiente de Guardar';
                html += `
                    <div class="assigned-task-card ${isPending ? 'pending' : ''}">
                        <div class="assigned-task-header">
                            <strong class="assigned-task-title">${wo.task_description}</strong>
                            <span class="assigned-task-order">Orden #${wo.execution_order}</span>
                        </div>
                        <div class="assigned-task-company">Empresa: <span style="color: var(--admin-text-primary);">${wo.company || 'Sin Asignar'}</span></div>
                        <div class="assigned-task-status-row">Estado: <strong style="color: ${isPending ? '#f97316' : 'var(--admin-primary)'};">${wo.status}</strong></div>
                    </div>`;
            });
        } else {
            html += `<p class="empty-tasks-msg">No hay tareas asignadas para este reclamo.</p>`;
        }

        html += `</div>
            </div>
        </div> <!-- FIN COLUMNA TERCERA -->`;
    }

    html += `</div> <!-- FIN GRID -->`;
    return html;
}
