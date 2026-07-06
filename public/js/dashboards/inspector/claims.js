/**
 * Componente (Dashboard Inspector): Lógica para la visualización, revisión y auditoría de reclamos.
 */

import { getCsrfToken, showNotification } from '../shared/layout.js';
import { state } from './state.js';
import { fetchClaims, fetchRequestStatuses, fetchActiveCompanies, updateClaimStatus } from './api.js';
import { updateStats } from './ui.js';

// Mapeo local de capacidades por empresa como fallback de seguridad
const companyCapabilities = {
    1: ['Poda Integral', 'Extracción y Destoconado'],
    2: ['Tratamiento Fitosanitario']
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

    state.claims.forEach(c => {
        let lat = c.lat || c.latitude;
        let lng = c.lng || c.longitude;

        if (!lat || !lng) {
            const numId = parseInt(c.id.replace(/\D/g, '')) || 1;
            lat = -34.5700 - (numId % 20) * 0.0015;
            lng = -58.4500 - (numId % 15) * 0.0012;
        }

        if (lat && lng) {
            const statusObj = state.requestStatuses.find(rs => rs.slug === c.estado);
            const markerColor = statusObj ? statusObj.color : '#10b981';

            const customIcon = L.divIcon({
                className: 'custom-claim-marker',
                html: `<div style="background-color: ${markerColor}; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 0 4px rgba(0,0,0,0.4);"></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            const marker = L.marker([lat, lng], { icon: customIcon })
                .bindPopup(`
                    <div style="font-family: var(--font-body, system-ui, sans-serif); padding: 4px; min-width: 160px;">
                        <h4 style="margin: 0 0 6px 0; color: #0f766e; font-size: 0.95rem; font-weight: 700; line-height: 1.2;">${c.categoria}</h4>
                        <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 6px; color: #4b5563; font-size: 0.8rem;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span>${c.direccion}</span>
                        </div>
                        <div style="color: #b45309; font-weight: 600; font-size: 0.8rem; margin-bottom: 8px;">#${c.id}</div>
                        <button onclick="selectClaim('${c.id}')" style="display: block; width: 100%; font-size: 0.72rem; padding: 8px 10px; background: #2d7a4f; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; text-align: center; box-shadow: 0 2px 4px rgba(45,122,79,0.15); transition: background-color 0.2s;">
                            Ver Más Datos
                        </button>
                    </div>
                `);
            
            claimsMarkersGroup.addLayer(marker);
            bounds.push([lat, lng]);
        }
    });

    if (bounds.length > 0) {
        claimsMapInstance.fitBounds(bounds, { padding: [30, 30] });
    }
}

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
            <div style="font-size: 0.75rem; text-align: right; color: rgba(107,114,128,0.7); margin-top: 5px;">${c.fecha}</div>
        `;
        container.appendChild(card);
    });
}

// Cerrar el modal de reclamo
window.closeClaimDetailModal = function() {
    const modal = document.getElementById('claim-detail-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('active');
    }
};

// Seleccionar y ver detalle de un reclamo en ventana emergente (modal) de ancho completo
export function selectClaim(id) {
    state.selectedClaimId = id;
    loadClaimsList();

    const claim = state.claims.find(c => c.id === id);
    const modal = document.getElementById('claim-detail-modal');
    const content = document.getElementById('claim-modal-body-content');

    if (!claim || !modal || !content) return;

    // Inicializar el estado temporal si no está asignado o es diferente
    if (state.tempSelectedStatus === undefined || state.selectedClaimId !== state.lastSelectedClaimId) {
        state.tempSelectedStatus = claim.estado;
        state.lastSelectedClaimId = id;
    }

    const statusObj = state.requestStatuses.find(rs => rs.slug === claim.estado);
    const statusLabel = statusObj ? statusObj.status_name : claim.estado.toUpperCase();
    const statusHex = statusObj ? statusObj.color : '#6b7280';

    // Obtener los trabajos (work orders) asignados a este reclamo
    const numericId = parseInt(claim.id.replace(/\D/g, '')) || claim.id;
    const jobs = claim.work_orders || (state.workOrders || []).filter(w => w.request_id === numericId);

    // Renderizar la grilla horizontal interna (izquierda detalles, derecha gestión)
    content.innerHTML = `
        <div class="claim-modal-grid">
            <!-- COLUMNA IZQUIERDA: DETALLES -->
            <div class="claim-modal-col-left">
                ${claim.suggested_duplicate_id ? `
                <div style="background-color: #fef08a; border-left: 4px solid #eab308; padding: 12px; border-radius: 8px;">
                    <strong style="color: #854d0e; display: block; margin-bottom: 4px; font-size: 0.85rem;">⚠️ Alerta de Duplicado Inteligente</strong>
                    <span style="color: #a16207; font-size: 0.8rem; display: block; margin-bottom: 8px;">Podría ser duplicado de #${claim.suggested_duplicate_id}.</span>
                    <div style="display: flex; gap: 6px;">
                        <button onclick="resolveDuplicate(true, ${claim.suggested_duplicate_id})" style="background: #eab308; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: bold;">Vincular</button>
                        <button onclick="resolveDuplicate(false)" style="background: transparent; color: #a16207; border: 1px solid #a16207; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">Ignorar</button>
                    </div>
                </div>
                ` : ''}

                ${claim.linked_to ? `
                <div style="background-color: #fce7f3; border-left: 4px solid #db2777; padding: 10px; border-radius: 8px;">
                    <strong style="color: #9d174d; font-size: 0.85rem;">🔗 Reclamo Vinculado</strong>
                    <span style="color: #be185d; font-size: 0.8rem; display: block;">Anexado al trámite principal #${claim.linked_to}.</span>
                </div>
                ` : ''}

                <div>
                    <h3 class="detail-title">${claim.categoria}</h3>
                    <p class="detail-subtitle">Reclamo ID: <strong>${claim.id}</strong> | Fecha: ${claim.fecha}</p>
                </div>

                <div style="border-top: 1px solid var(--admin-border); padding-top: 10px;">
                    <label class="detail-label">Vecino Solicitante</label>
                    <p class="detail-value" style="font-weight: 500;">${claim.vecino}</p>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--admin-text-secondary);">${claim.email}</p>
                </div>

                <div>
                    <label class="detail-label">Dirección y Especie</label>
                    <p class="detail-value" style="font-weight: 500;">${claim.direccion}</p>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--admin-text-secondary);">Especie: ${claim.especie}</p>
                </div>

                <div style="flex: 1; min-height: 80px; display: flex; flex-direction: column;">
                    <label class="detail-label">Mensaje del Reclamo</label>
                    <div class="detail-box" style="flex: 1; font-size: 0.85rem; overflow-y: auto; margin-top: 4px; line-height: 1.4;">
                        ${claim.descripcion}
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: GESTIÓN Y TRABAJOS -->
            <div class="claim-modal-col-right">
                <!-- Progress Tracker dots (Actualización visual temporal) -->
                <div class="status-tracker-container">
                    <div style="font-size: 0.8rem; font-weight: bold; color: var(--admin-text-primary);">Paso de Progreso a Asignar:</div>
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

                <!-- Listado de Trabajos Asignados (Debajo de las bolitas) -->
                <div class="assigned-jobs-section">
                    <h4 class="assigned-jobs-title" style="margin-top: 0; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <span>🔨 Trabajos y Ordenes Derivadas</span>
                        <span style="font-size: 0.8rem; background: var(--admin-accent); color: #fff; padding: 2px 6px; border-radius: 4px;">${jobs.length}</span>
                    </h4>
                    <div class="jobs-list-container">
                        ${jobs.length === 0 ? `
                            <div style="padding: 10px; text-align: center; color: var(--admin-text-secondary); font-size: 0.8rem;">
                                No se han derivado órdenes de trabajo para este reclamo.
                            </div>
                        ` : jobs.map(j => `
                            <div class="job-item-card" style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span class="job-item-card-title">${j.task_description}</span>
                                    <div class="job-item-card-company">Empresa: ${j.company ? (j.company.name || j.company.company_name) : (j.company_name || 'Sin especificar')}</div>
                                </div>
                                <span class="badge-status" style="font-size: 0.75rem; padding: 2px 6px; background-color: ${getJobStatusColor(j.work_status)}20; color: ${getJobStatusColor(j.work_status)}; border: 1px solid ${getJobStatusColor(j.work_status)};">${j.work_status}</span>
                            </div>
                        `).join('')}
                    </div>

                    <!-- Mostrar asignación solo cuando el estado temporal es Relevado/Inspeccionado (Sequence 2) -->
                    ${state.tempSelectedStatus === 'relevated' ? `
                    <div style="border-top: 1px solid var(--admin-border); padding-top: 12px; margin-top: 8px;">
                        <span style="font-size: 0.8rem; font-weight: bold; color: var(--admin-text-primary); display: block; margin-bottom: 6px;">Derivar Nuevo Trabajo Técnico:</span>
                        <div class="company-search-box">
                            <div style="width: 100%;">
                                <select id="assign-company-select" class="company-dropdown-select" onchange="updateTasksDropdown()" style="width: 100%;">
                                    <option value="">-- Seleccionar Empresa --</option>
                                    ${(state.activeCompanies || []).map(c => `
                                        <option value="${c.id}">${c.name || c.company_name}</option>
                                    `).join('')}
                                </select>
                            </div>
                        </div>
                        <div class="company-search-box" style="margin-top: 8px;">
                            <div>
                                <select id="assign-task-select" class="company-dropdown-select">
                                    <option value="">-- Seleccionar Tarea --</option>
                                </select>
                            </div>
                            <div>
                                <button class="btn-primary" onclick="createWorkOrderJob(${claim.id})" style="width: 100%; padding: 8px 12px; font-size: 0.85rem; height: 36px; display: flex; align-items: center; justify-content: center; gap: 4px;">
                                    ➕ Agregar Trabajo
                                </button>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>

                <!-- Sección de Respuestas y Acciones -->
                <div style="border-top: 1px solid var(--admin-border); padding-top: 10px;">
                    <div style="margin-bottom: 10px;">
                        <label style="font-size: 0.85rem; font-weight: bold; color: var(--admin-text-primary); display: block; margin-bottom: 4px;">Cambiar estado a:</label>
                        <select id="new-status-select" class="company-dropdown-select" onchange="selectTempStatus(this.value)" style="width: 100%;">
                            ${state.requestStatuses.map(s => `
                                <option value="${s.slug}" ${state.tempSelectedStatus === s.slug ? 'selected' : ''}>${s.status_name}</option>
                            `).join('')}
                        </select>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 0.85rem; font-weight: bold; color: var(--admin-text-primary);">Mensaje / Respuesta (Opcional):</span>
                        <div class="template-selector" style="display: flex; gap: 4px;">
                            <button class="template-btn" onclick="applyTemplate('info')" style="font-size: 0.7rem; padding: 2px 6px;">Info</button>
                            <button class="template-btn" onclick="applyTemplate('relevated')" style="font-size: 0.7rem; padding: 2px 6px;">Inspección</button>
                            <button class="template-btn" onclick="applyTemplate('scheduled')" style="font-size: 0.7rem; padding: 2px 6px;">Poda</button>
                            <button class="template-btn" onclick="applyTemplate('resolved')" style="font-size: 0.7rem; padding: 2px 6px;">Resolución</button>
                        </div>
                    </div>
                    <textarea id="response-text" class="response-textarea" style="width: 100%; height: 60px; min-height: 60px; font-size: 0.85rem; padding: 8px; border-radius: 8px; margin-bottom: 8px;" placeholder="Escribe un correo de respuesta al vecino (Si se deja vacío, solo se actualizará el estado interno)...">${claim.respuesta_admin || ''}</textarea>
                    
                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                        <!-- Botón de vincular manual si aplica -->
                        ${state.tempSelectedStatus === 'vinculated' ? `<button class="btn-secondary" onclick="setClaimStatus('vinculated')" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 8px;">🔗 Vincular a ID</button>` : ''}
                        
                        <!-- Botón único inteligente -->
                        <button class="btn-primary" onclick="smartUpdateClaim(${numericId})" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 8px;">Guardar y Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Centrar mapa en el árbol del reclamo seleccionado
    if (claimsMapInstance) {
        let lat = claim.lat || claim.latitude;
        let lng = claim.lng || claim.longitude;
        if (!lat || !lng) {
            const numId = parseInt(claim.id.replace(/\D/g, '')) || 1;
            lat = -34.5700 - (numId % 20) * 0.0015;
            lng = -58.4500 - (numId % 15) * 0.0012;
        }
        claimsMapInstance.setView([lat, lng], 16);
    }

    modal.style.display = 'flex';
    modal.classList.add('active');
}

function isActiveStatus(slug) {
    return state.tempSelectedStatus === slug;
}

function getJobStatusColor(status) {
    if (status === 'En Proceso') return '#e67e22';
    if (status === 'Finalizado') return '#22c55e';
    if (status === 'En espera') return '#95a5a6';
    return '#3498db';
}

// Seleccionar un estado temporalmente en las bolitas
window.selectTempStatus = function(slug) {
    state.tempSelectedStatus = slug;
    // Volver a renderizar el modal para actualizar el progreso visual y la visibilidad de derivación
    selectClaim(state.selectedClaimId);
};

// Actualizar las tareas que la empresa realiza en el segundo desplegable
window.updateTasksDropdown = function() {
    const companyId = document.getElementById('assign-company-select').value;
    const select = document.getElementById('assign-task-select');
    if (!select) return;

    select.innerHTML = '<option value="">-- Seleccionar Tarea --</option>';

    if (!companyId) return;

    const company = (state.activeCompanies || []).find(c => c.id === parseInt(companyId));
    let tasks = [];

    if (company && company.job_roles) {
        tasks = company.job_roles.map(r => r.job_role);
    } else if (companyCapabilities[companyId]) {
        tasks = companyCapabilities[companyId];
    }

    tasks.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t;
        opt.textContent = t;
        select.appendChild(opt);
    });
};

// Crear nueva orden de trabajo asignando empresa y tarea (POST /work-orders)
window.createWorkOrderJob = async function(claimId) {
    const companyId = document.getElementById('assign-company-select').value;
    const task = document.getElementById('assign-task-select').value;

    if (!companyId || !task) {
        alert('Por favor selecciona una empresa y el tipo de tarea que realiza.');
        return;
    }

    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    const numericId = parseInt(claim.id.replace(/\D/g, '')) || claim.id;

    // Calcular el orden de ejecución basado en los trabajos que ya tiene
    const currentJobsCount = claim.work_orders ? claim.work_orders.length : 0;
    const executionOrder = currentJobsCount + 1;

    try {
        const response = await fetch('/work-orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                request_id: numericId,
                company_id: parseInt(companyId),
                task_description: task,
                execution_order: executionOrder,
                scheduled_date: new Date().toISOString().split('T')[0] // Por defecto fecha de hoy
            })
        });

        if (response.ok) {
            showNotification(`Orden de trabajo "${task}" agregada con éxito.`);
            
            // Recargar datos para reflejar el nuevo trabajo asignado
            if (typeof window.loadWorkOrdersFromServer === 'function') {
                await window.loadWorkOrdersFromServer();
            }
            await loadClaimsFromServer();
            selectClaim(state.selectedClaimId);
        } else {
            const data = await response.json();
            alert('Error al guardar la orden de trabajo: ' + (data.message || 'Error del servidor'));
        }
    } catch (err) {
        console.error("Error al registrar orden de trabajo:", err);
        alert('Error de conexión al crear orden de trabajo.');
    }
};

// Función Inteligente para Actualizar (Si hay texto, envía mensaje. Si no, solo actualiza estado).
window.smartUpdateClaim = function(numericId) {
    const responseText = document.getElementById('response-text').value.trim();
    if (responseText === '') {
        updateClaimOnlyStatus(numericId);
    } else {
        sendResponseAndStatus(numericId);
    }
};

// Actualizar solo el estado del reclamo (PUT /requests/update-status/{id})
window.updateClaimOnlyStatus = async function(numericId) {
    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    if (!claim) return;

    const newStatus = state.tempSelectedStatus;

    // Regla: "cuando llega a relevado que no te deje avanzar sin una empresa"
    // Buscamos si tiene algún trabajo (orden de trabajo) creado
    const numericClaimId = parseInt(claim.id.replace(/\D/g, '')) || claim.id;
    const currentJobs = claim.work_orders || (state.workOrders || []).filter(w => w.request_id === numericClaimId);

    const statusObj = state.requestStatuses.find(rs => rs.slug === newStatus);
    const newSeq = statusObj ? statusObj.sequence : null;

    // Si intenta pasar de relevado (sequence > 2) y no hay trabajos/empresas asignadas
    if (newSeq && newSeq > 2 && currentJobs.length === 0) {
        alert('⚠️ No se puede avanzar del estado Relevado/Inspeccionado sin haber derivado un trabajo a una empresa contratista.');
        return;
    }

    try {
        const payload = { estado: newStatus };
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
            showNotification(`Estado del reclamo #${state.selectedClaimId} cambiado a: ${newStatus}`);
            await loadClaimsFromServer();
            selectClaim(state.selectedClaimId);
        } else {
            alert('Error al actualizar el estado.');
        }
    } catch (err) {
        console.error("Error al actualizar estado:", err);
    }
};

// Enviar respuesta al vecino y actualizar el estado
window.sendResponseAndStatus = async function(numericId) {
    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    if (!claim) return;

    const responseText = document.getElementById('response-text').value;
    if (!responseText.trim()) {
        alert('Por favor escribe un mensaje de respuesta antes de enviar.');
        return;
    }

    const newStatus = state.tempSelectedStatus;

    // Validar regla de empresa si el estado supera Relevado
    const numericClaimId = parseInt(claim.id.replace(/\D/g, '')) || claim.id;
    const currentJobs = claim.work_orders || (state.workOrders || []).filter(w => w.request_id === numericClaimId);
    const statusObj = state.requestStatuses.find(rs => rs.slug === newStatus);
    const newSeq = statusObj ? statusObj.sequence : null;

    if (newSeq && newSeq > 2 && currentJobs.length === 0) {
        alert('⚠️ No se puede avanzar del estado Relevado/Inspeccionado sin haber derivado un trabajo a una empresa contratista.');
        return;
    }

    try {
        const response = await fetch(`/requests/update-status/${state.selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                respuesta: responseText,
                estado: newStatus
            })
        });

        if (response.ok) {
            claim.respuesta_admin = responseText;
            claim.estado = newStatus;
            showNotification(`Respuesta enviada y estado del reclamo #${state.selectedClaimId} cambiado a: ${newStatus}`);
            await loadClaimsFromServer();
            selectClaim(state.selectedClaimId);
            clearResponse();
        } else {
            alert('Error al enviar la respuesta.');
        }
    } catch (err) {
        console.error("Error al enviar respuesta:", err);
    }
};

export function applyTemplate (type) {
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
}

export function clearResponse () {
    const textarea = document.getElementById('response-text');
    if (textarea) textarea.value = '';
}

export async function sendResponse () {
    // Mantener compatibilidad anterior
    sendResponseAndStatus(state.selectedClaimId);
}

export function filterClaims () {
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
}

export async function setClaimStatus (newStatus) {
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
                claim.linked_to = null;
            }

            claim.suggested_duplicate_id = null;
            state.tempSelectedStatus = newStatus;

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
}

export async function resolveDuplicate (isAccepted, duplicateId = null) {
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
                state.tempSelectedStatus = 'vinculated';
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
}

export async function loadStatusesFromServer () {
    try {
        const response = await fetch('/api/request-statuses');
        if (response.ok) {
            const result = await response.json();
            state.requestStatuses = result.data;
        }
    } catch (err) {
        console.error("Error al cargar estados:", err);
    }
}

export async function loadClaimsFromServer () {
    await loadActiveCompanies();
    if (state.requestStatuses.length === 0) {
        await loadStatusesFromServer();
    }

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
}

export async function loadActiveCompanies () {
    try {
        const response = await fetch('/api/admin/companies');
        if (response.ok) {
            const data = await response.json();
            state.activeCompanies = (data.data || []).filter(c => c.status === 'Activo' || c.status === undefined);
        }
    } catch (err) {
        console.error("Error al cargar empresas activas:", err);
        // Fallback local con empresas reales para pruebas si no responde el endpoint
        state.activeCompanies = [
            { id: 1, name: 'Arboricultura BA', company_name: 'Arboricultura BA' },
            { id: 2, name: 'Verde Urbano Mantenimiento', company_name: 'Verde Urbano Mantenimiento' }
        ];
    }
}
