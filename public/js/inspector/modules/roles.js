// --- Lógica de Gestión de Roles, Vecinos, Inspectores y Empresas (Admin Dashboard) ---

window.users = [];
window.companies = [];
window.selectedNeighborId = null;
window.selectedInspectorId = null;
window.selectedCompanyId = null;

// --- Carga y Renderizado de Vecinos ---
window.loadNeighborsList = function () {
    const container = document.getElementById('neighbors-list-container');
    if (!container) return;
    container.innerHTML = '';

    const searchVal = document.getElementById('search-neighbors')?.value.toLowerCase() || '';
    const neighbors = window.users.filter(u => u.role === 'vecino' && (u.name.toLowerCase().includes(searchVal) || u.email.toLowerCase().includes(searchVal)));

    if (neighbors.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No se encontraron vecinos</p></div>';
        return;
    }

    neighbors.forEach(u => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedNeighborId === u.id ? 'active' : ''}`;
        card.onclick = () => window.selectNeighbor(u.id);

        const isBanned = u.banned_until && new Date(u.banned_until) > new Date();
        const badgeColor = isBanned ? '#ef4444' : '#22c55e';
        const badgeLabel = isBanned ? 'Suspendido' : 'Activo';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">#${u.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor};">${badgeLabel}</span>
            </div>
            <div class="list-item-title">${u.name} ${u.last_name || ''}</div>
            <div class="list-item-subtitle">${u.email}</div>
        `;
        container.appendChild(card);
    });
};

window.selectNeighbor = function (id) {
    window.selectedNeighborId = id;
    window.loadNeighborsList();

    const u = window.users.find(item => item.id === id);
    const panel = document.getElementById('neighbor-detail-panel');
    if (!u || !panel) return;

    const isBanned = u.banned_until && new Date(u.banned_until) > new Date();
    const banText = isBanned ? `Suspendido hasta: ${new Date(u.banned_until).toLocaleDateString()}` : 'Activo';

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${u.name} ${u.last_name || ''}</h3>
                <p class="detail-subtitle">ID Vecino: <strong style="color:var(--admin-text-primary);">${u.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: ${isBanned ? '#ef4444' : '#22c55e'}20; color: ${isBanned ? '#ef4444' : '#22c55e'}; border: 1px solid ${isBanned ? '#ef4444' : '#22c55e'};">${banText}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Correo Electrónico</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${u.email}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Domicilio Declarado</p>
            <p class="detail-value">${u.address || 'Av. Cabildo 2200, CABA'}</p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Acciones de Control de Vecino</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <button class="btn-primary" onclick="window.changeUserRole(${u.id}, 'inspector')" style="background-color: #3498db; border-color: #3498db;">
                    Promover a Inspector
                </button>
                <button class="btn-secondary" onclick="window.banUserPrompt(${u.id}, 7)" style="color: #ea580c; border-color: #ea580c;">
                    Suspender 7 Días
                </button>
                <button class="btn-secondary" onclick="window.banUserPrompt(${u.id}, 30)" style="color: #e11d48; border-color: #e11d48;">
                    Suspender 30 Días
                </button>
                ${isBanned ? `
                    <button class="btn-primary" onclick="window.liftBan(${u.id})" style="background-color: #22c55e; border-color: #22c55e;">
                        Levantar Suspensión
                    </button>
                ` : `
                    <button class="btn-primary" onclick="window.banUserPrompt(${u.id}, 365)" style="background-color: #ef4444; border-color: #ef4444;">
                        Baneo Permanente
                    </button>
                `}
            </div>
        </div>
    `;
};

window.filterNeighbors = function () {
    window.loadNeighborsList();
};


// --- Carga y Renderizado de Inspectores ---
window.loadInspectorsList = function () {
    const container = document.getElementById('inspectors-list-container');
    if (!container) return;
    container.innerHTML = '';

    const searchVal = document.getElementById('search-inspectors')?.value.toLowerCase() || '';
    const inspectors = window.users.filter(u => u.role === 'inspector' && (u.name.toLowerCase().includes(searchVal) || u.email.toLowerCase().includes(searchVal)));

    if (inspectors.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No se encontraron inspectores</p></div>';
        return;
    }

    inspectors.forEach(u => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedInspectorId === u.id ? 'active' : ''}`;
        card.onclick = () => window.selectInspector(u.id);

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Inspector #${u.id}</span>
                <span class="badge-status" style="background-color: #3498db20; color: #3498db; border: 1px solid #3498db;">Activo</span>
            </div>
            <div class="list-item-title">${u.name} ${u.last_name || ''}</div>
            <div class="list-item-subtitle">${u.email}</div>
        `;
        container.appendChild(card);
    });
};

window.selectInspector = function (id) {
    window.selectedInspectorId = id;
    window.loadInspectorsList();

    const u = window.users.find(item => item.id === id);
    const panel = document.getElementById('inspector-detail-panel');
    if (!u || !panel) return;

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${u.name} ${u.last_name || ''}</h3>
                <p class="detail-subtitle">ID Inspector: <strong style="color:var(--admin-text-primary);">${u.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: #3498db20; color: #3498db; border: 1px solid #3498db;">Activo</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Correo Comunal</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${u.email}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Jurisdicción / Rol</p>
            <p class="detail-value">Personal Técnico Autorizado (Inspector Comunal)</p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: #ef4444; font-family: var(--font-display);">Remover Inspector</h4>
            <p style="font-size: 0.9rem; color: var(--admin-text-secondary); margin-bottom: 15px;">Al remover el rol de inspector, este usuario volverá a ser un vecino común y perderá todos sus accesos administrativos.</p>
            <button class="btn-primary" onclick="window.changeUserRole(${u.id}, 'vecino')" style="background-color: #ef4444; border-color: #ef4444;">
                Remover Rol de Inspector
            </button>
        </div>
    `;
};

window.filterInspectors = function () {
    window.loadInspectorsList();
};


// --- Carga y Renderizado de Empresas ---
window.loadCompaniesList = function () {
    const container = document.getElementById('companies-list-container');
    if (!container) return;
    container.innerHTML = '';

    if (window.companies.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No se encontraron empresas asociadas</p></div>';
        return;
    }

    window.companies.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedCompanyId === c.id ? 'active' : ''}`;
        card.onclick = () => window.selectCompany(c.id);

        let badgeColor = '#22c55e'; // Activo
        if (c.status === 'De baja') badgeColor = '#ef4444';
        if (c.status === 'Pendiente') badgeColor = '#ea580c';

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">Empresa #${c.id}</span>
                <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor}; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; border-radius: 8px;">${c.status || 'Activo'}</span>
            </div>
            <div class="list-item-title">${c.company_name}</div>
            <div class="list-item-subtitle" style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-top: 5px;">
                <span>${c.address || 'Buenos Aires, CABA'}</span>
                <span>${c.contact_email || ''}</span>
            </div>
        `;
        container.appendChild(card);
    });
};

window.selectCompany = function (id) {
    window.selectedCompanyId = id;
    window.loadCompaniesList();

    const c = window.companies.find(item => item.id === id);
    const panel = document.getElementById('company-detail-panel');
    if (!c || !panel) return;

    let badgeColor = '#22c55e'; // Activo
    if (c.status === 'De baja') badgeColor = '#ef4444';
    if (c.status === 'Pendiente') badgeColor = '#ea580c';

    // Buttons
    let actionButtons = '';
    if (c.status === 'Pendiente') {
        actionButtons = `
            <button class="btn-primary" onclick="window.updateCompanyStatus(${c.id}, 'Activo')" style="background-color: #22c55e; border-color: #22c55e; color: white;">
                Aprobar y Dar de Alta
            </button>
            <button class="btn-secondary" onclick="window.updateCompanyStatus(${c.id}, 'De baja')" style="color: #ef4444; border-color: #ef4444;">
                Rechazar Postulación
            </button>
        `;
    } else if (c.status === 'Activo') {
        actionButtons = `
            <button class="btn-secondary" onclick="window.updateCompanyStatus(${c.id}, 'De baja')" style="color: #ef4444; border-color: #ef4444; font-weight: 600;">
                Dar de Baja Empresa
            </button>
        `;
    } else if (c.status === 'De baja') {
        actionButtons = `
            <button class="btn-primary" onclick="window.updateCompanyStatus(${c.id}, 'Activo')" style="background-color: #22c55e; border-color: #22c55e; color: white;">
                Dar de Alta Empresa
            </button>
        `;
    }

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${c.company_name}</h3>
                <p class="detail-subtitle">ID Empresa: <strong style="color:var(--admin-text-primary);">${c.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: ${badgeColor}20; color: ${badgeColor}; border: 1px solid ${badgeColor}; font-weight: bold;">${c.status || 'Activo'}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">CUIT</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary); font-weight: bold;">${c.cuit || '30-99999999-1'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Representante / Email de contacto</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${c.contact_email || 'contacto@empresa.com.ar'}</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Dirección Fiscal / Operativa</p>
            <p class="detail-value">${c.address || 'Av. de Mayo 800, CABA'}</p>
        </div>

        ${c.services && c.services.length > 0 ? `
        <div class="detail-section">
            <p class="detail-label">Servicios Declarados</p>
            <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-top: 5px;">
                ${c.services.map(s => `<span style="background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500;">${s}</span>`).join('')}
            </div>
        </div>
        ` : ''}

        <div class="detail-box" style="margin-top: 25px; border-left: 4px solid var(--admin-accent); padding: 15px; background: rgba(45, 122, 79, 0.03);">
            <span style="font-size: 0.85rem; color: var(--admin-text-secondary); display: block; text-transform: uppercase;">Órdenes de Trabajo Asignadas</span>
            <strong style="font-size: 1.8rem; color: var(--admin-text-primary); font-family: var(--font-display);">${(c.work_orders || []).length}</strong>
        </div>

        <!-- Acciones de Control de Empresa -->
        <div class="detail-box" style="margin-top: 30px; background: rgba(0,0,0,0.02); border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Acciones de Control de Empresa</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                ${actionButtons}
            </div>
        </div>

        <div style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
            <h4 style="font-family: var(--font-display); color: var(--admin-accent); margin-bottom: 15px;">Historial de Tareas Realizadas</h4>
            ${c.work_orders && c.work_orders.length > 0 ? `
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--admin-border); text-align: left; color: var(--admin-text-secondary);">
                        <th style="padding: 8px;">ID Orden</th>
                        <th style="padding: 8px;">Tarea</th>
                        <th style="padding: 8px;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    ${c.work_orders.map(w => `
                        <tr style="border-bottom: 1px solid var(--admin-border);">
                            <td style="padding: 8px; font-weight: bold;">#${w.id}</td>
                            <td style="padding: 8px;">${w.description || w.task_description}</td>
                            <td style="padding: 8px;"><span class="badge-status" style="font-size: 0.75rem; padding: 2px 6px; background-color: #22c55e20; color: #22c55e; border: 1px solid #22c55e;">${w.status || w.work_status}</span></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            ` : `<p style="color: var(--admin-text-secondary); font-style: italic;">No hay tareas registradas para esta empresa.</p>`}
        </div>
    `;
};

window.updateCompanyStatus = async function (id, newStatus) {
    try {
        const response = await fetch(`/api/admin/companies/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!response.ok) {
            throw new Error('Error al actualizar el estado de la empresa');
        }

        const c = window.companies.find(item => item.id === id);
        if (c) {
            c.status = newStatus;
        }

        window.showNotification(`Empresa #${id} cambiada al estado: ${newStatus}`);
        window.loadCompaniesList();
        window.selectCompany(id);
        window.updateAdminStats();
    } catch (err) {
        console.error("Error al actualizar estado de la empresa:", err);
        window.showNotification(`Error al cambiar el estado de la empresa #${id}`);
    }
};


// --- Acciones de API / Interactivas ---
window.changeUserRole = async function (id, role) {
    const user = window.users.find(u => u.id === id);
    if (!user) return;

    try {
        const response = await fetch(`/api/admin/users/${id}/role`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify({ role: role })
        });

        if (!response.ok) throw new Error('Error al actualizar rol');

        user.role = role;
        window.showNotification(`Usuario #${id} cambiado al rol: ${role}`);
        window.updateAdminStats();
        
        // Reset panels
        if (role === 'inspector') {
            window.selectedNeighborId = null;
            window.loadNeighborsList();
            document.getElementById('neighbor-detail-panel').innerHTML = `<div class="empty-state-panel"><p>Vecino promovido a Inspector.</p></div>`;
            window.loadInspectorsList();
        } else if (role === 'vecino') {
            window.selectedInspectorId = null;
            window.loadInspectorsList();
            document.getElementById('inspector-detail-panel').innerHTML = `<div class="empty-state-panel"><p>Rol removido del inspector.</p></div>`;
            window.loadNeighborsList();
        }

    } catch (err) {
        console.error("Error al actualizar rol:", err);
    }
};

window.banUserPrompt = async function (id, days) {
    const user = window.users.find(u => u.id === id);
    if (!user) return;

    const date = new Date();
    date.setDate(date.getDate() + days);
    const bannedUntil = date.toISOString();

    try {
        const response = await fetch(`/api/admin/users/${id}/ban`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify({ banned_until: bannedUntil })
        });

        if (!response.ok) throw new Error('Error al suspender usuario');

        user.banned_until = bannedUntil;
        window.showNotification(`Vecino #${id} suspendido por ${days} días.`);
        window.selectNeighbor(id);
    } catch (err) {
        console.error("Error al banear usuario:", err);
    }
};

window.liftBan = async function (id) {
    const user = window.users.find(u => u.id === id);
    if (!user) return;

    try {
        const response = await fetch(`/api/admin/users/${id}/ban`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify({ banned_until: null })
        });

        if (!response.ok) throw new Error('Error al levantar suspensión');

        user.banned_until = null;
        window.showNotification(`Baneo de vecino #${id} levantado.`);
        window.selectNeighbor(id);
    } catch (err) {
        console.error("Error al levantar baneo:", err);
    }
};

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

// --- Actualizar tarjetas de Admin Resumen ---
window.updateAdminStats = function () {
    const neighborsCount = window.users.filter(u => u.role === 'vecino').length;
    const inspectorsCount = window.users.filter(u => u.role === 'inspector').length;
    const companiesCount = window.companies.length;
    
    const pendingPostulations = window.companies.filter(c => c.status === 'Pendiente').length;

    const elN = document.getElementById('stat-total-neighbors');
    const elI = document.getElementById('stat-total-inspectors');
    const elC = document.getElementById('stat-total-companies');
    const elP = document.getElementById('stat-pending-postulations');

    if (elN) elN.innerText = neighborsCount;
    if (elI) elI.innerText = inspectorsCount;
    if (elC) elC.innerText = companiesCount;
    if (elP) elP.innerText = pendingPostulations;
};

// --- Carga de datos globales del Admin ---
window.loadAdminData = async function () {
    try {
        const userRes = await fetch('/api/admin/users');
        if (userRes.ok) {
            const data = await userRes.json();
            window.users = data.data;
        } else {
            window.users = [];
        }
    } catch (err) {
        console.error("Error al cargar usuarios:", err);
        window.users = [];
    }

    try {
        const compRes = await fetch('/api/admin/companies');
        if (compRes.ok) {
            const data = await compRes.json();
            window.companies = data.data;
        } else {
            window.companies = [];
        }
    } catch (err) {
        console.error("Error al cargar empresas:", err);
        window.companies = [];
    }

    window.loadNeighborsList();
    window.loadInspectorsList();
    window.loadCompaniesList();
    window.updateAdminStats();
};

document.addEventListener('DOMContentLoaded', () => {
    if (window.currentUserRole === 'admin') {
        window.loadAdminData();
    }
});
