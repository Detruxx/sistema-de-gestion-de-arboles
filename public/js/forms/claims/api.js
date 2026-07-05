/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

export async function fetchRequestTypes() {
    const response = await fetch('/api/request-types');
    if (!response.ok) throw new Error('Error al cargar tipos de reclamo');
    return await response.json();
}

export async function submitClaim(formData, csrfToken) {
    const response = await fetch('/requests', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    });
    
    if (!response.ok) throw new Error('Error al registrar el reclamo en el servidor.');
    return await response.json();
}

export async function fetchClaimDetails(id) {
    const response = await fetch(`/requests/${id}`, {
        headers: {
            'Accept': 'application/json'
        }
    });
    if (!response.ok) throw new Error('Reclamo no encontrado o error en el servidor');
    return await response.json();
}

export async function fetchRequestStatuses() {
    const response = await fetch('/api/request-statuses');
    if (!response.ok) throw new Error('Error cargando estados');
    return await response.json();
}
