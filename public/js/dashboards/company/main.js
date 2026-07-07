/**
 * Principal (Dashboard Empresa): Punto de entrada y configuración general para el panel de la empresa contratista.
 */

import { fetchCompanyData } from './api.js';
import { updateCompanyStats } from './ui.js';
import { loadCompanyJobsList } from './jobs.js';
import { loadCompanyPaymentsList } from './payments.js';
import { showModule, toggleAdminSidebar } from '../shared/layout.js';

window.showModule = showModule;
window.toggleAdminSidebar = toggleAdminSidebar;

let companyJobs = [];

export function getCompanyJobs() { return companyJobs; }
export function setCompanyJobs(val) { companyJobs = val; }

async function loadCompanyData() {
    try {
        const data = await fetchCompanyData();
        setCompanyJobs(data.jobs || []);
    } catch (err) {
        console.error("Error al cargar datos de empresa:", err);
        setCompanyJobs([]);
    }

    loadCompanyJobsList();
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
