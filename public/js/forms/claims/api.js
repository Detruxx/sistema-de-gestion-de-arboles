/**
 * API (Formulario Reclamos): Funciones de conexión al servidor para el envío de reclamos.
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
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    });
    
    if (!response.ok) {
        let errorMessage = 'Error al registrar el reclamo en el servidor.';
        try {
            const errorData = await response.json();
            if (errorData.errors) {
                // Extraer el primer error de validación
                const firstKey = Object.keys(errorData.errors)[0];
                errorMessage = errorData.errors[firstKey][0];
            } else if (errorData.message) {
                errorMessage = errorData.message;
            }
        } catch (e) {
            console.error("No se pudo parsear el error:", e);
        }
        throw new Error(errorMessage);
    }
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
