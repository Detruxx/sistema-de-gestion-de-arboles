/**
 * Componente (Dashboard Empresa): Lógica para visualizar el estado de facturación y pagos de los servicios realizados.
 */

import { getCompanyJobs } from './main.js';

let selectedPaymentId = null;

window.selectCompanyPayment = function(id) {
    selectedPaymentId = id;
    loadCompanyPaymentsList();
    renderPaymentDetail(id);
};

export function loadCompanyPaymentsList() {
    const container = document.getElementById('company-payments-list-container');
    if (!container) return;
    container.innerHTML = '';

    const jobs = getCompanyJobs();
    const completed = jobs.filter(j => j.work_status === 'Finalizado');

    if (completed.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No tienes cobros o servicios finalizados registrados.</p></div>';
        return;
    }

    completed.forEach(j => {
        const card = document.createElement('div');
        card.className = `list-item-card ${selectedPaymentId === j.id ? 'active' : ''}`;
        card.onclick = () => window.selectCompanyPayment(j.id);

        const isPaid = j.payment_status === 'Pagado';
        const badgeColor = isPaid ? '#22c55e' : '#ef4444';
        const badgeLabel = isPaid ? 'VERIFICADO PAGADO' : 'PENDIENTE DE CERTIFICACIÓN';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Cobro #${j.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor};">${badgeLabel}</span>
            </div>
            <div class="list-item-title">${j.task_description}</div>
            <div class="list-item-subtitle" style="color: var(--admin-accent); font-weight: bold;">$${j.cost || '45,000'}</div>
        `;
        container.appendChild(card);
    });
}

function renderPaymentDetail(id) {
    const jobs = getCompanyJobs();
    const j = jobs.find(item => item.id === id);
    const panel = document.getElementById('company-payment-detail-panel');
    if (!j || !panel) return;

    const isPaid = j.payment_status === 'Pagado';

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">Estado de Pago Comunal</h3>
                <p class="detail-subtitle">Servicio de Contratación #${j.id}</p>
            </div>
            <span class="badge-status" style="background-color: ${isPaid ? '#22c55e' : '#ef4444'}20; color: ${isPaid ? '#22c55e' : '#ef4444'}; border: 1px solid ${isPaid ? '#22c55e' : '#ef4444'}; font-size: 1rem; padding: 6px 12px;">${isPaid ? 'PAGADO' : 'PENDIENTE DE REGISTRO'}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Monto Liquidado</p>
            <p class="detail-value" style="font-size: 1.4rem; color: var(--admin-accent); font-weight: bold;">$${j.cost || '45,000'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Servicio Ejecutado</p>
            <p class="detail-value">${j.task_description}</p>
        </div>

        <div class="detail-box" style="margin-top: 25px; border-left: 4px solid ${isPaid ? '#22c55e' : '#ef4444'}; padding: 15px; background: rgba(0,0,0,0.02);">
            <p style="margin: 0; font-size: 0.95rem; color: var(--admin-text-primary);">
                ${isPaid ? 'El pago ha sido verificado e ingresado a la cuenta bancaria de tu empresa por la Tesorería de Espacio Público.' : 'El Municipio está procesando la certificación técnica de la obra. El inspector verificará el pago a la brevedad.'}
            </p>
        </div>
    `;
}
