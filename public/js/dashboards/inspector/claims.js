/**
 * Componente (Dashboard Inspector): Lógica para la visualización, revisión y auditoría de reclamos.
 */

import { getCsrfToken, showNotification } from '../shared/layout.js';
import { state } from './state.js';
import { fetchClaims, fetchRequestStatuses, fetchActiveCompanies, updateClaimStatus } from './api.js';
import { updateStats } from './ui.js';
export function loadClaimsList() {
    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';

    state.claims.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => selectClaim(c.id);

        const statusObj = state.requestStatuses.find(rs => rs.slug === c.estado);
        let statusLabel = statusObj ? statusObj.status_name : c.estado.toUpperCase();
        let statusHex = statusObj ? statusObj.color : '#6b7280';
        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">${c.id}</span>
                <span class="badge-status" style="background-color: ${statusHex}20; color: ${statusHex}; border: 1px solid ${statusHex};">${statusLabel}</span>
            </div>
            <div class="list-item-title">${c.categoria}</div>
            <div class="list-item-subtitle">${c.direccion}</div>
            <div style="font-size: 0.75rem; text-align: right; color: rgba(245,249,246,0.4); margin-top: 5px;">${c.fecha}</div>
        `;
        container.appendChild(card);
    });
};

export function selectClaim(id) {
    state.selectedClaimId = id;
    loadClaimsList();

    const claim = state.claims.find(c => c.id === id);
    const modal = document.getElementById('claim-detail-modal');
    const panel = document.getElementById('claim-modal-body-content');

    if (!claim || !panel || !modal) return;

    const statusObj = state.requestStatuses.find(rs => rs.slug === claim.estado);
    const statusLabel = statusObj ? statusObj.status_name : claim.estado.toUpperCase();
    const statusHex = statusObj ? statusObj.color : '#6b7280';

    panel.innerHTML = `
        <div class="claim-modal-grid">
            <!-- COLUMNA IZQUIERDA: DETALLES -->
            <div class="claim-modal-col-left">
                ${claim.suggested_duplicate_id ? `
                <div style="background-color: #fef08a; border-left: 4px solid #eab308; padding: 12px; margin-bottom: 20px; border-radius: 4px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong style="color: #854d0e; display: block; margin-bottom: 4px;">⚠️ Alerta de Sistema Inteligente</strong>
                        <span style="color: #a16207; font-size: 0.9rem;">Este reclamo podría ser un duplicado del reclamo <strong>#${claim.suggested_duplicate_id}</strong> (misma cuadra y tipo de problema).</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="resolveDuplicate(true, ${claim.suggested_duplicate_id})" style="background: #eab308; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: bold;">✅ Vincular Automáticamente</button>
                        <button onclick="resolveDuplicate(false)" style="background: transparent; color: #a16207; border: 1px solid #a16207; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">❌ Ignorar</button>
                    </div>
                </div>
                ` : ''}

                ${claim.linked_to ? `
                <div style="background-color: #fce7f3; border-left: 4px solid #db2777; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    <strong style="color: #9d174d;">🔗 Reclamo Vinculado</strong>
                    <span style="color: #be185d; font-size: 0.9rem; margin-left: 8px;">Este trámite es un duplicado y está anexado al reclamo principal <strong>#${claim.linked_to}</strong>.</span>
                </div>
                ` : ''}

                <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${claim.categoria}</h3>
                <p class="detail-subtitle">Reclamo ID: <strong style="color:var(--admin-text-primary);">${claim.id}</strong> | Enviado el ${claim.fecha}</p>
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
    </div> <!-- FIN COLUMNA IZQUIERDA -->

    <!-- COLUMNA DERECHA: GESTIÓN -->
    <div class="claim-modal-col-right">
        <div class="status-tracker-container">
            <div class="status-tracker-title">Progreso del Reclamo actual:</div>
            <div class="status-steps">
                ${state.requestStatuses.map(s => {
        const currentSeq = state.requestStatuses.find(rs => rs.slug === claim.estado)?.sequence || 0;
        const isCompleted = s.sequence && s.sequence <= currentSeq;
        const isActive = claim.estado === s.slug;
        if (s.sequence || isActive) {
            return `
                        <div class="status-step ${isCompleted ? 'completed' : ''} ${isActive ? 'active' : ''}">
                            <div class="step-circle" style="background-color: ${isActive ? s.color : ''}; border-color: ${isActive ? s.color : ''}">${s.sequence || '!'}</div>
                            <div class="step-label">${s.status_name}</div>
                        </div>`;
        }
        return '';
    }).join('')}
            </div>
        </div>

        <div class="response-section" style="margin-top: 10px;">
            <h4 class="detail-title" style="font-size: 1.05rem; margin-bottom: 8px;">Actualizar Trámite y Responder al Vecino</h4>
            
            <div style="margin-bottom: 10px;">
                <label style="font-size: 0.85rem; font-weight: bold; color: var(--admin-text-primary); display: block; margin-bottom: 4px;">Cambiar estado a:</label>
                <select id="new-status-select" style="width: 100%; background: #fff; border: 1px solid var(--admin-border); border-radius: 8px; padding: 6px; color: var(--admin-text-primary); font-family: var(--font-body); font-size: 0.85rem;" onchange="if(this.value === 'vinculated') alert('Se te pedirá el ID a vincular al guardar.')">
                    ${state.requestStatuses.map(s => `
                        <option value="${s.slug}" ${claim.estado === s.slug ? 'selected' : ''}>${s.status_name}</option>
                    `).join('')}
                </select>
            </div>

            <label style="font-size: 0.85rem; font-weight: bold; color: var(--admin-text-primary); display: block; margin-bottom: 4px;">Mensaje / Respuesta (Opcional):</label>
            <div class="template-selector">
                <button class="template-btn" onclick="applyTemplate('info')">Pedir más info</button>
                <button class="template-btn" onclick="applyTemplate('relevated')">Avisar Inspección</button>
                <button class="template-btn" onclick="applyTemplate('scheduled')">Avisar Poda</button>
                <button class="template-btn" onclick="applyTemplate('resolved')">Informar Resolución</button>
                <button class="template-btn" onclick="applyTemplate('denied')">Rechazar</button>
            </div>
            <textarea id="response-text" class="response-textarea" rows="3" style="min-height: 60px;" placeholder="Escribe un correo de respuesta al vecino (Si se deja vacío, solo se actualizará el estado interno)..."></textarea>
            
            <div class="action-row" style="display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                <button class="btn-secondary" onclick="clearResponse()">Limpiar</button>
                <button class="btn-primary" onclick="smartUpdateClaim()">Guardar y Actualizar Estado</button>
            </div>
        </div>
    </div> <!-- FIN COLUMNA DERECHA -->

    <!-- COLUMNA TERCERA: TAREAS Y EMPRESAS -->
    <div class="claim-modal-col-third">
        <div class="response-section" style="margin-top: 10px; background-color: #f8fafc; border: 1px solid #e2e8f0;">
            <h4 class="detail-title" style="font-size: 1.05rem; margin-bottom: 8px;">Asignación de Tareas Externas</h4>
            <p style="font-size: 0.8rem; color: var(--admin-text-secondary); margin-bottom: 15px;">Genera órdenes de trabajo (Poda, Extracción, etc.) para las empresas contratistas.</p>
            
            <div style="margin-bottom: 10px;">
                <label style="font-size: 0.85rem; font-weight: bold; color: var(--admin-text-primary); display: block; margin-bottom: 4px;">Tipo de Tarea:</label>
                <select id="new-task-type-select" style="width: 100%; background: #fff; border: 1px solid var(--admin-border); border-radius: 8px; padding: 6px; color: var(--admin-text-primary); font-family: var(--font-body); font-size: 0.85rem;">
                    <option value="">-- Seleccionar Tarea --</option>
                    ${([...new Set(state.claims.map(c => c.categoria))].filter(c => c !== 'Otro' && c !== 'Arbol no mapeado')).map(cat => `
                        <option value="${cat}">${cat}</option>
                    `).join('')}
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.85rem; font-weight: bold; color: var(--admin-text-primary); display: block; margin-bottom: 4px;">Empresa Contratista:</label>
                <select id="new-task-company-select" style="width: 100%; background: #fff; border: 1px solid var(--admin-border); border-radius: 8px; padding: 6px; color: var(--admin-text-primary); font-family: var(--font-body); font-size: 0.85rem;">
                    <option value="">-- Seleccionar Empresa --</option>
                    ${(state.activeCompanies || []).map(c => `
                        <option value="${c.id}">${c.name || c.company_name}</option>
                    `).join('')}
                </select>
            </div>

            <button class="btn-primary" onclick="createWorkOrder(${claim.raw_request_id || claim.id})" style="width: 100%; padding: 8px 12px; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Asignar Nueva Tarea
            </button>
        </div>

        <div style="margin-top: 20px;">
            <h4 style="font-size: 0.9rem; margin-bottom: 10px; border-bottom: 1px solid var(--admin-border); padding-bottom: 5px;">Tareas Asignadas (${claim.work_orders ? claim.work_orders.length : 0})</h4>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                ${(claim.work_orders && claim.work_orders.length > 0) ? claim.work_orders.map(wo => `
                    <div style="background: white; border: 1px solid var(--admin-border); border-radius: 6px; padding: 10px; border-left: 4px solid var(--admin-accent);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <strong style="font-size: 0.85rem; color: var(--admin-text-primary);">${wo.task_description}</strong>
                            <span style="font-size: 0.7rem; background: #e2e8f0; padding: 2px 6px; border-radius: 10px; color: #475569;">Orden #${wo.execution_order}</span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--admin-text-secondary); margin-bottom: 4px;">🏢 Empresa: <span style="color: var(--admin-text-primary);">${wo.company || 'Sin Asignar'}</span></div>
                        <div style="font-size: 0.8rem; color: var(--admin-text-secondary);">⚙️ Estado: <strong style="color: var(--admin-primary);">${wo.status}</strong></div>
                    </div>
                `).join('') : '<p style="font-size: 0.8rem; color: var(--admin-text-secondary); font-style: italic;">No hay tareas asignadas para este reclamo.</p>'}
            </div>
        </div>
    </div> <!-- FIN COLUMNA TERCERA -->
</div> <!-- FIN GRID -->
    `;
    modal.style.display = 'flex';
};

export function applyTemplate(type) {
    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    if (!claim) return;

    const textarea = document.getElementById('response-text');

    let text = '';
    if (type === 'open' || type === 'info') {
        text = `Estimado/a ${claim.vecino},\n\nHemos recibido su solicitud ID ${claim.id} sobre "${claim.categoria}". Un inspector del área técnica estará evaluando la situación a la brevedad. Si posee más imágenes del estado actual del ejemplar, por favor adjúntelas respondiendo a este correo.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'relevated') {
        text = `Estimado/a ${claim.vecino},\n\nLe informamos que su solicitud ID ${claim.id} se encuentra en etapa de Inspección Técnica. Personal calificado visitará la dirección ${claim.direccion} dentro de los próximos 3 días hábiles para diagnosticar el árbol (${claim.especie}) y planificar el plan de acción.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'scheduled' || type === 'in_progress') {
        text = `Estimado/a ${claim.vecino},\n\nTras la inspección realizada en ${claim.direccion}, se ha planificado la intervención correspondiente para el día [Fecha]. Se realizará un saneamiento/poda de despeje preventivo para resguardar la seguridad pública.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'resolved') {
        text = `Estimado/a ${claim.vecino},\n\nNos complace informarle que la solicitud ID ${claim.id} ha sido completada de manera exitosa. Las tareas operativas y el despeje final en la zona han concluido.\n\nMuchas gracias por colaborar con el mantenimiento del arbolado de la Ciudad.\n\nAtentamente,\nGobierno de la Ciudad de Buenos Aires - Comuna 13.`;
    } else if (type === 'denied') {
        text = `Estimado/a ${claim.vecino},\n\nTras la evaluación técnica de su solicitud ID ${claim.id}, lamentamos informarle que la misma ha sido rechazada por no cumplir con los criterios de intervención de la Ley de Arbolado.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'vinculated') {
        text = `Estimado/a ${claim.vecino},\n\nLe informamos que su solicitud ID ${claim.id} ha sido vinculada a un trámite preexistente sobre el mismo ejemplar o incidencia.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    }

    if (textarea) textarea.value = text;
};

export function clearResponse() {
    const textarea = document.getElementById('response-text');
    if (textarea) textarea.value = '';
};

export async function smartUpdateClaim() {
    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    if (!claim) return;

    const responseText = document.getElementById('response-text').value.trim();
    const newStatus = document.getElementById('new-status-select').value;

    let payload = { estado: newStatus };
    if (responseText !== '') {
        payload.respuesta = responseText;
    }

    if (newStatus === 'vinculated') {
        const manualId = prompt('Ingrese el ID numérico del reclamo original al que desea vincularlo (Ej: 18):');
        if (!manualId) return; // Canceló
        payload.linked_to = parseInt(manualId);
    }

    try {
        const response = await fetch(`/requests/update-status/${state.selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            claim.estado = newStatus;

            if (newStatus === 'vinculated') {
                claim.linked_to = payload.linked_to;
            } else {
                claim.linked_to = null;
            }
            claim.suggested_duplicate_id = null;

            if (responseText !== '') {
                claim.respuesta_admin = responseText;
            }

            const banner = document.getElementById('notification-banner');
            const text = document.getElementById('notification-text');
            if (text) text.innerText = `El estado se actualizó a '${newStatus}' y los cambios fueron guardados.`;
            if (banner) banner.style.display = 'flex';

            setTimeout(() => {
                if (banner) banner.style.display = 'none';
            }, 5000);

            selectClaim(state.selectedClaimId); // Re-render
            if (typeof updateStats === 'function') updateStats();
            loadClaimsList();
        } else {
            alert('Error al actualizar el estado/respuesta en el servidor.');
        }
    } catch (err) {
        console.error("Error updating claim:", err);
        alert('Error de conexión.');
    }
};

window.smartUpdateClaim = smartUpdateClaim;
window.closeClaimDetailModal = function () {
    document.getElementById('claim-detail-modal').style.display = 'none';
};

export function filterClaims() {
    const query = document.getElementById('search-claims').value.toLowerCase();
    const statusFilter = document.getElementById('filter-claim-status') ? document.getElementById('filter-claim-status').value : '';
    const categoryFilter = document.getElementById('filter-claim-category') ? document.getElementById('filter-claim-category').value : '';

    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';

    const filtered = state.claims.filter(c => {
        const matchesQuery = c.vecino.toLowerCase().includes(query) ||
            c.direccion.toLowerCase().includes(query) ||
            c.id.toLowerCase().includes(query);
        const matchesStatus = !statusFilter || c.estado === statusFilter;
        const matchesCategory = !categoryFilter || c.categoria === categoryFilter;

        return matchesQuery && matchesStatus && matchesCategory;
    });

    filtered.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => selectClaim(c.id);

        const statusObj = state.requestStatuses.find(rs => rs.slug === c.estado);
        let statusLabel = statusObj ? statusObj.status_name : c.estado.toUpperCase();
        let statusHex = statusObj ? statusObj.color : '#6b7280';
        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">${c.id}</span>
                <span class="badge-status" style="background-color: ${statusHex}20; color: ${statusHex}; border: 1px solid ${statusHex};">${statusLabel}</span>
            </div>
            <div class="list-item-title">${c.categoria}</div>
            <div class="list-item-subtitle">${c.direccion}</div>
            <div style="font-size: 0.75rem; text-align: right; color: var(--admin-text-secondary); margin-top: 5px;">${c.fecha}</div>
        `;
        container.appendChild(card);
    });
};

export async function setClaimStatus(newStatus) {
    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    if (!claim) return;

    let payload = { estado: newStatus };

    if (newStatus === 'vinculated') {
        const manualId = prompt('Ingrese el ID numérico del reclamo original al que desea vincularlo:');
        if (!manualId) return;
        payload.linked_to = parseInt(manualId);
    }

    try {
        const response = await fetch(`/requests/update-status/${state.selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            claim.estado = newStatus;

            if (newStatus === 'vinculated') {
                claim.linked_to = payload.linked_to;
            } else {
                claim.linked_to = null; // Limpiar del array local para que se actualice la vista al instante
            }

            claim.suggested_duplicate_id = null;

            selectClaim(state.selectedClaimId);
            updateStats();
            applyTemplate(newStatus);
            loadClaimsList();
        } else {
            alert('Error al actualizar el estado en el servidor.');
        }
    } catch (err) {
        console.error("Error al actualizar estado:", err);
    }
};

export async function resolveDuplicate(isAccepted, duplicateId = null) {
    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    if (!claim) return;

    try {
        const payload = isAccepted ? { estado: 'vinculated', linked_to: duplicateId } : { ignore_suggestion: true };

        const response = await fetch(`/requests/update-status/${state.selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            if (isAccepted) {
                claim.estado = 'vinculated';
                claim.linked_to = duplicateId;
            }
            claim.suggested_duplicate_id = null;

            selectClaim(state.selectedClaimId);
            updateStats();
            loadClaimsList();
        } else {
            alert('Error al actualizar la sugerencia.');
        }
    } catch (err) {
        console.error(err);
    }
};

export async function loadStatusesFromServer() {
    try {
        const response = await fetch('/api/request-statuses');
        if (response.ok) {
            const result = await response.json();
            state.requestStatuses = result.data;
        }
    } catch (err) {
        console.error("Error al cargar estados:", err);
    }
};

export async function loadClaimsFromServer() {
    await loadActiveCompanies();
    if (state.requestStatuses.length === 0) {
        await loadStatusesFromServer();
    }

    // Poblar el selector de estados dinámicamente desde la BD
    const statusSelect = document.getElementById('filter-claim-status');
    if (statusSelect && statusSelect.options.length <= 1) {
        state.requestStatuses.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.slug;
            opt.textContent = s.status_name;
            statusSelect.appendChild(opt);
        });
    }

    try {
        const response = await fetch('/requests', {
            headers: {
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            const result = await response.json();
            state.claims = result.data;
            updateStats();
            loadClaimsList();
            initClaimsMap();
            updateClaimsMapMarkers();

            // Poblar el selector de categorías dinámicamente desde los reclamos cargados
            const catSelect = document.getElementById('filter-claim-category');
            if (catSelect && catSelect.options.length <= 1) {
                const uniqueCategories = [...new Set(state.claims.map(c => c.categoria))];
                uniqueCategories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat;
                    opt.textContent = cat;
                    catSelect.appendChild(opt);
                });
            }

            if (state.selectedClaimId) {
                selectClaim(state.selectedClaimId);
            }
        }
    } catch (err) {
        console.error("Error al cargar reclamos del servidor:", err);
    }
};

export async function loadActiveCompanies() {
    try {
        const response = await fetch('/admin/companies');
        if (response.ok) {
            const data = await response.json();
            // Obtenemos el array de empresas
            state.activeCompanies = Array.isArray(data) ? data : (data.data || []);
        }
    } catch (err) {
        console.error("Error al cargar empresas activas:", err);
    }
};

window.createWorkOrder = createWorkOrder;

export async function createWorkOrder(numericClaimId) {
    const taskDescription = document.getElementById('new-task-type-select').value;
    const companyId = document.getElementById('new-task-company-select').value;
    const claim = state.claims.find(c => c.raw_request_id == numericClaimId || c.id == numericClaimId);

    if (!taskDescription) {
        alert('Por favor selecciona un tipo de tarea.');
        return;
    }
    if (!companyId) {
        alert('Por favor selecciona una empresa contratista.');
        return;
    }

    const currentOrder = (claim.work_orders ? claim.work_orders.length : 0) + 1;

    try {
        const response = await fetch('/work-orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                request_id: numericClaimId,
                company_id: companyId,
                task_description: taskDescription,
                execution_order: currentOrder
            })
        });

        if (response.ok) {
            alert('Tarea asignada correctamente a la empresa.');
            // Refetch data to sync everything (including mapping IDs and new tasks)
            loadClaimsFromServer();
        } else {
            const data = await response.json();
            alert('Error al asignar la tarea: ' + (data.message || 'Verifica los datos.'));
        }
    } catch (err) {
        console.error("Error al asignar tarea:", err);
        alert('Error al conectar con el servidor.');
    }
};

let claimsMapInstance = null;
let claimsMarkersGroup = null;

export function initClaimsMap() {
    const mapContainer = document.getElementById('claims-map');
    if (!mapContainer || claimsMapInstance) return;

    claimsMapInstance = L.map('claims-map', {
        zoomControl: false
    }).setView([-34.5888, -58.4285], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(claimsMapInstance);

    L.control.zoom({ position: 'bottomright' }).addTo(claimsMapInstance);
    claimsMarkersGroup = L.layerGroup().addTo(claimsMapInstance);

    setTimeout(() => {
        claimsMapInstance.invalidateSize();
    }, 200);
}

export function triggerMapResize() {
    if (claimsMapInstance) {
        setTimeout(() => {
            claimsMapInstance.invalidateSize();
        }, 150);
    }
}

export function updateClaimsMapMarkers() {
    if (!claimsMapInstance || !claimsMarkersGroup) return;

    claimsMarkersGroup.clearLayers();
    const bounds = [];

    state.claims.forEach(claim => {
        let lat = claim.lat || claim.latitude;
        let lng = claim.lng || claim.longitude;
        if (!lat || !lng) {
            const numId = parseInt(claim.id.replace(/\D/g, '')) || 0;
            lat = -34.5700 - (numId % 20) * 0.0015;
            lng = -58.4500 - (numId % 15) * 0.0012;
        }

        const statusObj = state.requestStatuses.find(rs => rs.slug === claim.estado);
        const color = statusObj ? statusObj.color : '#6b7280';

        const markerHtml = `
            <div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
        `;

        const customIcon = L.divIcon({
            html: markerHtml,
            className: 'custom-claim-marker',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(claimsMarkersGroup);

        marker.on('click', () => {
            selectClaim(claim.id);
        });

        bounds.push([lat, lng]);
    });

    if (bounds.length > 0) {
        claimsMapInstance.fitBounds(bounds, { padding: [30, 30] });
    }
}




