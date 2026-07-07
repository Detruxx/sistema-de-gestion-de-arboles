/**
 * API (Dashboard Empresa): Funciones de conexión al servidor y llamadas AJAX para el panel de la empresa contratista.
 */

export async function fetchCompanyData() {
    const response = await fetch('/company/dashboard-data');
    if (!response.ok) throw new Error('Error fetching data');
    return await response.json();
}

export async function putJobStatus(id, newStatus, scheduledDate = null) {
    const payload = { work_status: newStatus };
    if (scheduledDate) payload.scheduled_date = scheduledDate;

    const response = await fetch(`/work-orders/${id}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        },
        body: JSON.stringify(payload)
    });
    if (!response.ok) {
        let msg = 'Error al actualizar estado del trabajo';
        try {
            const data = await response.json();
            if (data && data.message) msg = data.message;
        } catch(e) {}
        throw new Error(msg);
    }
    return true;
}

export async function putPaymentStatus(id, paymentStatus) {
    const payload = { payment_status: paymentStatus };
    const response = await fetch(`/work-orders/${id}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken()
        },
        body: JSON.stringify(payload)
    });
    if (!response.ok) {
        let msg = 'Error al actualizar estado de pago';
        try {
            const data = await response.json();
            if (data && data.message) msg = data.message;
        } catch(e) {}
        throw new Error(msg);
    }
    return true;
}
