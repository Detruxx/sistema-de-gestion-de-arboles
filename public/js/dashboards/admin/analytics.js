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
            renderSmartAlerts(result.data.alerts.alerts);
            renderProcessAlerts(result.data.alerts.process_alerts);
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
 * Renderiza las tarjetas de alertas inteligentes (originales).
 */
function renderSmartAlerts(alerts) {
    const container = document.getElementById('smart-alerts-container');
    container.innerHTML = '';

    if (!alerts || alerts.length === 0) {
        container.innerHTML = `
            <div style="padding: 20px; background: white; border-radius: 12px; border: 1px solid var(--admin-border); border-left: 4px solid #9ca3af; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <p style="color: #666; font-size: 0.95rem;">No hay hallazgos automáticos en este momento.</p>
            </div>`;
        return;
    }

    alerts.forEach(alert => {
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
            titleColor = '#b45309';
        } else if (alert.type === 'success') {
            dotClass = 'activity-dot-success';
            borderColor = 'var(--admin-success)';
            titleColor = 'var(--admin-success)';
        } else if (alert.type === 'info') {
            dotClass = 'activity-dot-info';
            borderColor = '#3b82f6';
            titleColor = '#2563eb';
        }

        const alertHtml = `
            <div style="padding: 20px; background: white; border-radius: 12px; border: 1px solid var(--admin-border); border-left: 4px solid ${borderColor}; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: flex-start; gap: 12px; transition: transform 0.2s ease;">
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
 * Renderiza las tarjetas de auditoría de procesos.
 */
function renderProcessAlerts(alerts) {
    const container = document.getElementById('process-alerts-container');
    container.innerHTML = '';

    if (!alerts || alerts.length === 0) {
        container.innerHTML = `
            <div style="padding: 20px; background: white; border-radius: 12px; border: 1px solid var(--admin-border); border-left: 4px solid #9ca3af; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <p style="color: #666; font-size: 0.95rem;">No hay hallazgos operativos que reportar en este momento.</p>
            </div>`;
        return;
    }

    alerts.forEach(alert => {
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
            titleColor = '#b45309';
        } else if (alert.type === 'success') {
            dotClass = 'activity-dot-success';
            borderColor = 'var(--admin-success)';
            titleColor = 'var(--admin-success)';
        }

        const alertHtml = `
            <div style="padding: 20px; background: white; border-radius: 12px; border: 1px solid var(--admin-border); border-left: 4px solid ${borderColor}; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: flex-start; gap: 12px; transition: transform 0.2s ease;">
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

        if (result.status === 'success' && result.data && result.data.results) {
            const resultsContainer = document.getElementById('custom-query-results');
            resultsContainer.style.display = 'block';

            if (exportType === 'table') {
                renderQueryTable(result.data, resultsContainer);
            } else if (exportType === 'chart') {
                renderQueryChart(result.data, resultsContainer);
            } else if (exportType === 'csv') {
                downloadCSV(result.data);
                
                // Mostrar alerta de éxito
                const banner = document.getElementById('notification-banner');
                document.getElementById('notification-text').innerText = 
                    'Archivo CSV generado y descargado con éxito.';
                banner.style.display = 'flex';
                setTimeout(() => { banner.style.display = 'none'; }, 4000);
            }
        } else if (result.status === 'error') {
            alert(result.message || "Ocurrió un error al procesar la consulta.");
        } else {
            throw new Error("Formato de respuesta inválido");
        }

    } catch (error) {
        console.error("Error al generar consulta", error);
        alert("Ocurrió un error inesperado de conexión al procesar la consulta.");
    }
}

/**
 * Renderiza los resultados en formato Tabla HTML
 */
function renderQueryTable(data, container) {
    const results = data.results;
    
    let html = `
        <h4 style="margin-bottom: 20px; color: var(--admin-text); font-family: var(--font-display);">Resultados de la Consulta (Formato Tabla)</h4>
        <div style="overflow-x: auto; border-radius: 8px; border: 1px solid var(--admin-border);">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-family: var(--font-base);">
                <thead>
                    <tr style="background-color: rgba(29, 138, 87, 0.05); color: var(--admin-accent); border-bottom: 2px solid var(--admin-border);">
                        <th style="padding: 15px; font-weight: 600;">Grupo Evaluado</th>
                        <th style="padding: 15px; font-weight: 600;">Resultado (Valor)</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (results.length === 0) {
        html += `<tr><td colspan="2" style="padding: 20px; text-align: center; color: #6b7280;">No se encontraron registros para esta consulta.</td></tr>`;
    } else {
        results.forEach(row => {
            // Manejar valores nulos o vacíos amigablemente
            const grupo = (row.grupo === null || row.grupo === '') ? 'Desconocido / Sin Clasificar' : row.grupo;
            html += `
                <tr style="border-bottom: 1px solid var(--admin-border);">
                    <td style="padding: 15px; color: var(--admin-text); font-weight: 500;">${grupo}</td>
                    <td style="padding: 15px; color: #4b5563;">${row.valor}</td>
                </tr>
            `;
        });
    }

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
}

/**
 * Renderiza los resultados en un gráfico de barras (Chart.js)
 */
let customQueryChartInstance = null;

function renderQueryChart(data, container) {
    const results = data.results;
    
    // Inyectar el canvas
    container.innerHTML = `
        <h4 style="margin-bottom: 20px; color: var(--admin-text); font-family: var(--font-display);">Resultados de la Consulta (Formato Gráfico)</h4>
        <div style="position: relative; height: 350px; width: 100%; background: white; border-radius: 12px; border: 1px solid var(--admin-border); padding: 20px;">
            <canvas id="customQueryCanvas"></canvas>
        </div>
    `;

    const ctx = document.getElementById('customQueryCanvas').getContext('2d');
    
    if (customQueryChartInstance) {
        customQueryChartInstance.destroy();
    }

    // Preparar datos
    const labels = results.map(row => (row.grupo === null || row.grupo === '') ? 'Sin Clasificar' : String(row.grupo));
    const values = results.map(row => parseFloat(row.valor));

    customQueryChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad / Valor',
                data: values,
                backgroundColor: 'rgba(29, 138, 87, 0.8)', // Verde Arbórea
                borderColor: '#1d8a57',
                borderWidth: 1,
                borderRadius: 4,
                maxBarThickness: 60 // Evita que las barras sean gigantes cuando hay pocos datos
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
                    cornerRadius: 8
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                x: { grid: { display: false } }
            }
        }
    });
}

/**
 * Genera un archivo CSV en memoria y fuerza su descarga
 */
function downloadCSV(data) {
    const results = data.results;
    
    if (results.length === 0) {
        alert("No hay datos para exportar.");
        return;
    }

    // Cabeceras del CSV
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Grupo,Valor\n";

    // Filas
    results.forEach(row => {
        const grupo = (row.grupo === null || row.grupo === '') ? 'Sin Clasificar' : String(row.grupo);
        const valor = row.valor;
        // Escapar comillas dobles y comas si existieran en el texto
        const safeGrupo = `"${grupo.replace(/"/g, '""')}"`;
        csvContent += `${safeGrupo},${valor}\n`;
    });

    // Crear un enlace virtual para descargar
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    
    // Nombre de archivo dinámico basado en la fecha
    const dateStr = new Date().toISOString().slice(0, 10);
    link.setAttribute("download", `Reporte_Arborea_${dateStr}.csv`);
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
