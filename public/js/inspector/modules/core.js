// --- Base de Datos Dinámica Global ---
window.claims = [];
window.requestStatuses = [];
window.trees = [];
window.selectedClaimId = null;
window.selectedTreeId = null;

window.getCsrfToken = function() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

// --- Cambio de Módulo ---
window.showModule = function(moduleName) {
    document.querySelectorAll('.dashboard-module').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar-btn').forEach(el => el.classList.remove('active'));

    const moduleEl = document.getElementById(`module-${moduleName}`);
    if(moduleEl) moduleEl.classList.add('active');
    
    const menuEl = document.getElementById(`menu-${moduleName}`);
    if(menuEl) menuEl.classList.add('active');
};

window.updateStats = function() {
    const elTotal = document.getElementById('stat-total-claims');
    const elResueltos = document.getElementById('stat-resolved-claims');
    const elPendientes = document.getElementById('stat-pending-claims');
    const elUnread = document.getElementById('unread-count-badge');
    
    const isTerminal = (slug) => {
        const s = window.requestStatuses.find(rs => rs.slug === slug);
        return s ? s.is_terminal : false;
    };
    
    if(elTotal) elTotal.innerText = window.claims.length;
    if(elResueltos) elResueltos.innerText = window.claims.filter(c => isTerminal(c.estado)).length;
    if(elPendientes) elPendientes.innerText = window.claims.filter(c => !isTerminal(c.estado)).length;
    if(elUnread) elUnread.innerText = window.claims.filter(c => !isTerminal(c.estado)).length;
};

// --- Inicialización Global ---
document.addEventListener('DOMContentLoaded', () => {
    if (typeof loadClaimsFromServer === 'function') loadClaimsFromServer();
    if (typeof loadTreesFromServer === 'function') loadTreesFromServer();
});
