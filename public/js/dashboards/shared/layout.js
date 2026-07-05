/**
 * Layout Compartido: Lógica global y estructura de base compartida entre distintos paneles.
 */

export function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

export function showModule(moduleName) {
    document.querySelectorAll('.dashboard-module').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar-btn').forEach(el => el.classList.remove('active'));

    const moduleEl = document.getElementById(`module-${moduleName}`);
    if (moduleEl) moduleEl.classList.add('active');

    const menuEl = document.getElementById(`menu-${moduleName}`);
    if (menuEl) menuEl.classList.add('active');

    // Cerrar el menú desplegable en pantallas pequeñas
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebar) sidebar.classList.remove('menu-open');
}

export function toggleAdminSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebar) sidebar.classList.toggle('menu-open');
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

