@extends('layouts.app')

@section('title', 'Panel de Control Administrador | Arborea')
@section('navbar-class', 'scrolled')

@section('styles')
    <!-- Estilos adicionales locales para complementar -->
    <link rel="stylesheet" href="{{ asset('css/dashboards/dynamic-status.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


@endsection
@section('content')
<div class="admin-dashboard-container">
    
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <!-- Toggle button only visible on mobile/tablet -->
        <button class="sidebar-toggle" onclick="toggleAdminSidebar()">
            <span>Menú del Panel Comunal</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>

        <div class="sidebar-menu">
            <h3 class="sidebar-menu-title">Panel Comunal</h3>
            <button class="sidebar-btn active" onclick="window.showAdminModule('resumen')" id="menu-resumen">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9"></rect>
                    <rect x="14" y="3" width="7" height="5"></rect>
                    <rect x="14" y="12" width="7" height="9"></rect>
                    <rect x="3" y="16" width="7" height="5"></rect>
                </svg>
                Resumen
            </button>

                <!-- Opciones exclusivas de Administrador -->
                <button class="sidebar-btn" onclick="window.showAdminModule('vecinos')" id="menu-vecinos">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    Gestión de Vecinos
                </button>
                <button class="sidebar-btn" onclick="window.showAdminModule('inspectores')" id="menu-inspectores">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Inspectores
                </button>
                <button class="sidebar-btn" onclick="window.showAdminModule('empresas')" id="menu-empresas">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                    Empresas Contratistas
                </button>
                <button class="sidebar-btn" onclick="showModule('estadisticas')" id="menu-estadisticas">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    Inteligencia & Analítica
                </button>
            
            <h3 class="sidebar-menu-title mt-25">Herramientas</h3>
            <a href="/mapa" target="_blank" class="sidebar-btn sidebar-btn-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                    <line x1="8" y1="2" x2="8" y2="18"></line>
                    <line x1="16" y1="6" x2="16" y2="22"></line>
                </svg>
                Abrir Mapa de Arbolado
            </a>
        </div>
        
        <div class="sidebar-footer">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-btn sidebar-btn-link sidebar-btn-full">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-main-panel">
        
        <!-- MODULE: RESUMEN (Admin o Inspector) -->
        <section id="module-resumen" class="dashboard-module active">
                <div class="admin-header-section">
                    <div>
                        <h2>Panel de Control General (Admin)</h2>
                        <p>Monitoreo y estadísticas globales de la Comuna 13</p>
                    </div>
                </div>

                <!-- Stats Grid para Admin -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Vecinos Registrados</h4>
                            <p id="stat-total-residents">...</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-primary">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Inspectores Activos</h4>
                            <p id="stat-total-inspectors">...</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-success">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Empresas Socias</h4>
                            <p id="stat-total-companies">...</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-warning">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Postulaciones Pendientes</h4>
                            <p id="stat-pending-postulations">...</p>
                        </div>
                    </div>
                </div>

            <!-- Recent Activity Panel -->
            <div class="detail-panel">
                <h3 class="sidebar-menu-title mt-25" style="font-family: var(--font-display); margin-bottom: 20px; color: var(--admin-accent);">Resumen Operativo Reciente</h3>
                <div class="activity-list" id="recent-activity-container">
                    <!-- Dinámico -->
                    <div class="activity-item">
                        <span class="activity-dot activity-dot-info"></span>
                        <div>
                            <p class="activity-title">Inspección de Jacarandá programada para el lunes</p>
                            <p class="activity-desc">Hace 2 horas - Comuna 13</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-dot activity-dot-success"></span>
                        <div>
                            <p class="activity-title">Orden de Trabajo concluida por Mantenimiento Verde S.A.</p>
                            <p class="activity-desc">Ayer - Dirección: Av. Cabildo 2800</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

            <!-- MODULE: VECINOS (Exclusivo Admin) -->
            <section id="module-vecinos" class="dashboard-module">
                <div class="admin-header-section">
                    <div>
                        <h2>Gestión de Vecinos</h2>
                        <p>Lista de ciudadanos registrados. Puedes generar baneos/suspensiones temporales o promoverlos a inspectores.</p>
                    </div>
                </div>

                <div class="inventory-filter-bar">
                    <div class="inventory-filter-group" style="flex: 1 1 300px;">
                        <label for="search-residents">Buscar vecino por nombre o email</label>
                        <input type="text" id="search-residents" placeholder="Ej. Gómez, vecino@gmail.com..." oninput="filterresidents()">
                    </div>
                </div>

                <div class="split-layout">
                    <div class="list-panel">
                        <div class="items-list" id="residents-list-container">
                            <!-- Loaded via JS -->
                        </div>
                    </div>
                    <div class="detail-panel" id="resident-detail-panel">
                        <div class="empty-state-panel">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                                <circle cx="12" cy="12" r="10"></circle><path d="M12 8v4M12 16h.01"></path>
                            </svg>
                            <p>Selecciona un vecino de la lista para gestionar su estado de cuenta o ascenderlo.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MODULE: INSPECTORES (Exclusivo Admin) -->
            <section id="module-inspectores" class="dashboard-module">
                <div class="admin-header-section">
                    <div>
                        <h2>Plantel de Inspectores</h2>
                        <p>Control y gestión del personal técnico comunal asignado.</p>
                    </div>
                </div>

                <div class="inventory-filter-bar">
                    <div class="inventory-filter-group" style="flex: 1 1 300px;">
                        <label for="search-inspectors">Buscar inspector</label>
                        <input type="text" id="search-inspectors" placeholder="Buscar por nombre o correo..." oninput="filterInspectors()">
                    </div>
                </div>

                <div class="split-layout">
                    <div class="list-panel">
                        <div class="items-list" id="inspectors-list-container">
                            <!-- Loaded via JS -->
                        </div>
                    </div>
                    <div class="detail-panel" id="inspector-detail-panel">
                        <div class="empty-state-panel">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                                <circle cx="12" cy="12" r="10"></circle><path d="M12 8v4M12 16h.01"></path>
                            </svg>
                            <p>Selecciona un inspector para ver su ficha técnica o removerle el rol.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MODULE: EMPRESAS CONTRATISTAS (Exclusivo Admin) -->
            <section id="module-empresas" class="dashboard-module">
                <div class="admin-header-section">
                    <div>
                        <h2>Empresas Contratistas</h2>
                        <p>Directorio de empresas registradas para realizar servicios de arbolado y mantenimiento.</p>
                    </div>
                </div>

                <div class="inventory-filter-bar">
                    <div class="inventory-filter-group" style="flex: 1 1 300px;">
                        <label for="search-companies">Buscar empresa por nombre o cuit</label>
                        <input type="text" id="search-companies" placeholder="Ej. Podas S.A., 30-12345678-9..." oninput="filtercompanies()">
                    </div>
                </div>

                <div class="split-layout">
                    <div class="list-panel">
                        <div class="items-list" id="companies-list-container">
                            <!-- Loaded via JS -->
                        </div>
                    </div>
                    <div class="detail-panel" id="company-detail-panel">
                        <div class="empty-state-panel">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <p>Selecciona una empresa para ver sus métricas, historial de trabajos y asignar nuevas órdenes.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MODULE: ESTADISTICAS E INTELIGENCIA (Exclusivo Admin) -->
            <section id="module-estadisticas" class="dashboard-module" style="overflow: visible !important;">
                <div class="admin-header-section">
                    <div>
                        <h2>Inteligencia y Analítica Operativa</h2>
                        <p>Descubre patrones, optimiza recursos y toma decisiones basadas en cruces de datos en tiempo real.</p>
                    </div>
                </div>

                <!-- Alertas Inteligentes (Actionable Intelligence) -->
                <div class="detail-panel" style="margin-bottom: 30px; border-left: 4px solid var(--admin-accent);">
                    <h3 class="sidebar-menu-title mt-25" style="font-family: var(--font-display); margin-bottom: 20px; color: var(--admin-accent);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 8px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        Alertas y Hallazgos Automáticos
                    </h3>
                    <div class="activity-list" id="smart-alerts-container">
                        <!-- Loaded via JS Skeleton -->
                        <div class="activity-item">
                            <span class="activity-dot activity-dot-warning"></span>
                            <div>
                                <p class="activity-title" style="font-weight: 600;">Cargando motor de inteligencia...</p>
                                <p class="activity-desc">Analizando correlaciones entre especies y reclamos...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grilla de Gráficos -->
                <div class="split-layout" style="gap: 30px;">
                    <!-- Panel Izquierdo: Gráfico de Tendencia -->
                    <div class="list-panel" style="flex: 2; padding: 25px; background: white; border-radius: 12px; border: 1px solid var(--admin-border); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <h4 style="margin-bottom: 20px; color: var(--admin-text); font-family: var(--font-display);">Evolución de Reclamos (Últimos 6 meses)</h4>
                        <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>

                    <!-- Panel Derecho: Gráfico de Distribución -->
                    <div class="detail-panel" style="flex: 1; min-width: 300px; padding: 25px; background: white; border-radius: 12px; border: 1px solid var(--admin-border); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <h4 style="margin-bottom: 20px; color: var(--admin-text); font-family: var(--font-display);">Salud del Arbolado</h4>
                        <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center; align-items: center;">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Explorador de Datos Personalizado -->
                <div class="admin-card" style="margin-top: 30px; overflow: visible !important;">
                    <div class="admin-card-body" style="overflow: visible !important; padding-bottom: 120px;">
                        <h3 style="margin-bottom: 20px; color: var(--admin-accent); display: flex; align-items: center; gap: 10px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            EXPLORADOR DE DATOS PERSONALIZADO
                        </h3>
                        <form id="custom-query-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 20px; overflow: visible !important;">
                        <div class="admin-form-group">
                            <label for="query-model">Modelo a Analizar</label>
                            <select id="query-model" class="custom-query-select" required>
                                <option value="trees">Árboles Registrados</option>
                                <option value="requests">Reclamos y Solicitudes</option>
                                <option value="work_orders">Órdenes de Trabajo</option>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label for="query-metric">Métrica a Calcular</label>
                            <select id="query-metric" class="custom-query-select" required>
                                <option value="count">Cantidad Total (Count)</option>
                                <option value="avg_time">Tiempo Promedio</option>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label for="query-groupby">Agrupar por</label>
                            <select id="query-groupby" class="custom-query-select" required>
                                <option value="status">Estado</option>
                                <option value="species">Especie</option>
                                <option value="month">Mes</option>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label for="query-daterange">Filtro de Tiempo</label>
                            <select id="query-daterange" class="custom-query-select" required>
                                <option value="all">Histórico Completo</option>
                                <option value="30days">Últimos 30 días</option>
                                <option value="this_year">Este año</option>
                            </select>
                        </div>
                    </form>
                    <div style="text-align: right; margin-top: 30px;">
                        <button type="button" class="btn-primary" onclick="openQueryExportModal()" style="padding: 14px 32px; font-size: 1.1rem; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(29,138,87,0.2);">Generar Consulta</button>
                    </div>
                    </div> <!-- Cierre admin-card-body -->
                </div> <!-- Cierre admin-card -->
            </section>

    </main>

    <!-- Modal Registrar Nuevo Árbol -->
    <div id="create-tree-modal" class="admin-modal-overlay">
        <div class="admin-modal-container">
            <div class="admin-modal-header">
                <h3>Registrar Nuevo Árbol</h3>
                <button type="button" class="admin-modal-close" onclick="closeCreateTreeModal()">&times;</button>
            </div>
            <form id="create-tree-form" onsubmit="submitCreateTree(event)">
                <div class="admin-modal-body">
                    <!-- Leaflet map to pick coordinates -->
                    <div class="admin-form-group">
                        <label>Selecciona la ubicación en el mapa (Arrastra o haz clic)</label>
                        <div id="admin-tree-map-canvas" class="admin-tree-map"></div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="new-tree-lat">Latitud</label>
                            <input type="number" step="any" id="new-tree-lat" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="new-tree-lng">Longitud</label>
                            <input type="number" step="any" id="new-tree-lng" required>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="new-tree-calle">Calle</label>
                            <input type="text" id="new-tree-calle" placeholder="Ej. Av. Cabildo" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="new-tree-nro">Número</label>
                            <input type="number" id="new-tree-nro" placeholder="Ej. 2950" required>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="new-tree-especie">Especie</label>
                            <select id="new-tree-especie" required>
                                <option value="Jacarandá">Jacarandá</option>
                                <option value="Ceibo">Ceibo</option>
                                <option value="Fresno">Fresno</option>
                                <option value="Palo Borracho">Palo Borracho</option>
                                <option value="Tilo">Tilo</option>
                                <option value="Liquidámbar">Liquidámbar</option>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label for="new-tree-estado">Estado de Salud</label>
                            <select id="new-tree-estado" required>
                                <option value="Saludable">Saludable</option>
                                <option value="Enfermo">Enfermo</option>
                                <option value="Dañado">Dañado</option>
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="new-tree-altura">Altura (metros)</label>
                            <input type="number" step="0.1" id="new-tree-altura" placeholder="Ej. 8.5" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="new-tree-circunferencia">Circunferencia del Tronco (cm)</label>
                            <input type="number" id="new-tree-circunferencia" placeholder="Ej. 95" required>
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label for="new-tree-edad">Edad Estimada (años)</label>
                        <input type="number" id="new-tree-edad" placeholder="Ej. 12" required>
                    </div>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeCreateTreeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Registrar Ejemplar</button>
                </div>
            </form>
        </div>
    </div>

<!-- Modal para Detalle de Reclamo -->
<div id="claim-detail-modal" class="admin-modal-overlay" style="display: none; z-index: 1050;">
    <div class="admin-modal-container claim-modal-large" style="max-width: 90%; width: 1200px; height: 90vh; display: flex; flex-direction: column;">
        <div class="admin-modal-header" style="padding: 15px 20px; border-bottom: 1px solid var(--admin-border);">
            <h3 style="margin: 0; color: var(--admin-accent);">Detalle y Gestión de Reclamo</h3>
            <button type="button" class="admin-modal-close" onclick="closeClaimDetailModal()" style="background: none; border: none; font-size: 1.8rem; line-height: 1; cursor: pointer; color: var(--admin-text-secondary);">&times;</button>
        </div>
        <div class="admin-modal-body" id="claim-modal-body-content" style="flex: 1; overflow-y: auto; padding: 20px;">
            <!-- Cargado dinámicamente por JS -->
        </div>
    </div>
</div>

<!-- Modal para Exportación de Consultas Personalizadas -->
<!-- Modal para Exportación de Consultas Personalizadas -->
<style>
    /* Ocultar selects nativos */
    .admin-form-group select.custom-query-select {
        display: none !important;
    }

    /* Estilos del Custom Select */
    .custom-select-wrapper {
        position: relative;
        user-select: none;
        width: 100%;
    }
    .custom-select-trigger {
        padding: 10px 14px; /* Contenedores un poco más chicos */
        border: 2px solid var(--admin-accent);
        border-radius: 8px;
        background-color: #ffffff;
        color: #374151;
        font-family: var(--font-base);
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }
    .custom-select-trigger:hover {
        box-shadow: 0 4px 12px rgba(29, 138, 87, 0.15);
    }
    .custom-select-trigger.open {
        background-color: var(--admin-accent);
        color: #ffffff;
    }
    .custom-select-trigger.open svg {
        stroke: #ffffff;
        transform: rotate(180deg);
    }
    .custom-select-trigger svg {
        transition: transform 0.3s ease, stroke 0.3s ease;
        stroke: var(--admin-accent);
    }

    /* El menú desplegable (opciones) basado en la estética de la ventana emergente */
    .custom-options {
        position: absolute;
        display: block;
        top: 100%;
        left: 0;
        right: 0;
        background: #ffffff; /* Fondo blanco */
        border: 2px solid var(--admin-accent); /* Borde verde visible */
        border-radius: 10px; /* Curva suave */
        margin-top: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-10px);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 100;
        overflow: hidden;
        padding: 5px; /* Pequeño espaciado interno para las opciones */
    }
    .custom-select-wrapper.open .custom-options {
        opacity: 1;
        visibility: visible;
        pointer-events: all;
        transform: translateY(0);
    }
    .custom-option {
        padding: 10px 14px;
        margin-bottom: 5px;
        color: var(--admin-accent); /* Texto verde sobre fondo blanco */
        background-color: #ffffff;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        border-radius: 8px; /* Opciones redondeadas internamente */
        transition: all 0.2s ease;
    }
    .custom-option:last-child {
        margin-bottom: 0;
    }
    /* El hover idéntico a la ventana emergente: fondo verde, letra blanca */
    .custom-option:hover, .custom-option.selected {
        background-color: var(--admin-accent);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(29, 138, 87, 0.15);
    }
    
    /* Forzar que los labels sean oscuros, legibles y estén centrados */
    #module-estadisticas .admin-form-group label {
        color: #1f2937 !important;
        font-weight: 600 !important;
        text-align: center !important;
        display: block !important; /* Necesario para que text-align funcione */
        margin-bottom: 8px !important;
    }

    /* Estilos para las opciones del modal (Fondo blanco, bordes y texto verde) */
    #query-export-modal .admin-modal-container {
        background-color: #ffffff !important;
        border: 2px solid var(--admin-accent) !important;
    }
    #query-export-modal .admin-modal-header h3 {
        color: var(--admin-accent) !important;
    }
    #query-export-modal .admin-modal-close {
        color: #6b7280 !important;
    }
    
    #query-export-modal button.export-option-btn {
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        padding: 16px 20px !important;
        background-color: #ffffff !important;
        border: 2px solid var(--admin-accent) !important;
        color: var(--admin-accent) !important;
        border-radius: 10px !important;
        font-family: var(--font-base) !important;
        font-size: 1.05rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        text-align: left !important;
    }
    #query-export-modal button.export-option-btn svg {
        margin-right: 12px !important;
        transition: transform 0.2s ease !important;
        stroke: var(--admin-accent) !important;
    }
    #query-export-modal button.export-option-btn:hover {
        background-color: var(--admin-accent) !important;
        color: #ffffff !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(29, 138, 87, 0.2) !important;
    }
    #query-export-modal button.export-option-btn:hover svg {
        transform: scale(1.1) !important;
        stroke: #ffffff !important;
    }
</style>

<script>
    // Inicializador de Custom Selects para el módulo de analíticas
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionamos todos los selects nativos que queremos transformar
        const selects = document.querySelectorAll('.custom-query-select');
        
        selects.forEach(select => {
            // Creamos el contenedor padre
            const wrapper = document.createElement('div');
            wrapper.className = 'custom-select-wrapper';
            
            // Movemos el select nativo dentro del contenedor (está oculto por CSS)
            select.parentNode.insertBefore(wrapper, select);
            wrapper.appendChild(select);

            // Creamos el botón visible (trigger)
            const trigger = document.createElement('div');
            trigger.className = 'custom-select-trigger';
            trigger.innerHTML = `<span>${select.options[select.selectedIndex].text}</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>`;
            wrapper.appendChild(trigger);

            // Creamos el contenedor del menú desplegable con curva
            const customOptions = document.createElement('div');
            customOptions.className = 'custom-options';
            
            // Llenamos el menú con las opciones del select nativo
            Array.from(select.options).forEach((option, index) => {
                const customOption = document.createElement('div');
                customOption.className = 'custom-option' + (index === select.selectedIndex ? ' selected' : '');
                customOption.dataset.value = option.value;
                customOption.textContent = option.text;
                
                // Acción al hacer clic en una opción (Fondo blanco, texto verde y selección)
                customOption.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Actualizamos el select original por detrás
                    select.value = this.dataset.value;
                    
                    // Actualizamos el texto del botón
                    trigger.querySelector('span').textContent = this.textContent;
                    
                    // Quitamos la clase selected de todos y se la ponemos a este
                    customOptions.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    // Cerramos el menú
                    wrapper.classList.remove('open');
                });
                
                customOptions.appendChild(customOption);
            });
            
            wrapper.appendChild(customOptions);

            // Acción al hacer clic en el botón (abrir/cerrar)
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                // Cerrar todos los demás que estén abiertos
                document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                    if (w !== wrapper) w.classList.remove('open');
                });
                wrapper.classList.toggle('open');
            });
        });

        // Si se hace clic fuera del select, cerrar cualquier menú abierto
        document.addEventListener('click', function() {
            document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                w.classList.remove('open');
            });
        });
    });
</script>

<div id="query-export-modal" class="admin-modal-overlay" style="display: none; z-index: 1060; background-color: rgba(0,0,0,0.6);">
    <div class="admin-modal-container" style="max-width: 500px; background: #ffffff; border: 3px solid var(--admin-primary); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div class="admin-modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 20px 25px;">
            <h3 style="color: var(--admin-primary); margin: 0; font-size: 1.4rem;">Exportar Resultados</h3>
            <button type="button" class="admin-modal-close" onclick="closeQueryExportModal()" style="color: #6b7280; font-size: 1.5rem;">&times;</button>
        </div>
        <div class="admin-modal-body" style="padding: 25px;">
            <p style="margin-bottom: 25px; color: #4b5563; font-size: 1.05rem; line-height: 1.5;">¿Cómo deseas visualizar o exportar los datos generados por tu consulta?</p>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <button type="button" class="export-option-btn" onclick="exportQuery('table')">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    Ver como Tabla de Resultados
                </button>
                <button type="button" class="export-option-btn" onclick="exportQuery('chart')">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    Generar Gráfico Dinámico
                </button>
                <button type="button" class="export-option-btn" onclick="exportQuery('csv')">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Descargar Excel (.CSV)
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Notification Banner -->
    <div id="notification-banner" class="notification-banner" style="display: none;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 8 12 12 16 14"></polyline>
        </svg>
        <span id="notification-text">Respuesta enviada con éxito.</span>
    </div>
</div>
@endsection
@section('footer')
    <!-- Se oculta el footer en el Panel de Administración para un estilo más limpio tipo dashboard -->
@endsection
@section('scripts')
    <script>
        window.currentUserRole = "{{ auth()->user()->role }}";
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script type="module" src="{{ asset('js/dashboards/admin/main.js') }}"></script>
@endsection



