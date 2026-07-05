/**
 * Componente (Dashboard Empresa): Lógica para visualizar y postularse a licitaciones abiertas.
 */

import { postTenderBid } from './api.js';
import { updateCompanyStats, showNotification } from './ui.js';
import { getTenders } from './main.js';

let selectedTenderId = null;

// Exponer al scope global para onClick
window.selectTender = function(id) {
    selectedTenderId = id;
    loadTendersList();
    renderTenderDetail(id);
};

window.submitTenderBid = async function(id) {
    const tenders = getTenders();
    const t = tenders.find(item => item.id === id);
    if (!t) return;

    try {
        await postTenderBid(id);
        t.applied = true;
        showNotification(`Postulación enviada exitosamente para la licitación #${id}`);
        window.selectTender(id);
        loadTendersList();
        updateCompanyStats();
    } catch (err) {
        console.error("Error al enviar postulación:", err);
    }
};

export function loadTendersList() {
    const container = document.getElementById('tenders-list-container');
    if (!container) return;
    container.innerHTML = '';

    const tenders = getTenders();

    if (tenders.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No hay licitaciones comunales abiertas hoy.</p></div>';
        return;
    }

    tenders.forEach(t => {
        const card = document.createElement('div');
        card.className = `list-item-card ${selectedTenderId === t.id ? 'active' : ''}`;
        card.onclick = () => window.selectTender(t.id);

        const hasApplied = t.applied;
        const badgeColor = hasApplied ? '#22c55e' : '#3498db';
        const badgeLabel = hasApplied ? 'POSTULADO' : 'ABIERTO';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Licitación #${t.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor};">${badgeLabel}</span>
            </div>
            <div class="list-item-title">${t.task_description}</div>
            <div class="list-item-subtitle">Ubicación: ${t.location || 'Av. Cabildo 1500, CABA'}</div>
        `;
        container.appendChild(card);
    });
}

function renderTenderDetail(id) {
    const tenders = getTenders();
    const t = tenders.find(item => item.id === id);
    const panel = document.getElementById('tender-detail-panel');
    if (!t || !panel) return;

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${t.task_description}</h3>
                <p class="detail-subtitle">Licitación Pública #${t.id} | Comuna 13</p>
            </div>
        </div>

        <div class="detail-section">
            <p class="detail-label">Ubicación de Obra</p>
            <p class="detail-value">${t.location || 'Av. Cabildo 1500, CABA'}</p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            ${t.applied ? `
                <div style="color: #22c55e; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    <span>✔ Ya te has postulado a este trabajo. Esperando resolución de adjudicación.</span>
                </div>
            ` : `
                <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Postularse a Licitación</h4>
                <div style="display: flex; gap: 10px; flex-direction: column;">
                    <p style="font-size: 0.9rem; color: var(--admin-text-secondary); margin-bottom: 10px;">Postúlate para realizar este servicio de mantenimiento y saneamiento.</p>
                    <button class="btn-primary" onclick="window.submitTenderBid(${t.id})" style="max-width: 200px;">
                        Enviar Postulación
                    </button>
                </div>
            `}
        </div>
    `;
}
