/**
 * Componente (Dashboard Inspector): Lógica para el seguimiento y gestión de pagos o certificaciones.
 */

import { state } from './state.js';
export function loadPaymentsList () {
    const container = document.getElementById('payments-list-container');
    if (!container) return;
    container.innerHTML = '';

    const completedWorks = state.workOrders.filter(w => w.work_status === 'Finalizado');

    if (completedWorks.length === 0) {
        container.innerHTML = `
            <div class="empty-state-panel" style="padding: 20px;">
                <p>No hay órdenes de trabajo finalizadas para procesar pagos.</p>
            </div>`;
        return;
    }

    completedWorks.forEach(w => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedWorkOrderId === w.id ? 'active' : ''}`;
        card.onclick = () => selectWorkOrderPayment(w.id);

        const isPaid = w.payment_status === 'Pagado';
        const badgeColor = isPaid ? '#22c55e' : '#ef4444';
        const badgeLabel = isPaid ? 'PAGADO' : 'IMPAGO';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Orden #${w.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor};">${badgeLabel}</span>
            </div>
            <div class="list-item-title">${w.task_description}</div>
            <div class="list-item-subtitle" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <span>Empresa: ${w.company ? w.company.company_name : 'No Asignada'}</span>
            </div>
        `;
        container.appendChild(card);
    });
};

export function selectWorkOrderPayment (id) {
    state.selectedWorkOrderId = id;
    loadPaymentsList();

    const w = state.workOrders.find(item => item.id === id);
    const panel = document.getElementById('payment-detail-panel');
    if (!w || !panel) return;

    const isPaid = w.payment_status === 'Pagado';
    const badgeColor = isPaid ? '#22c55e' : '#ef4444';
    const badgeLabel = isPaid ? 'PAGADO' : 'IMPAGO';

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">Certificación de Pago</h3>
                <p class="detail-subtitle">Orden de Trabajo #${w.id} | Finalizada</p>
            </div>
            <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor}; font-size: 1rem; padding: 6px 12px;">${badgeLabel}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Empresa Ejecutora</p>
            <p class="detail-value" style="font-size: 1.1rem; font-weight: bold; color: var(--admin-text-primary);">${w.company ? w.company.company_name : 'Mantenimiento Verde S.A.'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Detalle del Servicio Realizado</p>
            <p class="detail-value">${w.task_description}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Fecha de Programación / Ejecución</p>
            <p class="detail-value">${w.scheduled_date ? new Date(w.scheduled_date).toLocaleDateString() : 'N/A'}</p>
        </div>

        <div class="detail-box" style="margin-top: 25px; border-left: 4px solid var(--admin-accent); padding: 15px; background: rgba(45, 122, 79, 0.03);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.85rem; color: var(--admin-text-secondary); display: block; text-transform: uppercase;">Estado de Certificación de Pago</span>
                    <strong style="font-size: 1.2rem; color: var(--admin-text-primary); font-family: var(--font-display);">${isPaid ? 'Pago Certificado' : 'Pendiente de Certificar'}</strong>
                </div>
                <div>
                    <button class="btn-primary" onclick="togglePaymentStatus(${w.id})" style="background-color: ${isPaid ? '#ef4444' : '#22c55e'}; border-color: ${isPaid ? '#ef4444' : '#22c55e'}; display: flex; align-items: center; gap: 8px;">
                        <span>${isPaid ? '✖ Marcar como Impago' : '✔ Certificar Pago'}</span>
                    </button>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
            <h4 style="font-family: var(--font-display); color: var(--admin-accent); margin-top: 0;">Trámite de Origen</h4>
            <div style="background: var(--admin-bg-panel); border: 1px solid var(--admin-border); padding: 15px; border-radius: 10px; margin-top: 10px;">
                <p style="margin: 0 0 5px 0; font-size: 0.9rem;"><strong>ID Reclamo:</strong> ${w.request ? w.request.tracking_code || 'REC-2026-004' : 'REC-2026-004'}</p>
                <p style="margin: 0; font-size: 0.9rem; color: var(--admin-text-secondary);">El reclamo ha sido marcado automáticamente como <strong>Finalizado por la Empresa</strong>. Al certificar el pago, se notificará al vecino.</p>
            </div>
        </div>
    `;
};

export async function togglePaymentStatus (id) {
    const w = state.workOrders.find(item => item.id === id);
    if (!w) return;

    const newStatus = w.payment_status === 'Pagado' ? 'Pendiente' : 'Pagado';

    try {
        const response = await fetch(`/api/work-orders/${id}/payment`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ payment_status: newStatus })
        });

        w.payment_status = newStatus;
        selectWorkOrderPayment(id);
        loadPaymentsList();

        const banner = document.getElementById('notification-banner');
        const text = document.getElementById('notification-text');
        if (text) text.innerText = `Estado de pago de la Orden #${id} cambiado a: ${newStatus}`;
        if (banner) banner.style.display = 'flex';
        setTimeout(() => {
            if (banner) banner.style.display = 'none';
        }, 4000);

    } catch (err) {
        console.error("Error al actualizar pago:", err);
        w.payment_status = newStatus;
        selectWorkOrderPayment(id);
        loadPaymentsList();
    }
};

export async function loadWorkOrdersFromServer () {
    try {
        const response = await fetch('/api/work-orders');
        if (response.ok) {
            const result = await response.json();
            state.workOrders = result.data;
        } else {
            state.workOrders = [];
        }
    } catch (err) {
        console.error("Error al cargar ordenes de trabajo:", err);
        state.workOrders = [];
    }
    loadPaymentsList();
};

document.addEventListener('DOMContentLoaded', () => {
    if (state.currentUserRole === 'inspector') {
        loadWorkOrdersFromServer();
    }
});
