@extends('layouts.app')

@section('title', 'Panel de Control Empresa | TreeBA')
@section('navbar-class', 'scrolled')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboards/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboards/dynamic-status.css') }}">
@endsection

@section('content')
<div class="admin-dashboard-container">
    
    <!-- Sidebar de la Empresa -->
    <aside class="admin-sidebar">
        <button class="sidebar-toggle" onclick="toggleAdminSidebar()">
            <span>Menú de la Empresa</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>

        <div class="sidebar-menu">
            <h3 class="sidebar-menu-title">Panel de Contratista</h3>
            <button class="sidebar-btn active" onclick="showModule('resumen')" id="menu-resumen">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9"></rect>
                    <rect x="14" y="3" width="7" height="5"></rect>
                    <rect x="14" y="12" width="7" height="9"></rect>
                    <rect x="3" y="16" width="7" height="5"></rect>
                </svg>
                Resumen
            </button>
            <button class="sidebar-btn" onclick="showModule('trabajos')" id="menu-trabajos">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
                Trabajos Asignados
            </button>
            <button class="sidebar-btn" onclick="showModule('pagos')" id="menu-pagos">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12" y2="18.01"></line>
                </svg>
                Validación de Pagos
            </button>
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

    <!-- Main Content Panel -->
    <main class="admin-main-panel">
        
        <!-- MODULE: RESUMEN -->
        <section id="module-resumen" class="dashboard-module active">
            <div class="admin-header-section">
                <div>
                    <h2>Panel de Control - Contratista</h2>
                    <p>Resumen de tareas y servicios contratados por Espacio Público</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="stat-info">
                        <h4>Trabajos Realizados</h4>
                        <p id="company-stat-completed">...</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="stat-info">
                        <h4>Trabajos a Realizar</h4>
                        <p id="company-stat-pending">...</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-warning">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect>
                            <line x1="12" y1="18" x2="12" y2="18.01"></line>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <h4>Pagos Pendientes</h4>
                        <p id="company-stat-unpaid">...</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Panel -->
            <div class="detail-panel">
                <h3 class="sidebar-menu-title mt-25" style="font-family: var(--font-display); margin-bottom: 20px; color: var(--admin-accent);">Notificaciones y Novedades</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <span class="activity-dot activity-dot-info"></span>
                        <div>
                            <p class="activity-title">Licitación de poda de Av. Cabildo abierta para ofertas</p>
                            <p class="activity-desc">Hace 3 horas - Espacio Público</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-dot activity-dot-success"></span>
                        <div>
                            <p class="activity-title">Tu postulación a la orden #105 ha sido PRE-APROBADA por el inspector</p>
                            <p class="activity-desc">Ayer - Espacio Público</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MODULE: TRABAJOS ASIGNADOS -->
        <section id="module-trabajos" class="dashboard-module">
            <div class="admin-header-section">
                <div>
                    <h2>Trabajos Asignados a la Empresa</h2>
                    <p>Listado de órdenes de trabajo recibidas desde el cuerpo de inspectores.</p>
                </div>
            </div>

            <div class="split-layout">
                <!-- Left Panel -->
                <div class="list-panel">
                    <div class="items-list" id="company-jobs-list-container">
                        <!-- Loaded via JS -->
                    </div>
                </div>

                <!-- Right Panel -->
                <div class="detail-panel" id="company-job-detail-panel">
                    <div class="empty-state-panel">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                        </svg>
                        <p>Selecciona un trabajo asignado para ver las especificaciones técnicas y actualizar su avance.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- MODULE: VALIDACION DE PAGOS -->
        <section id="module-pagos" class="dashboard-module">
            <div class="admin-header-section">
                <div>
                    <h2>Validación y Estado de Pagos</h2>
                    <p>Verifica el estado de facturación y cobros de tus servicios finalizados.</p>
                </div>
            </div>

            <div class="split-layout">
                <div class="list-panel">
                    <div class="items-list" id="company-payments-list-container">
                        <!-- Loaded via JS -->
                    </div>
                </div>
                <div class="detail-panel" id="company-payment-detail-panel">
                    <div class="empty-state-panel">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-state-icon">
                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect>
                            <line x1="12" y1="18" x2="12" y2="18.01"></line>
                        </svg>
                        <p>Selecciona un servicio finalizado para ver o validar su estado de pago.</p>
                    </div>
                </div>
            </div>
        </section>



    </main>
</div>

<!-- Notification Banner -->
<div id="notification-banner" class="notification-banner" style="display: none;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 8 12 12 16 14"></polyline>
    </svg>
    <span id="notification-text">Novedades guardadas.</span>
</div>
@endsection

@section('footer')
    <!-- Se oculta el footer en el Panel de Administración para un estilo más limpio -->
@endsection

@section('scripts')
    <script>
        window.currentUserRole = "empresa";
    </script>
    <script src="{{ asset('js/dashboards/inspector/modules/core.js') }}"></script>
    <script type="module" src="{{ asset('js/dashboards/company/main.js') }}"></script>
@endsection

