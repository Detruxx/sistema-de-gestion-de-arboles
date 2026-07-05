/**
 * Interfaz (Formulario Reclamos): Lógica de validación y control visual del formulario paso a paso de reclamos.
 */

export function populateRequestTypes(types) {
    const selectTipoReclamo = document.getElementById('tipo-reclamo');
    if (!selectTipoReclamo) return;

    selectTipoReclamo.innerHTML = '<option value="">Seleccione una opcion...</option>';

    types.forEach(type => {
        const option = document.createElement('option');
        option.value = type.id;
        option.textContent = type.task_description;
        selectTipoReclamo.appendChild(option);
    });
}

export function setSeleccionArbolUI(arbol) {
    const inputDireccion = document.getElementById('direccion');
    const banner = document.getElementById('selected-tree-banner');
    const bannerText = document.getElementById('selected-tree-text');
    const helpText = document.getElementById('arbol-id-help');

    if (arbol) {
        if (inputDireccion) {
            inputDireccion.value = arbol.direccion;
            inputDireccion.readOnly = true;
            inputDireccion.classList.add('readonly-input');
        }

        if (banner && bannerText) {
            banner.style.display = 'flex';
            bannerText.innerHTML = `Estás registrando un reclamo para el árbol <strong>ID #${arbol.id} (${arbol.especie})</strong> ubicado en <strong>${arbol.direccion}</strong>.`;
        }
        if (helpText) helpText.style.display = 'none';
    } else {
        if (inputDireccion) {
            inputDireccion.readOnly = false;
            inputDireccion.classList.remove('readonly-input');
        }

        if (banner && bannerText) {
            banner.style.display = 'none';
            bannerText.textContent = '';
        }
    }
}

// Lógica de cambio de pestañas
export function initTabSwitching() {
    window.switchTab = function (tabName) {
        // Ocultar todos los contenidos
        document.querySelectorAll('.tab-content').forEach(el => {
            el.style.display = 'none';
        });
        // Mostrar el contenido seleccionado
        const selectedContent = document.getElementById(`section-${tabName}`);
        if (selectedContent) selectedContent.style.display = 'block';

        // Alternar clases activas en los botones de pestañas
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        const activeBtn = document.getElementById(`tab-btn-${tabName}`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
    };
}

export function renderTrackingResult(claim, requestStatuses) {
    const dirEl = document.getElementById('track-direccion');
    if (dirEl) dirEl.textContent = claim.direccion;

    const catEl = document.getElementById('track-categoria');
    if (catEl) catEl.textContent = `${claim.categoria} (${claim.fecha})`;

    // Texto de respuesta
    const replyTextEl = document.getElementById('track-admin-reply');
    const adminReplyBox = document.getElementById('admin-reply-box');
    if (claim.respuesta_admin) {
        if (replyTextEl) replyTextEl.textContent = claim.respuesta_admin;
        if (adminReplyBox) adminReplyBox.style.borderColor = 'var(--living-moss)';
    } else {
        if (replyTextEl) replyTextEl.textContent = "La solicitud fue recibida correctamente. Aún no se ha redactado una respuesta oficial por los inspectores.";
        if (adminReplyBox) adminReplyBox.style.borderColor = '#e5e7eb';
    }

    // Renderizar la barra de progreso
    const container = document.getElementById('dynamic-stepper-container');
    if (container) {
        // Filtramos solo los estados lineales (los que tienen secuencia definida)
        const linearSteps = requestStatuses.filter(s => s.sequence !== null).sort((a, b) => a.sequence - b.sequence);

        // Determinamos el estado actual
        const currentState = requestStatuses.find(s => s.slug === claim.estado);
        const isTerminalException = currentState && currentState.is_terminal && currentState.sequence === null;

        // Obtenemos la secuencia en la que nos encontramos
        let currentSeq = 0;
        if (!isTerminalException && currentState) {
            currentSeq = currentState.sequence;
        }

        let html = '<div class="track-step-line" id="track-step-line"></div>';

        linearSteps.forEach((step) => {
            const isActive = step.sequence === currentSeq;
            const isPassed = step.sequence < currentSeq;

            let bgNum = '#e5e7eb'; // Gris por defecto
            let colorNum = '#9ca3af';
            let colorLbl = '#9ca3af';
            let fontLbl = '500';
            let borderNum = '#e5e7eb';

            let labelText = step.status_name;
            let numText = step.sequence;

            if (isTerminalException) {
                if (step.sequence === 1) {
                    bgNum = currentState.color; // Color BD (Rojo o Magenta)
                    borderNum = currentState.color;
                    colorNum = '#fff';
                    colorLbl = currentState.color; // Texto BD
                    labelText = currentState.status_name;
                    numText = currentState.slug === 'denied' ? '✖' : '●'; // Cruz o Punto
                }
            } else if (isActive) {
                bgNum = '#166534'; // Verde oscuro bolita actual
                borderNum = '#166534';
                colorNum = '#ffffff';
                colorLbl = currentState.color; // Texto hereda color BD (Violeta, azul, etc)
                fontLbl = '700';
            } else if (isPassed) {
                bgNum = '#e5e7eb'; // Gris claro bolitas pasadas
                borderNum = '#e5e7eb';
                colorNum = '#9ca3af';
                colorLbl = '#9ca3af';
            }

            html += `
                <div class="track-step-item" id="step-${step.slug}">
                    <div class="step-num ${isTerminalException && step.sequence === 1 && currentState.slug === 'denied' ? 'is-denied' : ''}" style="background:${bgNum}; border-color:${borderNum}; color:${colorNum}">${numText}</div>
                    <span class="step-lbl" style="color:${colorLbl}; font-weight:${fontLbl}">${labelText}</span>
                </div>
            `;
        });

        container.innerHTML = html;

        // Actualizar porcentaje de la línea
        const line = document.getElementById('track-step-line');
        if (line) {
            let progressPercent = 0;
            if (!isTerminalException && currentSeq > 1) {
                progressPercent = ((currentSeq - 1) / (linearSteps.length - 1)) * 100;
            }
            let lineBg = '#166534'; // Mismo verde oscuro que la pelotita actual
            if (isTerminalException) {
                lineBg = currentState.color;
                progressPercent = 0;
            }
            line.style.background = `linear-gradient(to right, ${lineBg} ${progressPercent}%, #e5e7eb ${progressPercent}%)`;
        }
    }

    const resultDiv = document.getElementById('track-result');
    if (resultDiv) resultDiv.style.display = 'block';
}

export function showTrackError() {
    const errorDiv = document.getElementById('track-error');
    if (errorDiv) errorDiv.style.display = 'block';
}

export function hideTrackMessages() {
    const errorDiv = document.getElementById('track-error');
    const resultDiv = document.getElementById('track-result');
    if (errorDiv) errorDiv.style.display = 'none';
    if (resultDiv) resultDiv.style.display = 'none';
}
