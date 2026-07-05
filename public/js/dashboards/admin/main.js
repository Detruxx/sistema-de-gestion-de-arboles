/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { showModule, toggleAdminSidebar, getCsrfToken } from '../shared/layout.js';
import * as residents from './residents.js';
import * as inspectors from './inspectors.js';
import * as companies from './companies.js';
import * as api from './api.js';

window.showModule = showModule;
window.toggleAdminSidebar = toggleAdminSidebar;
window.getCsrfToken = getCsrfToken;

Object.assign(window, residents);
Object.assign(window, inspectors);
Object.assign(window, companies);
Object.assign(window, api);

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.loadDataFromServer === 'function') window.loadDataFromServer();
});
