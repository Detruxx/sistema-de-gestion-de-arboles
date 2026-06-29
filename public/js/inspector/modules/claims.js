// --- Lógica de Reclamos ---

window.loadClaimsList = function() {
    const container = document.getElementById('claims-list-container');
    if (!container) return;
    container.innerHTML = '';
    
    window.claims.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => window.selectClaim(c.id);

        const statusObj = window.requestStatuses.find(rs => rs.slug === c.estado);
        let statusLabel = statusObj ? statusObj.status_name : c.estado.toUpperCase();
        let statusHex = statusObj ? statusObj.color : '#6b7280';
        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">${c.id}</span>
                <span class="badge-status" style="background-color: ${statusHex}20; color: ${statusHex}; border: 1px solid ${statusHex};">${statusLabel}</span>
            </div>
            <div class="list-item-title">${c.categoria}</div>
            <div class="list-item-subtitle">${c.direccion}</div>
            <div style="font-size: 0.75rem; text-align: right; color: rgba(245,249,246,0.4); margin-top: 5px;">${c.fecha}</div>
        `;
        container.appendChild(card);
    });
};

window.selectClaim = function(id) {
    window.selectedClaimId = id;
    window.loadClaimsList();
    
    const claim = window.claims.find(c => c.id === id);
    const panel = document.getElementById('claim-detail-panel');

    if (!claim || !panel) return;

    const statusObj = window.requestStatuses.find(rs => rs.slug === claim.estado);
    const statusLabel = statusObj ? statusObj.status_name : claim.estado.toUpperCase();
    const statusHex = statusObj ? statusObj.color : '#6b7280';

    panel.innerHTML = `
        ${claim.suggested_duplicate_id ? `
        <div style="background-color: #fef08a; border-left: 4px solid #eab308; padding: 12px; margin-bottom: 20px; border-radius: 4px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <strong style="color: #854d0e; display: block; margin-bottom: 4px;">⚠️ Alerta de Sistema Inteligente</strong>
                <span style="color: #a16207; font-size: 0.9rem;">Este reclamo podría ser un duplicado del reclamo <strong>#${claim.suggested_duplicate_id}</strong> (misma cuadra y tipo de problema).</span>
            </div>
            <div style="display: flex; gap: 8px;">
                <button onclick="window.resolveDuplicate(true, ${claim.suggested_duplicate_id})" style="background: #eab308; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: bold;">✅ Vincular Automáticamente</button>
                <button onclick="window.resolveDuplicate(false)" style="background: transparent; color: #a16207; border: 1px solid #a16207; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">❌ Ignorar</button>
            </div>
        </div>
        ` : ''}

        ${claim.linked_to ? `
        <div style="background-color: #fce7f3; border-left: 4px solid #db2777; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            <strong style="color: #9d174d;">🔗 Reclamo Vinculado</strong>
            <span style="color: #be185d; font-size: 0.9rem; margin-left: 8px;">Este trámite es un duplicado y está anexado al reclamo principal <strong>#${claim.linked_to}</strong>.</span>
        </div>
        ` : ''}

        <div class="detail-header-panel">
            <div>
                <h3 class="detail-title">${claim.categoria}</h3>
                <p class="detail-subtitle">Reclamo ID: <strong style="color:var(--admin-text-primary);">${claim.id}</strong> | Enviado el ${claim.fecha}</p>
            </div>
            <span class="badge-status" id="detail-badge-status" style="background-color: ${statusHex}20; color: ${statusHex}; border: 1px solid ${statusHex};">${statusLabel}</span>
        </div>

        <div class="detail-section">
            <p class="detail-label">Vecino Solicitante</p>
            <p class="detail-value">${claim.vecino} (${claim.email})</p>
        </div>

        <div class="detail-section">
            <p class="detail-label">Dirección / Especie</p>
            <p class="detail-value">${claim.direccion} — Especie involucrada: ${claim.especie}</p>
        </div>

        <div class="detail-box">
            <p class="detail-label">Mensaje / Descripción del problema</p>
            <p class="detail-box-desc">${claim.descripcion}</p>
        </div>

        <div class="status-tracker-container">
            <div class="status-tracker-title">Progreso del Reclamo (Haz clic en un paso para cambiar el estado)</div>
            <div class="status-steps">
                ${window.requestStatuses.map(s => {
                    const currentSeq = window.requestStatuses.find(rs => rs.slug === claim.estado)?.sequence || 0;
                    const isCompleted = s.sequence && s.sequence <= currentSeq;
                    const isActive = claim.estado === s.slug;
                    if (s.sequence || isActive) {
                        return `
                        <div class="status-step ${isCompleted ? 'completed' : ''} ${isActive ? 'active' : ''}" onclick="window.setClaimStatus('${s.slug}')">
                            <div class="step-circle" style="background-color: ${isActive ? s.color : ''}; border-color: ${isActive ? s.color : ''}">${s.sequence || '!'}</div>
                            <div class="step-label">${s.status_name}</div>
                        </div>`;
                    }
                    return '';
                }).join('')}
            </div>
            
            <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center;">
                ${window.requestStatuses.filter(s => s.sequence === null).map(s => `
                    <button class="btn-secondary" style="font-size: 0.8rem; padding: 6px 12px; border: 1px solid ${s.color}; color: ${s.color}; background: ${s.color}15; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='${s.color}30'" onmouseout="this.style.background='${s.color}15'" onclick="window.setClaimStatus('${s.slug}')">
                        ${s.slug === 'denied' ? '✖' : '∞'} Marcar como ${s.status_name}
                    </button>
                `).join('')}
            </div>
        </div>

        <div class="response-section">
            <h4 class="detail-title" style="font-size: 1.2rem;">Responder al Vecino</h4>
            <div class="template-selector">
                <button class="template-btn" onclick="window.applyTemplate('info')">Pedir más info</button>
                <button class="template-btn" onclick="window.applyTemplate('relevated')">Avisar Inspección</button>
                <button class="template-btn" onclick="window.applyTemplate('scheduled')">Avisar Poda</button>
                <button class="template-btn" onclick="window.applyTemplate('resolved')">Informar Resolución</button>
                <button class="template-btn" onclick="window.applyTemplate('denied')">Rechazar</button>
            </div>
            <textarea id="response-text" class="response-textarea" placeholder="Escribe un mensaje personalizado para enviar al correo del vecino..."></textarea>
            <div class="action-row">
                <button class="btn-secondary" onclick="window.clearResponse()">Limpiar</button>
                <button class="btn-primary" onclick="window.sendResponse()">Enviar Respuesta y Actualizar</button>
            </div>
        </div>
    `;
};

window.applyTemplate = function(type) {
    const claim = window.claims.find(c => c.id === window.selectedClaimId);
    if (!claim) return;

    const textarea = document.getElementById('response-text');
    
    let text = '';
    if (type === 'open' || type === 'info') {
        text = `Estimado/a ${claim.vecino},\n\nHemos recibido su solicitud ID ${claim.id} sobre "${claim.categoria}". Un inspector del área técnica estará evaluando la situación a la brevedad. Si posee más imágenes del estado actual del ejemplar, por favor adjúntelas respondiendo a este correo.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'relevated') {
        text = `Estimado/a ${claim.vecino},\n\nLe informamos que su solicitud ID ${claim.id} se encuentra en etapa de Inspección Técnica. Personal calificado visitará la dirección ${claim.direccion} dentro de los próximos 3 días hábiles para diagnosticar el árbol (${claim.especie}) y planificar el plan de acción.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'scheduled' || type === 'in_progress') {
        text = `Estimado/a ${claim.vecino},\n\nTras la inspección realizada en ${claim.direccion}, se ha planificado la intervención correspondiente para el día [Fecha]. Se realizará un saneamiento/poda de despeje preventivo para resguardar la seguridad pública.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'resolved') {
        text = `Estimado/a ${claim.vecino},\n\nNos complace informarle que la solicitud ID ${claim.id} ha sido completada de manera exitosa. Las tareas operativas y el despeje final en la zona han concluido.\n\nMuchas gracias por colaborar con el mantenimiento del arbolado de la Ciudad.\n\nAtentamente,\nGobierno de la Ciudad de Buenos Aires - Comuna 13.`;
    } else if (type === 'denied') {
        text = `Estimado/a ${claim.vecino},\n\nTras la evaluación técnica de su solicitud ID ${claim.id}, lamentamos informarle que la misma ha sido rechazada por no cumplir con los criterios de intervención de la Ley de Arbolado.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    } else if (type === 'vinculated') {
        text = `Estimado/a ${claim.vecino},\n\nLe informamos que su solicitud ID ${claim.id} ha sido vinculada a un trámite preexistente sobre el mismo ejemplar o incidencia.\n\nAtentamente,\nEquipo de Gestión de Arbolado - Comuna 13.`;
    }

    if (textarea) textarea.value = text;
};

window.clearResponse = function() {
    const textarea = document.getElementById('response-text');
    if (textarea) textarea.value = '';
};

window.sendResponse = async function() {
    const claim = window.claims.find(c => c.id === window.selectedClaimId);
    if (!claim) return;

    const responseText = document.getElementById('response-text').value;
    if (!responseText.trim()) {
        alert('Por favor escribe un mensaje de respuesta antes de enviar.');
        return;
    }

    try {
        const response = await fetch(`/requests/update-status/${window.selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify({ respuesta: responseText })
        });

        if (response.ok) {
            claim.respuesta_admin = responseText;
            
            const banner = document.getElementById('notification-banner');
            const text = document.getElementById('notification-text');
            if(text) text.innerText = `Respuesta enviada a ${claim.vecino} (${claim.email}) y guardada en el sistema.`;
            if(banner) banner.style.display = 'flex';

            setTimeout(() => {
                if(banner) banner.style.display = 'none';
            }, 5000);

            window.clearResponse();
        } else {
            alert('Error al guardar la respuesta en el servidor.');
        }
    } catch (err) {
        console.error("Error sending response:", err);
        alert('Error de conexión.');
    }
};

window.filterClaims = function() {
    const query = document.getElementById('search-claims').value.toLowerCase();
    const statusFilter = document.getElementById('filter-claim-status') ? document.getElementById('filter-claim-status').value : '';
    const categoryFilter = document.getElementById('filter-claim-category') ? document.getElementById('filter-claim-category').value : '';

    const container = document.getElementById('claims-list-container');
    if(!container) return;
    container.innerHTML = '';

    const filtered = window.claims.filter(c => {
        const matchesQuery = c.vecino.toLowerCase().includes(query) || 
                             c.direccion.toLowerCase().includes(query) ||
                             c.id.toLowerCase().includes(query);
        const matchesStatus = !statusFilter || c.estado === statusFilter;
        const matchesCategory = !categoryFilter || c.categoria === categoryFilter;

        return matchesQuery && matchesStatus && matchesCategory;
    });

    filtered.forEach(c => {
        const card = document.createElement('div');
        card.className = `list-item-card ${window.selectedClaimId === c.id ? 'active' : ''}`;
        card.onclick = () => window.selectClaim(c.id);

        const statusObj = window.requestStatuses.find(rs => rs.slug === c.estado);
        let statusLabel = statusObj ? statusObj.status_name : c.estado.toUpperCase();
        let statusHex = statusObj ? statusObj.color : '#6b7280';
        card.innerHTML = `
            <div class="list-item-header">
                <span class="list-item-id">${c.id}</span>
                <span class="badge-status" style="background-color: ${statusHex}20; color: ${statusHex}; border: 1px solid ${statusHex};">${statusLabel}</span>
            </div>
            <div class="list-item-title">${c.categoria}</div>
            <div class="list-item-subtitle">${c.direccion}</div>
            <div style="font-size: 0.75rem; text-align: right; color: var(--admin-text-secondary); margin-top: 5px;">${c.fecha}</div>
        `;
        container.appendChild(card);
    });
};

window.setClaimStatus = async function(newStatus) {
    const claim = window.claims.find(c => c.id === window.selectedClaimId);
    if (!claim) return;

    let payload = { estado: newStatus };

    if (newStatus === 'vinculated') {
        const manualId = prompt('Ingrese el ID numérico del reclamo original al que desea vincularlo:');
        if (!manualId) return; 
        payload.linked_to = parseInt(manualId);
    }

    try {
        const response = await fetch(`/requests/update-status/${window.selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            claim.estado = newStatus;
            if (payload.linked_to) claim.linked_to = payload.linked_to;
            claim.suggested_duplicate_id = null; 
            
            window.selectClaim(window.selectedClaimId);
            window.updateStats();
            window.applyTemplate(newStatus);
            window.loadClaimsList();
        } else {
            alert('Error al actualizar el estado en el servidor.');
        }
    } catch (err) {
        console.error("Error al actualizar estado:", err);
    }
};

window.resolveDuplicate = async function(isAccepted, duplicateId = null) {
    const claim = window.claims.find(c => c.id === window.selectedClaimId);
    if (!claim) return;

    try {
        const payload = isAccepted ? { estado: 'vinculated', linked_to: duplicateId } : { ignore_suggestion: true };
        
        const response = await fetch(`/requests/update-status/${window.selectedClaimId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            if (isAccepted) {
                claim.estado = 'vinculated';
                claim.linked_to = duplicateId;
            }
            claim.suggested_duplicate_id = null;
            
            window.selectClaim(window.selectedClaimId);
            window.updateStats();
            window.loadClaimsList();
        } else {
            alert('Error al actualizar la sugerencia.');
        }
    } catch (err) {
        console.error(err);
    }
};

window.loadStatusesFromServer = async function() {
    try {
        const response = await fetch('/api/request-statuses');
        if (response.ok) {
            const result = await response.json();
            window.requestStatuses = result.data;
        }
    } catch (err) {
        console.error("Error al cargar estados:", err);
    }
};

window.loadClaimsFromServer = async function() {
    if (window.requestStatuses.length === 0) {
        await window.loadStatusesFromServer();
    }

    // Poblar el selector de estados dinámicamente desde la BD
    const statusSelect = document.getElementById('filter-claim-status');
    if (statusSelect && statusSelect.options.length <= 1) {
        window.requestStatuses.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.slug;
            opt.textContent = s.status_name;
            statusSelect.appendChild(opt);
        });
    }

    try {
        const response = await fetch('/requests', {
            headers: {
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            const result = await response.json();
            window.claims = result.data;
            window.updateStats();
            window.loadClaimsList();

            // Poblar el selector de categorías dinámicamente desde los reclamos cargados
            const catSelect = document.getElementById('filter-claim-category');
            if (catSelect && catSelect.options.length <= 1) {
                const uniqueCategories = [...new Set(window.claims.map(c => c.categoria))];
                uniqueCategories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat;
                    opt.textContent = cat;
                    catSelect.appendChild(opt);
                });
            }

            if (window.selectedClaimId) {
                window.selectClaim(window.selectedClaimId);
            }
        }
    } catch (err) {
        console.error("Error al cargar reclamos del servidor:", err);
    }
};
