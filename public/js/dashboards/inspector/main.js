/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { showModule, toggleAdminSidebar, getCsrfToken } from '../shared/layout.js';
import * as claims from './claims.js';
import * as trees from './trees.js';
import * as map from './map.js';
import * as payments from './payments.js';

// Exponer utilidades al window para que los onclick de blade funcionen
window.showModule = showModule;
window.toggleAdminSidebar = toggleAdminSidebar;
window.getCsrfToken = getCsrfToken;

// Exponer funciones de cada módulo
Object.assign(window, claims);
Object.assign(window, trees);
Object.assign(window, map);
Object.assign(window, payments);

// Inicializar la carga de datos si estamos en Inspector
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.loadClaimsFromServer === 'function') window.loadClaimsFromServer();
    if (typeof window.loadTreesFromServer === 'function') window.loadTreesFromServer();
});
