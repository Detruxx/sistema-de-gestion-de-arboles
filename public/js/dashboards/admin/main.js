/**
 * Principal (Dashboard Administrador): Punto de entrada y configuración general para el panel de administración.
 */

import { showModule, toggleAdminSidebar, getCsrfToken } from '../shared/layout.js';
import * as residents from './residents.js';
import * as inspectors from './inspectors.js';
import * as companies from './companies.js';
import * as api from './api.js';
import * as analytics from './analytics.js';

window.showModule = function(moduleName) {
    showModule(moduleName);
    if (moduleName === 'estadisticas') {
        analytics.loadAnalyticsModule();
    }
};
window.toggleAdminSidebar = toggleAdminSidebar;
window.getCsrfToken = getCsrfToken;

Object.assign(window, residents);
Object.assign(window, inspectors);
Object.assign(window, companies);
Object.assign(window, api);
Object.assign(window, analytics);

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.loadDataFromServer === 'function') window.loadDataFromServer();
    handleHashRouting();
});

window.addEventListener('hashchange', handleHashRouting);

function handleHashRouting() {
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        if (['resumen', 'vecinos', 'inspectores', 'empresas', 'estadisticas'].includes(hash)) {
            window.showModule(hash);
        }
    }
}
