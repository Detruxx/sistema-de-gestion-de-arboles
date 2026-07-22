/**
 * Módulo de Inteligencia y Analítica Operativa (Admin Dashboard)
 */

let trendChartInstance = null;
let distributionChartInstance = null;
let analyticsLoaded = false;

/**
 * Función principal que carga y renderiza las estadísticas.
 * Se llama cuando se abre el módulo #estadisticas.
 */
export async function loadAnalyticsModule() {
    // Evitar cargar múltiples veces si ya está cargado
    if (analyticsLoaded) return;
    
    try {
        const response = await fetch('/api/admin/analytics');
        const result = await response.json();

        if (result.status === 'success') {
            renderSmartAlerts(result.data.alerts);
            renderCharts(result.data.charts);
            analyticsLoaded = true;
        }
    } catch (error) {
        console.error('Error cargando las analíticas:', error);
        document.getElementById('smart-alerts-container').innerHTML = `
            <div class="activity-item">
                <span class="activity-dot activity-dot-danger"></span>
                <div>
                    <p class="activity-title" style="color: var(--admin-danger);">Error de conexión</p>
                    <p class="activity-desc">No se pudieron cargar los datos de inteligencia.</p>
                </div>
            </div>`;
    }
}

/**
 * Renderiza las tarjetas de alertas inteligentes cruzando datos.
 */
function renderSmartAlerts(alerts) {
    const container = document.getElementById('smart-alerts-container');
    container.innerHTML = '';

    if (!alerts || alerts.length === 0) {
        container.innerHTML = `
            <div style="padding: 20px; background: white; border-radius: 12px; border-left: 4px solid #9ca3af; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <p style="color: #666; font-size: 0.95rem;">No hay hallazgos automáticos que reportar en este momento.</p>
            </div>`;
        return;
    }

    alerts.forEach(alert => {
        // Map alert type to dot color class and border color
        let dotClass = 'activity-dot-info';
        let borderColor = 'var(--admin-accent)';
        let titleColor = 'var(--admin-accent)';

        if (alert.type === 'danger') {
            dotClass = 'activity-dot-danger';
            borderColor = 'var(--admin-danger)';
            titleColor = 'var(--admin-danger)';
        } else if (alert.type === 'warning') {
            dotClass = 'activity-dot-warning';
            borderColor = 'var(--admin-warning)';
            titleColor = '#b45309'; // Darker warning for text readability
        } else if (alert.type === 'success') {
            dotClass = 'activity-dot-success';
            borderColor = 'var(--admin-success)';
            titleColor = 'var(--admin-success)';
        }

        const alertHtml = `
            <div style="padding: 20px; background: white; border-radius: 12px; border-left: 4px solid ${borderColor}; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: flex-start; gap: 12px; transition: transform 0.2s ease;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="activity-dot ${dotClass}" style="margin:0;"></span>
                    <p style="font-weight: 700; font-size: 1.05rem; color: ${titleColor}; margin: 0;">${alert.title}</p>
                </div>
                <p style="font-size: 0.95rem; color: var(--admin-text-secondary); line-height: 1.5; margin: 0;">${alert.description}</p>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', alertHtml);
    });
}

/**
 * Renderiza los gráficos usando Chart.js
 */
function renderCharts(chartsData) {
    // 1. Configuración global para que combine con la estética del dashboard
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.color = '#4b5563';

    // Gráfico de Tendencia (Line Chart)
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    if (trendChartInstance) trendChartInstance.destroy();
    
    trendChartInstance = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: chartsData.requests_trend.labels,
            datasets: [{
                label: 'Reclamos Recibidos',
                data: chartsData.requests_trend.data,
                borderColor: '#1d8a57', // Verde principal
                backgroundColor: 'rgba(29, 138, 87, 0.1)',
                borderWidth: 3,
                tension: 0.4, // Curvas suaves
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#1d8a57',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: { size: 14, family: 'Inter' },
                    bodyFont: { size: 13, family: 'Inter' },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                x: { grid: { display: false } }
            }
        }
    });

    // Gráfico de Distribución de Salud (Doughnut Chart)
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    if (distributionChartInstance) distributionChartInstance.destroy();

    distributionChartInstance = new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: chartsData.tree_health.labels,
            datasets: [{
                data: chartsData.tree_health.data,
                backgroundColor: [
                    '#10b981', // Saludable (Verde claro)
                    '#f59e0b', // Enfermo (Naranja)
                    '#ef4444'  // Dañado (Rojo)
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%', // Grosor de la dona
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 13, family: 'Inter' }
                    }
                }
            }
        }
    });
}

/**
 * Lógica para el Generador de Consultas Personalizadas
 */

export function openQueryExportModal() {
    document.getElementById('query-export-modal').style.display = 'flex';
}

export function closeQueryExportModal() {
    document.getElementById('query-export-modal').style.display = 'none';
}

export async function exportQuery(exportType) {
    // 1. Recolectar datos del formulario
    const model = document.getElementById('query-model').value;
    const metric = document.getElementById('query-metric').value;
    const groupBy = document.getElementById('query-groupby').value;
    const dateRange = document.getElementById('query-daterange').value;

    const payload = {
        model, metric, groupBy, dateRange, exportType
    };

    try {
        // Realizar la petición POST simulada
        const response = await fetch('/api/admin/analytics/custom', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        // 3. Cerrar el modal
        closeQueryExportModal();

        // 4. Mostrar alerta de éxito (Mock)
        if (result.status === 'success') {
            const banner = document.getElementById('notification-banner');
            document.getElementById('notification-text').innerText = 
                `Consulta generada con éxito. Tipo de exportación: ${exportType.toUpperCase()}`;
            
            banner.style.display = 'flex';
            setTimeout(() => { banner.style.display = 'none'; }, 4000);
            
            // Aquí iría la lógica para renderizar la tabla, gráfico, o forzar descarga de CSV
            console.log("Datos generados:", result.data);
        }

    } catch (error) {
        console.error("Error al generar consulta", error);
        alert("Ocurrió un error al procesar la consulta personalizada.");
    }
}
