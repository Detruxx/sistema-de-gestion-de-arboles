/**
 * Actions (Dashboard Empresa): Lógica de negocio y eventos para modificar estado de trabajos.
 */

import { putJobStatus, fetchCompanyData } from './api.js';
import { renderJobsList, closeJobDetailModal, openJobDetailModal } from './jobs-ui.js';
import { updateCompanyStats } from './ui.js';

let stateRef = null;

export function initJobsActions(stateObj) {
    stateRef = stateObj;
}

export async function updateJobStatus(jobId, newStatus) {
    try {
        const success = await putJobStatus(jobId, newStatus);

        if (success) {
            // Actualizar estado local
            const jobIndex = stateRef.jobs.findIndex(j => j.id === jobId);
            let updatedJob = null;
            if (jobIndex > -1) {
                stateRef.jobs[jobIndex].work_status = newStatus;
                updatedJob = stateRef.jobs[jobIndex];
            }

            // Mostrar notificación
            const banner = document.getElementById('notification-banner');
            const text = document.getElementById('notification-text');
            if (banner && text) {
                text.textContent = 'Estado del trabajo actualizado correctamente.';
                banner.style.display = 'flex';
                setTimeout(() => { banner.style.display = 'none'; }, 4000);
            }

            // Refrescar lista
            renderJobsList();

            // Refrescar el modal abierto en lugar de cerrarlo
            if (updatedJob) {
                openJobDetailModal(updatedJob);
            }

            // Actualizar stats (en main.js o donde corresponda)
            if (typeof updateCompanyStats === 'function') {
                updateCompanyStats();
            }

            // Si se finalizó y estamos en "pagos" o "trabajos", refrescar
            if (newStatus === 'Finalizado' && typeof window.loadCompanyPaymentsList === 'function') {
                window.loadCompanyPaymentsList();
            }
        }
    } catch (error) {
        console.error('Error al actualizar trabajo:', error);
        alert(error.message || 'Hubo un error al actualizar el trabajo.');
    }
}

window.updateJobSchedule = async function (id) {
    const dateInput = document.getElementById(`job-schedule-date-${id}`);
    if (!dateInput || !dateInput.value) {
        alert('Por favor selecciona una fecha válida.');
        return;
    }

    try {
        const success = await putJobStatus(id, 'Asignado', dateInput.value);

        if (success) {
            // Actualizar estado local y la fecha programada
            const jobIndex = stateRef.jobs.findIndex(j => j.id === id);
            let updatedJob = null;
            if (jobIndex > -1) {
                stateRef.jobs[jobIndex].work_status = 'Asignado';
                stateRef.jobs[jobIndex].scheduled_date = dateInput.value;
                updatedJob = stateRef.jobs[jobIndex];
            }

            // Mostrar notificación
            const banner = document.getElementById('notification-banner');
            const text = document.getElementById('notification-text');
            if (banner && text) {
                text.textContent = 'Fecha programada del trabajo actualizada correctamente.';
                banner.style.display = 'flex';
                setTimeout(() => { banner.style.display = 'none'; }, 4000);
            }

            // Refrescar lista y modal
            renderJobsList();
            if (updatedJob) {
                openJobDetailModal(updatedJob);
            }

            // Actualizar stats si corresponde
            if (typeof updateCompanyStats === 'function') {
                updateCompanyStats();
            }
        }
    } catch (error) {
        console.error('Error al programar trabajo:', error);
        alert(error.message || 'Hubo un error al guardar la fecha programada.');
    }
}
