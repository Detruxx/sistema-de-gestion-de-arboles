/**
 * UI (Dashboard Empresa): Manipulación del DOM, modales y renderizado de la lista.
 */

import { getJobListCardHtml, getJobModalHtml } from './jobs-template.js';
import { updateCompanyMapMarkers } from './jobs-map.js';

let stateRef = null;

export function initJobsUI(stateObj) {
    stateRef = stateObj;
    renderJobsList();
}

export function renderJobsList() {
    const container = document.getElementById('company-jobs-list-container');
    if (!container || !stateRef) return;

    container.innerHTML = '';
    
    // Solo mostrar trabajos no finalizados
    const activeJobs = stateRef.jobs.filter(j => j.work_status !== 'Finalizado');

    if (activeJobs.length === 0) {
        container.innerHTML = `<div class="empty-state-panel">No tienes trabajos activos asignados.</div>`;
        return;
    }

    activeJobs.forEach(job => {
        const item = document.createElement('div');
        item.className = `list-item-card ${stateRef.selectedJobId == job.id ? 'active' : ''}`;
        
        item.innerHTML = getJobListCardHtml(job, stateRef.selectedJobId == job.id);
        
        item.onclick = () => {
            if (typeof window.selectJob === 'function') {
                window.selectJob(job.id);
            }
        };
        container.appendChild(item);
    });

    updateCompanyMapMarkers();
}

export function openJobDetailModal(job) {
    const modal = document.getElementById('company-job-modal');
    const body = document.getElementById('company-job-modal-body');
    if (!modal || !body) return;

    body.innerHTML = getJobModalHtml(job);
    modal.style.display = 'flex';
}

export function closeJobDetailModal() {
    const modal = document.getElementById('company-job-modal');
    if (modal) {
        modal.style.display = 'none';
        
        // Al cerrar el modal, deseleccionamos visualmente en la lista (opcional, pero limpio)
        stateRef.selectedJobId = null;
        renderJobsList();
    }
}
