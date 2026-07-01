// --- Lógica de Gestión de Roles de Usuarios ---

window.users = [];
window.selectedUserId = null;

window.loadUsersList = function () {
    const container = document.getElementById('users-list-container');
    if (!container) return;
    container.innerHTML = '';

    const filterVal = document.getElementById('filter-user-role')?.value || '';
    const searchVal = document.getElementById('search-users')?.value.toLowerCase() || '';

    const filtered = window.users.filter(u => {
        const matchesSearch = u.name.toLowerCase().includes(searchVal) || u.email.toLowerCase().includes(searchVal);
        const matchesRole = filterVal === '' || u.role === filterVal;
        return matchesSearch && matchesRole;
    });

    if (filtered.length === 0) {
        container.innerHTML = '<div class="empty-state-panel" style="padding: 20px;"><p>No se encontraron usuarios</p></div>';
        return;
    }

    filtered.forEach(u => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedUserId === u.id ? 'active' : ''}`;
        card.onclick = () => window.selectUser(u.id);

        let roleBadgeColor = '#6b7280';
        let roleName = 'Vecino';
        if (u.role === 'admin') {
            roleBadgeColor = '#e74c3c';
            roleName = 'Administrador';
        } else if (u.role === 'inspector') {
            roleBadgeColor = '#3498db';
            roleName = 'Inspector';
        }

        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">#ID ${u.id}</span>
                <span class="badge-status" style="background-color: ${roleBadgeColor}20; color: ${roleBadgeColor}; border: 1px solid ${roleBadgeColor};">${roleName}</span>
            </div>
            <div class="list-item-title">${u.name}</div>
            <div class="list-item-subtitle">${u.email}</div>
        `;
        container.appendChild(card);
    });
};

window.selectUser = function (id) {
    window.selectedUserId = id;
    window.loadUsersList();

    const user = window.users.find(u => u.id === id);
    const panel = document.getElementById('user-detail-panel');

    if (!user || !panel) return;

    let roleBadgeColor = '#6b7280';
    let roleName = 'Vecino';
    if (user.role === 'admin') {
        roleBadgeColor = '#e74c3c';
        roleName = 'Administrador';
    } else if (user.role === 'inspector') {
        roleBadgeColor = '#3498db';
        roleName = 'Inspector';
    }

    panel.innerHTML = `
        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${user.name}</h3>
                <p class="detail-subtitle">ID Usuario: <strong style="color:var(--admin-text-primary);">${user.id}</strong></p>
            </div>
            <span class="badge-status" style="background-color: ${roleBadgeColor}20; color: ${roleBadgeColor}; border: 1px solid ${roleBadgeColor};">${roleName}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Correo Electrónico</p>
            <p class="detail-value" style="font-size: 1.1rem; color: var(--admin-text-primary);">${user.email}</p>
        </div>

        <div class="detail-box" style="margin-top: 30px; background: rgba(245,249,246,0.03); border: 1px solid rgba(245,249,246,0.08); border-radius: 8px; padding: 20px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--admin-accent); font-family: var(--font-display);">Asignar Nuevo Rol</h4>
            <form id="change-role-form" onsubmit="window.submitUserRole(event, ${user.id})">
                <div class="admin-form-group" style="margin-bottom: 20px;">
                    <label for="new-user-role" style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.85rem; color: rgba(245,249,246,0.6);">Seleccionar Rol de Acceso</label>
                    <select id="new-user-role" required style="width: 100%; padding: 10px; background: var(--admin-bg-panel); border: 1px solid rgba(245,249,246,0.15); border-radius: 6px; color: var(--admin-text-primary); font-family: var(--font-sans);">
                        <option value="vecino" ${user.role === 'vecino' ? 'selected' : ''}>Vecino (Acceso básico para reclamos)</option>
                        <option value="inspector" ${user.role === 'inspector' ? 'selected' : ''}>Inspector (Gestión de reclamos y árboles)</option>
                        <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Administrador (Estadísticas generales y control de roles)</option>
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">Actualizar Rol</button>
                </div>
            </form>
        </div>
    `;
};

window.filterUsers = function () {
    window.loadUsersList();
};

window.submitUserRole = async function (e, id) {
    e.preventDefault();
    const newRole = document.getElementById('new-user-role').value;
    const token = typeof window.getCsrfToken === 'function' ? window.getCsrfToken() : '';

    try {
        const response = await fetch(`/api/admin/users/${id}/role`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ role: newRole })
        });

        if (response.ok) {
            // Actualizar localmente el array
            const user = window.users.find(u => u.id === id);
            if (user) {
                user.role = newRole;
            }

            // Mostrar banner de éxito
            window.showRolesNotification('Rol actualizado con éxito.');

            // Recargar vista
            window.selectUser(id);
            window.loadUsersList();
        } else {
            const errResult = await response.json();
            alert(errResult.message || 'Error al actualizar el rol.');
        }
    } catch (err) {
        console.error("Error al actualizar el rol:", err);
        alert('Ocurrió un error al enviar la solicitud.');
    }
};

window.showRolesNotification = function (text) {
    const banner = document.getElementById('notification-banner');
    const bannerText = document.getElementById('notification-text');
    if (banner && bannerText) {
        bannerText.innerText = text;
        banner.style.display = 'flex';
        banner.style.animation = 'slideIn 0.3s forwards';
        setTimeout(() => {
            banner.style.animation = 'slideOut 0.3s forwards';
            setTimeout(() => {
                banner.style.display = 'none';
            }, 300);
        }, 3000);
    }
};

window.loadUsersFromServer = async function () {
    try {
        const response = await fetch('/api/admin/users', {
            headers: {
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            const result = await response.json();
            window.users = result.data;
            window.loadUsersList();
        }
    } catch (err) {
        console.error("Error al cargar usuarios:", err);
    }
};

// Auto-inicializar si el usuario es administrador
document.addEventListener('DOMContentLoaded', () => {
    if (window.currentUserRole === 'admin') {
        window.loadUsersFromServer();
    }
});
