/**
 * Interfaz (Dashboard Inspector): Lógica de manipulación del DOM y renderizado de la interfaz para el inspector.
 */

export function updateStats(claims, requestStatuses) {
    const elTotal = document.getElementById('stat-total-claims');
    const elResueltos = document.getElementById('stat-resolved-claims');
    const elPendientes = document.getElementById('stat-pending-claims');
    const elUnread = document.getElementById('unread-count-badge');

    const isTerminal = (slug) => {
        const s = requestStatuses.find(rs => rs.slug === slug);
        return s ? s.is_terminal : false;
    };

    if (elTotal) elTotal.innerText = claims.length;
    if (elResueltos) elResueltos.innerText = claims.filter(c => isTerminal(c.estado)).length;
    if (elPendientes) elPendientes.innerText = claims.filter(c => !isTerminal(c.estado)).length;
    if (elUnread) elUnread.innerText = claims.filter(c => !isTerminal(c.estado)).length;
}

