/**
 * Interfaz (Dashboard Empresa): Lógica de manipulación del DOM y renderizado de la interfaz para la empresa.
 */

import { getCompanyJobs } from './main.js';

export function updateCompanyStats() {
    const jobs = getCompanyJobs();

    const completedCount = jobs.filter(j => j.work_status === 'Finalizado').length;
    const pendingCount = jobs.filter(j => j.work_status !== 'Finalizado').length;
    const unpaidCount = jobs.filter(j => j.work_status === 'Finalizado' && j.payment_status !== 'Pagado').length;

    const elCompleted = document.getElementById('company-stat-completed');
    const elPending = document.getElementById('company-stat-pending');
    const elUnpaid = document.getElementById('company-stat-unpaid');

    if (elCompleted) elCompleted.innerText = completedCount;
    if (elPending) elPending.innerText = pendingCount;
    if (elUnpaid) elUnpaid.innerText = unpaidCount;
}

export function showNotification(text) {
    const banner = document.getElementById('notification-banner');
    const label = document.getElementById('notification-text');
    if (banner && label) {
        label.innerText = text;
        banner.style.display = 'flex';
        setTimeout(() => {
            banner.style.display = 'none';
        }, 4000);
    }
}
