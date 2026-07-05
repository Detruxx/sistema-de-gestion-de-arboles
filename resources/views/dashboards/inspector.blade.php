@extends('layouts.app')

@section('title', 'Panel de Control Administrador | TreeBA')
@section('navbar-class', 'scrolled')

@section('styles')
    <!-- Estilos adicionales locales para complementar -->
    <link rel="stylesheet" href="{{ asset('css/dashboards/dynamic-status.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <style>
        /* Garantizar matemáticamente la misma altura para inputs y botón */
        .inventory-filter-group select,
        .inventory-filter-group input {
            height: 40px !important;
            box-sizing: border-box !important;
        }
    </style>

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
            <button class="sidebar-btn active" onclick="showModule('resumen')" id="menu-resumen">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9"></rect>
                    <rect x="14" y="3" width="7" height="5"></rect>
                    <rect x="14" y="12" width="7" height="9"></rect>
                    <rect x="3" y="16" width="7" height="5"></rect>
                </svg>
                Resumen
            </button>

                <!-- Opciones de Inspector -->
                <button class="sidebar-btn" onclick="showModule('reclamos')" id="menu-reclamos">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Mensajes de Reclamos
                    <span id="unread-count-badge" class="badge-unread">0</span>
                </button>
                <button class="sidebar-btn" onclick="showModule('inventario')" id="menu-inventario">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    Inventario de Arbolado
                </button>
                <button class="sidebar-btn" onclick="showModule('pagos')" id="menu-pagos">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12" y2="18.01"></line>
                    </svg>
                    Pagos y Gastos
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
                        <h2>Panel de Control General (Inspector)</h2>
                        <p>Monitoreo y estadísticas de arbolado y solicitudes - Comuna 13</p>
                    </div>
                </div>

                <!-- Stats Grid para Inspector -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Reclamos Totales</h4>
                            <p id="stat-total-claims">...</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-warning">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                        <div class="stat-info">
                            <h4>En Curso</h4>
                            <p id="stat-pending-claims">...</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-success">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Resueltos</h4>
                            <p id="stat-resolved-claims">...</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-primary">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Árboles Registrados</h4>
                            <p id="stat-total-trees">...</p>
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

            <!-- MODULE: RECLAMOS (Exclusivo Inspector) -->
            <section id="module-reclamos" class="dashboard-module">
                <div class="admin-header-section">
                    <div>
                        <h2>Mensajes y Reclamos de Vecinos</h2>
                        <p>Gestiona las consultas, cambia los estados del proceso y responde directamente</p>
                    </div>
                </div>

                <!-- Filters Bar -->
                <div class="inventory-filter-bar">
                    <div class="inventory-filter-group" style="flex: 1 1 250px;">
                        <label for="search-claims">Buscar por vecino, dirección o ID</label>
                        <input type="text" id="search-claims" placeholder="Ej. Laura Gómez, REC-2026-001..." oninput="filterClaims()">
                    </div>
                    <div class="inventory-filter-group" style="flex: 1 1 180px;">
                        <label for="filter-claim-status">Estado</label>
                        <select id="filter-claim-status" onchange="filterClaims()">
                            <option value="">Todos los estados</option>
                        </select>
                    </div>
                    <div class="inventory-filter-group" style="flex: 1 1 200px;">
                        <label for="filter-claim-category">Categoría</label>
                        <select id="filter-claim-category" onchange="filterClaims()">
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                </div>

                <div class="split-layout">
                    <!-- Left Panel -->
                    <div class="list-panel">
                        <div class="items-list" id="claims-list-container">
                            <!-- Loaded via JS -->
                        </div>
                    </div>

                    <!-- Right Panel -->
                    <div class="detail-panel" id="claim-detail-panel">
                        <div class="empty-state-panel">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <p>Selecciona un reclamo de la lista para ver el progreso, inspeccionar los detalles y enviar una respuesta.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MODULE: TREE INVENTORY (Exclusivo Inspector) -->
            <section id="module-inventario" class="dashboard-module">
                <div class="admin-header-section">
                    <div>
                        <h2>Inventario de Arbolado Urbano</h2>
                        <p>Directorio de especies plantadas, alturas, estados y búsqueda rápida por ID</p>
                    </div>
                    <button class="btn-primary btn-icon" onclick="openCreateTreeModal()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Registrar Nuevo Árbol
                    </button>
                </div>

                <!-- Filters -->
                <div class="inventory-filter-bar">
                    <div class="inventory-filter-group">
                        <label for="filter-tree-id">Buscar por ID</label>
                        <input type="text" id="filter-tree-id" placeholder="Ej. 10045" oninput="filterTrees()">
                    </div>
                    <div class="inventory-filter-group">
                        <label for="filter-tree-species">Especie</label>
                        <select id="filter-tree-species" onchange="filterTrees()">
                            <option value="">Todas las especies</option>
                            <option value="Jacarandá">Jacarandá</option>
                            <option value="Ceibo">Ceibo</option>
                            <option value="Fresno">Fresno</option>
                            <option value="Palo Borracho">Palo Borracho</option>
                            <option value="Tilo">Tilo</option>
                            <option value="Liquidámbar">Liquidámbar</option>
                        </select>
                    </div>
                    <div class="inventory-filter-group">
                        <label for="filter-tree-state">Estado</label>
                        <select id="filter-tree-state" onchange="filterTrees()">
                            <option value="">Cualquier estado</option>
                            <option value="Saludable">Saludable</option>
                            <option value="Enfermo">Enfermo</option>
                            <option value="Dañado">Dañado</option>
                        </select>
                    </div>
                </div>

                <div class="split-layout">
                    <!-- Left Panel -->
                    <div class="list-panel">
                        <div class="items-list trees-list" id="trees-list-container">
                            <!-- Loaded via JS -->
                        </div>
                    </div>

                    <!-- Right Panel -->
                    <div class="detail-panel" id="tree-detail-panel">
                        <div class="empty-state-panel">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                                <path d="M12 19v3M12 3L9 8h1.5L7.5 13h1.5L5 19h14l-4-6h1.5l-3-5h1.5Z"/>
                            </svg>
                            <p>Selecciona un árbol del inventario para visualizar sus detalles técnicos y estado fitosanitario.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MODULE: PAGOS (Exclusivo Inspector) -->
            <section id="module-pagos" class="dashboard-module">
                <div class="admin-header-section">
                    <div>
                        <h2>Órdenes de Trabajo y Estado de Pagos</h2>
                        <p>Monitoreo de tareas realizadas por empresas comunales, verificación de costos y confirmación de pagos.</p>
                    </div>
                </div>

                <div class="split-layout">
                    <div class="list-panel">
                        <div class="items-list" id="payments-list-container">
                            <!-- Loaded via JS -->
                        </div>
                    </div>
                    <div class="detail-panel" id="payment-detail-panel">
                        <div class="empty-state-panel">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                                <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect>
                                <line x1="12" y1="18" x2="12" y2="18.01"></line>
                            </svg>
                            <p>Selecciona una orden de trabajo realizada para ver su desglose de costos y certificar su estado de pago.</p>
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
    <script type="module" src="{{ asset('js/dashboards/inspector/main.js') }}"></script>

@endsection

