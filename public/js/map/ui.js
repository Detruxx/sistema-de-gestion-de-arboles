/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

export function setupUI() {
    const sidebar = document.getElementById('tree-sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const btnTreeBack = document.getElementById('btn-tree-back');
    const btnToggleFilters = document.getElementById('btn-toggle-filters');
    const filterDropdownMenu = document.getElementById('filter-dropdown-menu');

    // Función para abrir/cerrar panel de detalles
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-closed');
        });
    }

    if (btnTreeBack) {
        btnTreeBack.addEventListener('click', () => {
            sidebar.classList.add('sidebar-closed');
        });
    }

    // Alternar el menú desplegable de filtros
    if (btnToggleFilters && filterDropdownMenu) {
        btnToggleFilters.addEventListener('click', (e) => {
            e.stopPropagation();
            btnToggleFilters.classList.toggle('active');
            filterDropdownMenu.classList.toggle('active');
        });

        // Cerrar el menú si se hace clic fuera del mismo
        document.addEventListener('click', (e) => {
            if (!filterDropdownMenu.contains(e.target) && e.target !== btnToggleFilters && !btnToggleFilters.contains(e.target)) {
                btnToggleFilters.classList.remove('active');
                filterDropdownMenu.classList.remove('active');
            }
        });
    }
}

export function openSidebar() {
    const sidebar = document.getElementById('tree-sidebar');
    if (sidebar) {
        sidebar.classList.remove('sidebar-closed');
    }
}
