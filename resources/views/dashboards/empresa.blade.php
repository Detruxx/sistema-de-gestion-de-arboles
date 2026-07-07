@extends('layouts.app')

@section('title', 'Panel de Control Empresa | TreeBA')
@section('navbar-class', 'scrolled')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboards/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboards/dynamic-status.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="{{ asset('css/dashboards/inspector.css') }}">
@endsection

@section('content')
<div class="admin-dashboard-container">
    <style>
        /* Encuadrar contenido más arriba */
        .admin-main-panel { padding-top: 15px !important; }
        .admin-header-section { margin-bottom: 20px !important; }
        .admin-sidebar { justify-content: flex-start !important; }
        .sidebar-footer { margin-top: auto !important; margin-bottom: auto !important; } 
        /* Si margin-top es auto, empujará un poco el footer pero no del todo al fondo si le damos min-height. Mejor un margen fijo: */
        .sidebar-footer { margin-top: 50px !important; }
    </style>
    
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
                    <p>Resumen de tareas y servicios contratados por la Comuna 13</p>
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
                            <p class="activity-desc">Hace 3 horas - Comuna 13</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-dot activity-dot-success"></span>
                        <div>
                            <p class="activity-title">Tu postulación a la orden #105 ha sido PRE-APROBADA por el inspector</p>
                            <p class="activity-desc">Ayer - Comuna 13</p>
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

            <div class="split-layout" style="align-items: stretch; min-height: calc(100vh - 220px);">
                <!-- Left Panel -->
                <div class="list-panel" style="height: 100%; max-height: none; padding-right: 5px;">
                    <div class="items-list" id="company-jobs-list-container" style="height: 100%; max-height: calc(100vh - 250px); overflow-y: auto;">
                        <!-- Loaded via JS -->
                    </div>
                </div>

                <!-- Right Panel (Mapa Interactivo) -->
                <div class="detail-panel" id="company-map-panel" style="padding: 0; position: relative; height: 100%; min-height: 520px;">
                    <div id="company-jobs-map" style="width: 100%; height: 100%; min-height: 100%; border-radius: 12px; z-index: 1;"></div>
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

            <div class="items-list" id="company-payments-list-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                <!-- Loaded via JS -->
            </div>
        </section>

        <!-- Modal para Detalles de Pago -->
        <div id="company-payment-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: flex-start; padding-top: 95px; padding-bottom: 25px;">
            <div class="admin-modal" style="background: white; width: 90%; max-width: 600px; max-height: calc(100vh - 120px); border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                <div class="admin-modal-header" style="background: var(--admin-bg); padding: 15px 20px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: var(--admin-accent);">Detalle de Pago</h3>
                    <button type="button" class="admin-modal-close" onclick="closeCompanyPaymentModal()" style="background: none; border: none; font-size: 1.8rem; line-height: 1; cursor: pointer; color: var(--admin-text-secondary);">&times;</button>
                </div>
                <div class="admin-modal-body" id="company-payment-modal-body" style="flex: 1; overflow-y: auto; padding: 20px;">
                    <!-- Cargado dinámicamente por JS -->
                </div>
            </div>
        </div>



        <!-- Modal para Detalles del Trabajo -->
        <div id="company-job-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: flex-start; padding-top: 95px; padding-bottom: 25px;">
            <div class="admin-modal" style="background: white; width: 90%; max-width: 900px; max-height: calc(100vh - 120px); border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                <div class="admin-modal-header" style="background: var(--admin-bg); padding: 15px 20px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: var(--admin-accent);">Detalle de Orden de Trabajo</h3>
                    <button type="button" class="admin-modal-close" onclick="closeCompanyJobModal()" style="background: none; border: none; font-size: 1.8rem; line-height: 1; cursor: pointer; color: var(--admin-text-secondary);">&times;</button>
                </div>
                <div class="admin-modal-body" id="company-job-modal-body" style="flex: 1; overflow-y: auto; padding: 20px;">
                    <!-- Cargado dinámicamente por JS -->
                </div>
            </div>
        </div>

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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script type="module" src="{{ asset('js/dashboards/company/main.js') }}"></script>
@endsection

