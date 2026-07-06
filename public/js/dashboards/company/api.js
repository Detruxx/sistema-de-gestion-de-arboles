/**
 * API (Dashboard Empresa): Funciones de conexión al servidor y llamadas AJAX para el panel de la empresa contratista.
 */

export async function fetchCompanyData() {
    const response = await fetch('/api/company/dashboard-data');
    if (!response.ok) throw new Error('Error fetching data');
    return await response.json();
}

export async function putJobStatus(id, newStatus) {
    const response = await fetch(`/api/work-orders/${id}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        },
        body: JSON.stringify({ work_status: newStatus })
    });
    if (!response.ok) throw new Error('Error al actualizar estado del trabajo');
    return true;
}


