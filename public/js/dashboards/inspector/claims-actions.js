/**
 * Actions (Dashboard Inspector): Funciones de lógica de negocio, actualización de estados y eventos de botones.
 */

import { state } from './state.js';
import { getCsrfToken } from '../shared/layout.js';
import { updateStats } from './ui.js';

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

    const statusSelect = document.getElementById('new-status-select');
    if (statusSelect) {
        let statusValue = type;
        if (type === 'info') statusValue = 'open';
        if (type === 'scheduled') statusValue = 'scheduled';

        const options = Array.from(statusSelect.options).map(o => o.value);
        if (options.includes(statusValue)) {
            statusSelect.value = statusValue;
        }
    }
}

export function clearResponse() {
    const textarea = document.getElementById('response-text');
    if (textarea) textarea.value = '';
}

export async function smartUpdateClaim() {
    const claim = state.claims.find(c => c.id === state.selectedClaimId);
    if (!claim) return;

    const responseText = document.getElementById('response-text').value.trim();
    const newStatus = document.getElementById('new-status-select').value;
    const newPriorityId = document.getElementById('new-priority-select')?.value;

    const currentStatusSlug = claim.estado;
    // Bloqueamos el avance o retroceso del estado segun ciertos momentos del reclamo
    if (currentStatusSlug === 'scheduled' || currentStatusSlug === 'in_progress') {
        if (newStatus !== currentStatusSlug) {
            alert('No puedes avanzar o retroceder el estado de un reclamo que está Programado o En Proceso. La contratista actualizará el estado al trabajar.');
            return;
        }
    }

    if (currentStatusSlug === 'resolved') {
        if (newStatus !== currentStatusSlug && newStatus !== 'certified') {
            alert('Un reclamo Completado solo puede avanzar al estado Certificado.');
            return;
        }
    }

    // Validar asignación de trabajos si se pasa a programado o superior
    const newStatusObj = state.requestStatuses.find(rs => rs.slug === newStatus);
    const scheduledObj = state.requestStatuses.find(rs => rs.slug === 'scheduled');

    if (newStatusObj && scheduledObj && newStatusObj.sequence >= scheduledObj.sequence) {
        const totalWorkOrders = (claim.work_orders ? claim.work_orders.length : 0) + (claim.pending_work_orders ? claim.pending_work_orders.length : 0);
        if (totalWorkOrders === 0) {
            alert('Para pasar del estado Relevado a Programado (o superior), es obligatorio asignar al menos un trabajo (orden de trabajo).');
            return;
        }
    }

    let payload = { estado: newStatus };
    if (responseText !== '') {
        payload.respuesta = responseText;
    }
    if (newPriorityId) {
        payload.priority_name = newPriorityId;
    }

    if (newStatus === 'vinculated') {
        const manualId = prompt('Ingrese el ID numérico del reclamo original al que desea vincularlo (Ej: 18):');
        if (!manualId) return; // Canceló
        payload.linked_to = parseInt(manualId);
    }

    let workOrdersCreated = false;
    if (claim.pending_work_orders && claim.pending_work_orders.length > 0) {
        for (const wo of claim.pending_work_orders) {
            try {
                const wores = await fetch('/work-orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        request_id: wo.request_id,
                        company_id: wo.company_id,
                        task_description: wo.task_description,
                        execution_order: wo.execution_order
                    })
                });
                if (wores.ok) workOrdersCreated = true;
            } catch (e) {
                console.error("Error creating work order:", e);
            }
        }
        claim.pending_work_orders = [];
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
            const statusFriendlyName = state.requestStatuses.find(rs => rs.slug === newStatus)?.status_name || newStatus;
            if (text) text.innerText = `El estado se actualizó a '${statusFriendlyName}' y los cambios fueron guardados.`;
            if (banner) banner.style.display = 'flex';

            setTimeout(() => {
                if (banner) banner.style.display = 'none';
            }, 5000);

            if (workOrdersCreated) {
                if (typeof window.loadClaimsFromServer === 'function') {
                    await window.loadClaimsFromServer();
                }
            }

            window.selectClaim(state.selectedClaimId); // Re-render
            if (typeof updateStats === 'function') updateStats();
            window.loadClaimsList();
        } else {
            alert('Error al actualizar el estado/respuesta en el servidor.');
        }
    } catch (err) {
        console.error("Error updating claim:", err);
        alert('Error de conexión.');
    }
}

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
                claim.linked_to = null;
            }
            claim.suggested_duplicate_id = null;

            window.selectClaim(state.selectedClaimId);
            updateStats();
            applyTemplate(newStatus);
            window.loadClaimsList();
        } else {
            alert('Error al actualizar el estado en el servidor.');
        }
    } catch (err) {
        console.error("Error al actualizar estado:", err);
    }
}

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

            window.selectClaim(state.selectedClaimId);
            updateStats();
            window.loadClaimsList();
        } else {
            alert('Error al actualizar la sugerencia.');
        }
    } catch (err) {
        console.error(err);
    }
}

export function queueWorkOrder(numericClaimId) {
    const taskDescription = document.getElementById('new-task-type-select').value;
    const companySelect = document.getElementById('new-task-company-select');
    const companyId = companySelect.value;

    if (!taskDescription) {
        alert('Por favor selecciona un tipo de tarea.');
        return;
    }
    if (!companyId) {
        alert('Por favor selecciona una empresa contratista.');
        return;
    }

    const companyName = companySelect.options[companySelect.selectedIndex].text;
    const claim = state.claims.find(c => c.raw_request_id == numericClaimId || c.id == numericClaimId);

    if (!claim.pending_work_orders) {
        claim.pending_work_orders = [];
    }

    const currentOrder = (claim.work_orders ? claim.work_orders.length : 0) + claim.pending_work_orders.length + 1;

    claim.pending_work_orders.push({
        request_id: numericClaimId,
        company_id: companyId,
        company: companyName,
        task_description: taskDescription,
        execution_order: currentOrder,
        status: 'Pendiente de Guardar'
    });

    window.selectClaim(state.selectedClaimId);
}

export function createWorkOrder(numericClaimId) {
    alert('Por favor utiliza el botón Asignar Trabajo y luego Guardar y Actualizar Estado.');
}
