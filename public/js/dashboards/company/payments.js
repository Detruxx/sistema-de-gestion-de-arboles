/**
 * Componente (Dashboard Empresa): Lógica para visualizar el estado de facturación y pagos de los servicios realizados.
 */

import { getCompanyJobs } from './main.js';
import { putPaymentStatus } from './api.js';

window.closeCompanyPaymentModal = function () {
    document.getElementById('company-payment-modal').style.display = 'none';
};

window.openCompanyPaymentModal = function (id) {
    const jobs = getCompanyJobs();
    const j = jobs.find(item => item.id === id);
    if (!j) return;

    const modalBody = document.getElementById('company-payment-modal-body');
    const isPaid = j.payment_status === 'Pagado';
    const isCertified = j.payment_status === 'Apto para Cobro';

    let statusLabel = 'En proceso de revisión por la comuna';
    if (isCertified) statusLabel = 'Certificado - Apto para emitir pago';
    if (isPaid) statusLabel = 'Aprobado y Transferido';

    modalBody.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">Estado de Pago Comunal</h3>
                <p class="detail-subtitle">Servicio de Contratación #${j.id}</p>
            </div>
            <span class="badge-status" style="background-color: ${isPaid ? '#0d9488' : (isCertified ? '#22c55e' : '#eab308')}20; color: ${isPaid ? '#0d9488' : (isCertified ? '#22c55e' : '#eab308')}; border: 1px solid ${isPaid ? '#0d9488' : (isCertified ? '#22c55e' : '#eab308')}; font-size: 1rem; padding: 6px 12px;">
                ${isPaid ? 'PAGADO' : (isCertified ? 'LISTO PARA COBRO' : 'PENDIENTE')}
            </span>
        </div>

        <div class="detail-section" style="margin-top: 15px;">
            <p class="detail-label">Estado de la Certificación</p>
            <p class="detail-value" style="font-size: 1.2rem; color: var(--admin-text-primary); font-weight: bold;">
                ${statusLabel}
            </p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Servicio Ejecutado</p>
            <p class="detail-value">${j.task_description}</p>
        </div>

        <div class="detail-box" style="margin-top: 25px; border-left: 4px solid ${isPaid ? '#0d9488' : (isCertified ? '#22c55e' : '#eab308')}; padding: 15px; background: rgba(0,0,0,0.02);">
            <p style="margin: 0; font-size: 0.95rem; color: var(--admin-text-primary);">
                ${isPaid ? 'El pago ha sido verificado e ingresado a la cuenta bancaria de tu empresa por la Tesorería de la Comuna 13.' : (isCertified ? 'La certificación fue aprobada. Ya puedes registrar el ingreso del pago si la transferencia se hizo efectiva.' : 'La Comuna está procesando la certificación técnica de la obra. El inspector verificará el pago a la brevedad.')}
            </p>
        </div>

        ${isCertified ? `
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button class="btn-primary" onclick="window.markPaymentAsPaid(${j.id})" style="background-color: #0d9488; border-color: #0f766e; padding: 10px 16px; font-size: 0.95rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: text-bottom; margin-right: 6px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Registrar Cobro como Verificado Pagado
                </button>
            </div>
        ` : ''}
    `;

    document.getElementById('company-payment-modal').style.display = 'flex';
};

window.markPaymentAsPaid = async function (id) {
    if (!confirm('¿Estás seguro que deseas marcar este servicio como Verificado Pagado? Esto asume que la transferencia ya fue realizada al/los trabajador/es.')) return;

    try {
        await putPaymentStatus(id, 'Pagado');
        const jobs = getCompanyJobs();
        const j = jobs.find(item => item.id === id);
        if (j) j.payment_status = 'Pagado';

        window.closeCompanyPaymentModal();
        loadCompanyPaymentsList();
    } catch (e) {
        alert(e.message || 'Error al actualizar el estado de pago');
    }
};

export function loadCompanyPaymentsList() {
    const container = document.getElementById('company-payments-list-container');
    if (!container) return;
    container.innerHTML = '';

    const jobs = getCompanyJobs();
    const completed = jobs.filter(j => j.work_status === 'Finalizado');

    if (completed.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px; grid-column: 1 / -1;"><p>No tienes cobros o servicios finalizados registrados.</p></div>';
        return;
    }

    completed.forEach(j => {
        const card = document.createElement('div');
        card.className = `list-item-card`;
        card.onclick = () => window.openCompanyPaymentModal(j.id);

        const isPaid = j.payment_status === 'Pagado';
        const isCertified = j.payment_status === 'Apto para Cobro';

        let badgeColor = '#eab308'; // Amarillo
        let badgeLabel = 'PENDIENTE DE CERTIFICACIÓN';
        if (isPaid) {
            badgeColor = '#0d9488'; // Teal (pagado)
            badgeLabel = 'VERIFICADO PAGADO';
        } else if (isCertified) {
            badgeColor = '#22c55e'; // Verde
            badgeLabel = 'CERTIFICADO - LISTO PARA COBRO';
        }

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Cobro #${j.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor};">${badgeLabel}</span>
            </div>
            <div class="list-item-title">${j.task_description}</div>
            <div class="list-item-subtitle" style="margin-top: 8px;">Reclamo Original: ${j.request ? j.request.id : 'N/A'}</div>
        `;
        container.appendChild(card);
    });
}

// Bind to window so jobs-actions.js can refresh it automatically when a job is marked Finalizado
window.loadCompanyPaymentsList = loadCompanyPaymentsList;
