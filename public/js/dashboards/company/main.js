/**
 * Principal (Dashboard Empresa): Punto de entrada y configuración general para el panel de la empresa contratista.
 */

import { fetchCompanyData } from './api.js';
import { updateCompanyStats } from './ui.js';
import { loadCompanyJobsList } from './jobs.js';
import { loadTendersList } from './tenders.js';
import { showModule, toggleAdminSidebar } from '../shared/layout.js';

window.showModule = showModule;
window.toggleAdminSidebar = toggleAdminSidebar;

let companyJobs = [];
let tenders = [];

export function getCompanyJobs() { return companyJobs; }
export function setCompanyJobs(val) { companyJobs = val; }

export function getTenders() { return tenders; }
export function setTenders(val) { tenders = val; }

async function loadCompanyData() {
    try {
        const data = await fetchCompanyData();
        setCompanyJobs(data.jobs || []);
        setTenders(data.tenders || []);
    } catch (err) {
        console.error("Error al cargar datos de empresa:", err);
        setCompanyJobs([]);
        setTenders([]);
    }

    loadCompanyJobsList();
    loadTendersList();
    updateCompanyStats();
}

document.addEventListener('DOMContentLoaded', () => {
    loadCompanyData();
});
