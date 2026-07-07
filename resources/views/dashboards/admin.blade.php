@extends('layouts.app')

@section('title', 'Panel de Control Administrador | TreeBA')
@section('navbar-class', 'scrolled')

@section('styles')
    <!-- Estilos adicionales locales para complementar -->
    <link rel="stylesheet" href="{{ asset('css/dashboards/dynamic-status.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>


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

            <!-- MODULE: EMPRESAS (Exclusivo Admin) -->
            <section id="module-empresas" class="dashboard-module">
                <div class="admin-header-section">
                    <div>
                        <h2>Empresas Contratistas</h2>
                        <p>Listado general de empresas asociadas, sus datos y costos generados.</p>
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
                                <circle cx="12" cy="12" r="10"></circle><path d="M12 8v4M12 16h.01"></path>
                            </svg>
                            <p>Selecciona una empresa contratista para ver su desempeño operativo y gastos acumulados.</p>
                        </div>
                    </div>
                </div>
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


