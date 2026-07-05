/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { getCsrfToken } from '../shared/layout.js';

export async function fetchClaims() {
    const res = await fetch('/requests', { headers: { 'Accept': 'application/json' }});
    if (!res.ok) throw new Error('Error al cargar reclamos');
    return res.json();
}

export async function fetchRequestStatuses() {
    const res = await fetch('/api/request-statuses');
    if (!res.ok) throw new Error('Error al cargar estados');
    return res.json();
}

export async function fetchActiveCompanies() {
    const res = await fetch('/api/admin/companies');
    if (!res.ok) throw new Error('Error al cargar empresas');
    return res.json();
}

export async function updateClaimStatus(claimId, payload) {
    const res = await fetch(`/requests/update-status/${claimId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(payload)
    });
    if (!res.ok) throw new Error('Error al actualizar estado');
    return res.json();
}

export async function assignCompanyToClaim(claimId, companyId) {
    const res = await fetch(`/api/admin/claims/${claimId}/assign-company`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ company_id: companyId })
    });
    if (!res.ok) throw new Error('Error al asignar empresa');
    return res.json();
}
