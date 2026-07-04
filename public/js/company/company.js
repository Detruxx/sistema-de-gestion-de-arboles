// --- Lógica del Dashboard de Empresa Contratista ---

window.companyJobs = [];
window.tenders = [];
window.selectedJobId = null;
window.selectedTenderId = null;

// --- Actualizar Estadísticas del Resumen ---
window.updateCompanyStats = function () {
    const completedCount = window.companyJobs.filter(j => j.work_status === 'Finalizado').length;
    const pendingCount = window.companyJobs.filter(j => j.work_status !== 'Finalizado').length;
    const availableTendersCount = window.tenders.filter(t => !t.applied).length;

    const elCompleted = document.getElementById('company-stat-completed');
    const elPending = document.getElementById('company-stat-pending');
    const elTenders = document.getElementById('company-stat-tenders');

    if (elCompleted) elCompleted.innerText = completedCount;
    if (elPending) elPending.innerText = pendingCount;
    if (elTenders) elTenders.innerText = availableTendersCount;
};

// --- Renderizar Trabajos Asignados ---
window.loadCompanyJobsList = function () {
    const container = document.getElementById('company-jobs-list-container');
    if (!container) return;
    container.innerHTML = '';

    if (window.companyJobs.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No tienes trabajos asignados.</p></div>';
        return;
    }

    window.companyJobs.forEach(j => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedJobId === j.id ? 'active' : ''}`;
        card.onclick = () => window.selectCompanyJob(j.id);

        let statusColor = '#3498db';
        if (j.work_status === 'En Proceso') statusColor = '#e67e22';
        else if (j.work_status === 'Finalizado') statusColor = '#22c55e';
        else if (j.work_status === 'En espera') statusColor = '#95a5a6';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Orden #${j.id}</span>
                <span class="badge-status" style="background-color: ${statusColor}20; color: ${statusColor}; border: 1px solid ${statusColor};">${j.work_status}</span>
            </div>
            <div class="list-item-title">${j.task_description}</div>
            <div class="list-item-subtitle">Programado: ${j.scheduled_date || 'Sin fecha asignada'}</div>
        `;
        container.appendChild(card);
    });
};

window.selectCompanyJob = function (id) {
    window.selectedJobId = id;
    window.loadCompanyJobsList();

    const j = window.companyJobs.find(item => item.id === id);
    const panel = document.getElementById('company-job-detail-panel');
    if (!j || !panel) return;

    let statusColor = '#3498db';
    if (j.work_status === 'En Proceso') statusColor = '#e67e22';
    else if (j.work_status === 'Finalizado') statusColor = '#22c55e';
    else if (j.work_status === 'En espera') statusColor = '#95a5a6';

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${j.task_description}</h3>
                <p class="detail-subtitle">Orden de Trabajo #${j.id} | Asignado el ${j.created_at || 'Recientemente'}</p>
            </div>
            <span class="badge-status" style="background-color: ${statusColor}20; color: ${statusColor}; border: 1px solid ${statusColor}; font-size: 1rem; padding: 6px 12px;">${j.work_status}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Ubicación del Reclamo Comunal</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${j.request ? j.request.direccion || 'Av. Cabildo 2800' : 'Av. Cabildo 2800'} (Jacarandá)</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Detalles del Reclamo</p>
            <p class="detail-value">${j.request ? j.request.descripcion || 'Poda preventiva de ramas con peligro de caída.' : 'Poda preventiva de ramas con peligro de caída.'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Orden de Ejecución Secuencial</p>
            <p class="detail-value">Intervención Nivel: <strong>${j.execution_order || 1}</strong></p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Actualizar Estado del Trabajo</h4>
            
            ${j.work_status === 'Finalizado' ? `
                <div style="color: #22c55e; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    <span>✔ Tarea Completada. El control ha sido devuelto al inspector de la comuna para continuar hablando con el vecino.</span>
                </div>
            ` : `
                <div style="display: flex; gap: 10px;">
                    ${j.work_status === 'Asignado' || j.work_status === 'En espera' ? `
                        <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'En Proceso')" style="background-color: #ea580c; border-color: #ea580c;">
                            Iniciar Tarea (En Proceso)
                        </button>
                    ` : ''}
                    
                    ${j.work_status === 'En Proceso' ? `
                        <button class="btn-primary" onclick="window.updateJobStatus(${j.id}, 'Finalizado')" style="background-color: #22c55e; border-color: #22c55e;">
                            Marcar como Finalizado
                        </button>
                    ` : ''}
                </div>
            `}
        </div>
    `;
};

window.updateJobStatus = async function (id, newStatus) {
    const j = window.companyJobs.find(item => item.id === id);
    if (!j) return;

    try {
        const response = await fetch(`/api/work-orders/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify({ work_status: newStatus })
        });

        if (!response.ok) throw new Error('Error al actualizar estado del trabajo');

        j.work_status = newStatus;
        if (newStatus === 'Finalizado') {
            window.showNotification(`Trabajo #${id} finalizado. Control devuelto al Inspector.`);
        } else {
            window.showNotification(`Trabajo #${id} actualizado a: ${newStatus}`);
        }
        window.selectCompanyJob(id);
        window.loadCompanyJobsList();
        window.updateCompanyStats();

    } catch (err) {
        console.error("Error al actualizar estado del trabajo:", err);
    }
};

// --- Renderizar Licitaciones y Postulaciones ---
window.loadTendersList = function () {
    const container = document.getElementById('tenders-list-container');
    if (!container) return;
    container.innerHTML = '';

    if (window.tenders.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No hay licitaciones comunales abiertas hoy.</p></div>';
        return;
    }

    window.tenders.forEach(t => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedTenderId === t.id ? 'active' : ''}`;
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
};

window.selectTender = function (id) {
    window.selectedTenderId = id;
    window.loadTendersList();

    const t = window.tenders.find(item => item.id === id);
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
};

window.submitTenderBid = async function (id) {
    const t = window.tenders.find(item => item.id === id);
    if (!t) return;

    try {
        const response = await fetch(`/api/work-orders/${id}/apply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            }
        });

        if (!response.ok) throw new Error('Error al enviar propuesta');

        t.applied = true;
        window.showNotification(`Postulación enviada exitosamente para la licitación #${id}`);
        window.selectTender(id);
        window.loadTendersList();
        window.updateCompanyStats();

    } catch (err) {
        console.error("Error al enviar postulación:", err);
    }
};

// --- Utilidades ---
window.showNotification = function (text) {
    const banner = document.getElementById('notification-banner');
    const label = document.getElementById('notification-text');
    if (banner && label) {
        label.innerText = text;
        banner.style.display = 'flex';
        setTimeout(() => {
            banner.style.display = 'none';
        }, 4000);
    }
};

// --- Cargar Datos del Servidor ---
window.loadCompanyData = async function () {
    try {
        const response = await fetch('/api/company/dashboard-data');
        if (response.ok) {
            const data = await response.json();
            window.companyJobs = data.jobs || [];
            window.tenders = data.tenders || [];
        } else {
            window.companyJobs = [];
            window.tenders = [];
        }
    } catch (err) {
        console.error("Error al cargar datos de empresa:", err);
        window.companyJobs = [];
        window.tenders = [];
    }

    window.loadCompanyJobsList();
    window.loadTendersList();
    window.updateCompanyStats();
};

document.addEventListener('DOMContentLoaded', () => {
    window.loadCompanyData();
});
