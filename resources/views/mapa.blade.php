@extends('layouts.app')

@section('title', 'Mapa | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-mapa', 'active')
@section('body-class', 'map-body')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endsection

@section('content')
    <main class="map-page-container">
        <div class="map-wrapper">
            

            <div class="map-floating-controls">
                <div class="search-box">
                    <input type="text" id="map-search-input" placeholder="Buscar dirección o especie...">
                    <button class="search-btn" id="map-search-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </div>
                <div class="filter-dropdown-container">
                    <button id="btn-toggle-filters" class="filter-toggle-btn" aria-label="Filtrar árboles">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                    </button>
                    <div id="filter-dropdown-menu" class="filter-dropdown-menu">
                        <!-- Filtros de especie, altura, edad, estado -->
                        <h3 class="filter-title">Filtros</h3>
                        
                        <div class="filter-group">
                            <label class="filter-group-label">Especie</label>
                            <select id="filter-especie" class="form-control">
                                <option value="">Todas las especies</option>
                                <option value="Jacarandá">Jacarandá</option>
                                <option value="Ceibo">Ceibo</option>
                                <option value="Fresno">Fresno</option>
                                <option value="Palo Borracho">Palo Borracho</option>
                                <option value="Tilo">Tilo</option>
                                <option value="Liquidámbar">Liquidámbar</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-group-label">Altura</label>
                            <select id="filter-altura" class="form-control">
                                <option value="">Cualquier altura</option>
                                <option value="bajo">Bajo (menor a 6m)</option>
                                <option value="medio">Medio (6m a 12m)</option>
                                <option value="alto">Alto (mayor a 12m)</option>
                            </select>
                        </div>
                        <!--   POR AHORA ESTOS FILTROS NO LOS NECESITAMOS
                        <div class="filter-group">
                            <label class="filter-group-label">Edad</label>
                            <select id="filter-edad" class="form-control">
                                <option value="">Cualquier edad</option>
                                <option value="joven">Joven (menor a 10 años)</option>
                                <option value="maduro">Maduro (10 a 30 años)</option>
                                <option value="centenario">Centenario (mayor a 30 años)</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-group-label">Estado Fitosanitario</label>
                            <select id="filter-estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="Bueno">Bueno</option>
                                <option value="Regular">Regular</option>
                                <option value="Malo">Malo</option>
                            </select>
                        </div>
                        -->
                    </div>
                </div>
            </div>

            <aside id="tree-sidebar" class="sidebar-closed">
                <button id="toggle-sidebar" class="toggle-btn">
                    <svg id="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <div class="sidebar-content">

                    <!-- PANEL DE DETALLES DEL ÁRBOL -->
                    <div id="sidebar-panel-details" class="sidebar-panel active">
                        <button class="panel-back-btn" id="btn-tree-back">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Volver
                        </button>

                        <div class="tree-card-detail">
                            <div class="sidebar-top">
                                <div class="badges-col">
                                    <div class="info-badge">
                                        <span class="badge-label">ID</span>
                                        <span id="t-id">#0000</span>
                                    </div>
                                </div>
                                <div class="photo-col">
                                    <img id="t-foto" src="https://via.placeholder.com/150" alt="Foto del árbol">
                                </div>
                            </div>

                            <div class="sidebar-bottom">
                                <h4 class="data-title">DATOS DEL ARBOL</h4>
                                <ul class="data-list">
                                    <li><strong>Especie:</strong> <span id="t-especie">-</span></li>
                                    <li><strong>Altura:</strong> <span id="t-altura">-</span></li>
                                    <li><strong>DAP:</strong> <span id="t-dap">-</span></li>
                                    <li><strong>Fitosanitario:</strong> <span id="t-vitalidad">-</span></li>
                                    <li><strong>Mantenimiento:</strong> <span id="t-mantenimiento">-</span></li>
                                    <li><strong>Estructura:</strong> <span id="t-estructura">-</span></li>
                                    <li><strong>Observaciones:</strong> <span id="t-observaciones">-</span></li>
                                    <li><strong id="t-direccion-label">Dirección:</strong> <span id="t-direccion">-</span></li>
                                </ul>
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <a id="btn-reclamar-arbol" href="#" class="btn-main-cta" style="width: 100%; font-size: 0.95rem; padding: 12px 20px; border-radius: 8px; text-align: center; text-transform: none; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(45, 122, 79, 0.15);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                                Iniciar Reclamo
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <div id="tree-map"></div>
            
        </div>
    </main>
@endsection

@section('footer')
    <!-- Sobreescribir footer para que no se muestre en el mapa a pantalla completa -->
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/home/map.js') }}"></script>
@endsection