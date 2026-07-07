/**
 * UI Components Genéricos compartidos entre múltiples paneles (Inspector, Empresa, etc.)
 */

/**
 * Genera el HTML de las "bolitas de progreso" (Progress Steps)
 * @param {Array} steps - Array de objetos con { label: 'Nombre', sequence: 1, color: '#hex' }
 * @param {Number} currentSequence - La secuencia activa actual (1, 2, 3...)
 * @param {String} activeSlug - El slug o estado activo actual (opcional, por si la secuencia no es suficiente)
 * @returns {String} HTML string
 */
export function getProgressTrackerHtml(steps, currentSequence, activeSlug = null) {
    let html = `<div class="status-tracker-container">
                    <div class="status-steps">`;

    steps.forEach(s => {
        // Un step está completado si tiene una secuencia y es menor o igual a la actual
        const isCompleted = s.sequence && s.sequence <= currentSequence;
        // Está activo si su slug coincide (para claims) o si es exactamente el step actual (para jobs)
        const isActive = (activeSlug && activeSlug === s.slug) || (!activeSlug && s.sequence === currentSequence);

        if (s.sequence || isActive) {
            html += `
                <div class="status-step ${isCompleted ? 'completed' : ''} ${isActive ? 'active' : ''}">
                    <div class="step-circle" style="background-color: ${isActive ? s.color : ''}; border-color: ${isActive ? s.color : ''}">${s.sequence || '!'}</div>
                    <div class="step-label">${s.label}</div>
                </div>`;
        }
    });

    html += `       </div>
                </div>`;
    return html;
}
