/**
 * Principal (Dashboard Empresa): Punto de entrada y configuración general para el panel de la empresa contratista.
 */

import { fetchCompanyData } from './api.js';
import { updateCompanyStats } from './ui.js';
import { initJobs, reloadJobsList, state as jobsState } from './jobs.js';
import { loadCompanyPaymentsList } from './payments.js';
import { showModule, toggleAdminSidebar, getCsrfToken } from '../shared/layout.js';

window.showModule = (moduleName) => {
    showModule(moduleName);
    if (moduleName === 'trabajos') {
        import('./jobs.js').then(module => {
            if (typeof module.triggerMapResize === 'function') {
                module.triggerMapResize();
            }
        });
    }
};
window.toggleAdminSidebar = toggleAdminSidebar;
window.getCsrfToken = getCsrfToken;

export function getCompanyJobs() { return jobsState.jobs; }

async function loadCompanyData() {
    let jobs = [];
    try {
        const data = await fetchCompanyData();
        jobs = data.jobs || [];
    } catch (err) {
        console.error("Error al cargar datos de empresa:", err);
    }

    initJobs(jobs);
    loadCompanyPaymentsList();
    updateCompanyStats();
}

document.addEventListener('DOMContentLoaded', () => {
    loadCompanyData();
    handleHashRouting();
});

window.addEventListener('hashchange', handleHashRouting);

function handleHashRouting() {
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        if (['resumen', 'trabajos', 'pagos'].includes(hash)) {
            window.showModule(hash);
        }
    }
}
