/**
 * UI (Dashboard Inspector): Funciones para la manipulación del DOM y visualización de la interfaz.
 */

import { state } from './state.js';
import { getClaimListCardHtml, getClaimModalHtml } from './claims-template.js';
import { updateStats } from './ui.js';
import { fetchClaimPhotos } from './api.js';

let currentGalleryPhotos = [];
let currentPhotoIndex = 0;

export function loadClaimsList() {
    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';

    state.claims.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => window.selectClaim(c.id);

        const statusObj = state.requestStatuses.find(rs => rs.slug === c.estado);
        const statusColor = statusObj ? statusObj.color : '#e5e7eb';
        card.style.setProperty('border-left', `5px solid ${statusColor}`, 'important');

        card.innerHTML = getClaimListCardHtml(c, state.selectedClaimId === c.id, statusObj);
        container.appendChild(card);
    });
}

export function selectClaim(id) {
    state.selectedClaimId = id;
    loadClaimsList();

    const claim = state.claims.find(c => c.id === id);
    const modal = document.getElementById('claim-detail-modal');
    const panel = document.getElementById('claim-modal-body-content');

    if (!claim || !panel || !modal) return;

    panel.innerHTML = getClaimModalHtml(claim, state);
    modal.style.display = 'flex';
}

export function filterClaims() {
    const query = document.getElementById('search-claims').value.toLowerCase();
    const statusFilter = document.getElementById('filter-claim-status') ? document.getElementById('filter-claim-status').value : '';
    const categoryFilter = document.getElementById('filter-claim-category') ? document.getElementById('filter-claim-category').value : '';

    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';

    const filtered = state.claims.filter(c => {
        const matchesQuery = c.vecino.toLowerCase().includes(query) ||
            c.direccion.toLowerCase().includes(query) ||
            c.id.toLowerCase().includes(query);
        const matchesStatus = !statusFilter || c.estado === statusFilter;
        const matchesCategory = !categoryFilter || c.categoria === categoryFilter;

        return matchesQuery && matchesStatus && matchesCategory;
    });

    filtered.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${state.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => window.selectClaim(c.id);

        const statusObj = state.requestStatuses.find(rs => rs.slug === c.estado);
        const statusColor = statusObj ? statusObj.color : '#e5e7eb';
        card.style.setProperty('border-left', `5px solid ${statusColor}`, 'important');

        card.innerHTML = getClaimListCardHtml(c, state.selectedClaimId === c.id, statusObj);
        container.appendChild(card);
    });
}

export function closeClaimDetailModal() {
    const modal = document.getElementById('claim-detail-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

export async function openPhotosGallery(claimId) {
    const claim = state.claims.find(c => c.id === claimId);
    if (!claim || !claim.photo_count || claim.photo_count === 0) return;

    try {
        const response = await fetchClaimPhotos(claim.raw_request_id);
        if (!response.data || response.data.length === 0) return;
        
        currentGalleryPhotos = response.data;
        currentPhotoIndex = 0;
        
        updateGalleryModal();
        document.getElementById('photos-gallery-modal').style.display = 'flex';
    } catch (err) {
        console.error("Error al cargar fotos", err);
        alert("Ocurrió un error al cargar las fotos.");
    }
}

export function closePhotosGallery() {
    document.getElementById('photos-gallery-modal').style.display = 'none';
}

export function prevGalleryPhoto() {
    if (currentPhotoIndex > 0) {
        currentPhotoIndex--;
        updateGalleryModal();
    }
}

export function nextGalleryPhoto() {
    if (currentPhotoIndex < currentGalleryPhotos.length - 1) {
        currentPhotoIndex++;
        updateGalleryModal();
    }
}

function updateGalleryModal() {
    const img = document.getElementById('gallery-current-image');
    const counter = document.getElementById('gallery-counter');
    const prevBtn = document.getElementById('gallery-prev-btn');
    const nextBtn = document.getElementById('gallery-next-btn');

    if (!img) return;

    // Asumimos que los paths están guardados en la BD relativos a storage/app/public o directamente en public.
    // Usualmente Laravel devuelve el path desde storage, así que ajustamos si es necesario.
    let src = currentGalleryPhotos[currentPhotoIndex];
    if (src && !src.startsWith('/storage/') && !src.startsWith('http')) {
        src = '/storage/' + src;
    }
    img.src = src;

    if (counter) {
        counter.textContent = `${currentPhotoIndex + 1} / ${currentGalleryPhotos.length}`;
    }

    if (prevBtn) {
        prevBtn.style.display = currentPhotoIndex > 0 ? 'block' : 'none';
    }
    if (nextBtn) {
        nextBtn.style.display = currentPhotoIndex < currentGalleryPhotos.length - 1 ? 'block' : 'none';
    }
}
