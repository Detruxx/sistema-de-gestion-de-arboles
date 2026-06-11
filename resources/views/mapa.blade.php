@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endpush

@section('body-class', 'map-body')
@section('navbar-class', 'scrolled')
@section('content')



    <main class="map-page-container">
        <div class="map-wrapper">
            
            <aside id="tree-sidebar" class="sidebar-closed">
                <button id="toggle-sidebar" class="toggle-btn">
                    <svg id="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <div class="sidebar-content">
                    <div class="sidebar-top">
                        <div class="badges-col">
                            <div class="info-badge">
                                <span class="badge-label">ID</span>
                                <span id="t-id">#0000</span>
                            </div>
                            <div class="info-badge">
                                <span class="badge-label">ESTADO</span>
                                <span id="t-estado" class="status-good">Saludable</span>
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
                            <li><strong>Plantado/Años:</strong> <span id="t-edad">-</span></li>
                            <li><strong>Cantidad de reclamos:</strong> <span id="t-reclamos">-</span></li>
                        </ul>
                    </div>
                </div>
            </aside>

            <div id="tree-map"></div>
            
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/navbar.js') }}"></script>
    <script src="{{ asset('js/map.js') }}"></script>
@endpush

