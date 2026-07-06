/**
 * Interfaz (Dashboard Inspector): Lógica de manipulación del DOM y renderizado de la interfaz para el inspector.
 */
import { state } from './state.js';

export function updateStats() {
    const elTotal = document.getElementById('stat-total-claims');
    const elResueltos = document.getElementById('stat-resolved-claims');
    const elPendientes = document.getElementById('stat-pending-claims');
    const elUnread = document.getElementById('unread-count-badge');

    const isTerminal = (slug) => {
        const status = state.requestStatuses.find(rs => rs.slug === slug);
        return status ? status.is_terminal : false;
    };

    if (elTotal) elTotal.innerText = state.claims.length;
    if (elResueltos) elResueltos.innerText = state.claims.filter(c => isTerminal(c.estado)).length;
    if (elPendientes) elPendientes.innerText = state.claims.filter(c => !isTerminal(c.estado)).length;
    if (elUnread) elUnread.innerText = state.claims.filter(c => !isTerminal(c.estado)).length;
}

