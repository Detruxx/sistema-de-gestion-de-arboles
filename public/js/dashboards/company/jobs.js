/**
 * Orquestador (Dashboard Empresa): Une componentes de UI, Mapa y Acciones.
 */

import { initJobsUI, renderJobsList, openJobDetailModal, closeJobDetailModal } from './jobs-ui.js';
import { initCompanyMap } from './jobs-map.js';
import { initJobsActions, updateJobStatus } from './jobs-actions.js';

export const state = {
    jobs: [],
    selectedJobId: null
};

export function initJobs(jobsArray) {
    state.jobs = jobsArray;
    
    // Inicializar sub-módulos inyectando el estado
    initJobsUI(state);
    initCompanyMap(state);
    initJobsActions(state);
    
    // Funciones globales requeridas por el HTML
    window.selectJob = (id) => {
        state.selectedJobId = id;
        const job = state.jobs.find(j => j.id == id);
        if (job) {
            openJobDetailModal(job);
            renderJobsList(); // Repintar lista para marcar como 'active'
        }
    };
    
    window.closeCompanyJobModal = () => {
        closeJobDetailModal();
    };

    window.updateJobStatus = updateJobStatus;
}

export function reloadJobsList() {
    renderJobsList();
}

export { triggerCompanyMapResize as triggerMapResize } from './jobs-map.js';
