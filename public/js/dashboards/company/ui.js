/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { getCompanyJobs, getTenders } from './main.js';

export function updateCompanyStats() {
    const jobs = getCompanyJobs();
    const tenders = getTenders();

    const completedCount = jobs.filter(j => j.work_status === 'Finalizado').length;
    const pendingCount = jobs.filter(j => j.work_status !== 'Finalizado').length;
    const availableTendersCount = tenders.filter(t => !t.applied).length;

    const elCompleted = document.getElementById('company-stat-completed');
    const elPending = document.getElementById('company-stat-pending');
    const elTenders = document.getElementById('company-stat-tenders');

    if (elCompleted) elCompleted.innerText = completedCount;
    if (elPending) elPending.innerText = pendingCount;
    if (elTenders) elTenders.innerText = availableTendersCount;
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
