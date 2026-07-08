/**
 * Principal (Dashboard Inspector): Punto de entrada y configuración general para el panel del inspector.
 */

import { showModule, toggleAdminSidebar, getCsrfToken } from '../shared/layout.js';
import * as claims from './claims.js';
import * as trees from './trees.js';
import * as map from './map.js';

// Exponer utilidades al window para que los onclick de blade funcionen
window.showModule = (moduleName) => {
    showModule(moduleName);
    if (moduleName === 'reclamos') {
        claims.triggerMapResize();
    }
};
window.toggleAdminSidebar = toggleAdminSidebar;
window.getCsrfToken = getCsrfToken;

// Exponer funciones de cada módulo
Object.assign(window, claims);
Object.assign(window, trees);
Object.assign(window, map);

// Inicializar la carga de datos si estamos en Inspector
function initInspector() {
    if (typeof window.loadClaimsFromServer === 'function') window.loadClaimsFromServer();
    if (typeof window.loadTreesFromServer === 'function') window.loadTreesFromServer();
    
    handleHashRouting();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initInspector);
} else {
    initInspector();
}

window.addEventListener('hashchange', handleHashRouting);

function handleHashRouting() {
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        if (['resumen', 'reclamos', 'inventario'].includes(hash)) {
            window.showModule(hash);
        }
    }
}
