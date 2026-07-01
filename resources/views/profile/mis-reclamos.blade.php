@extends('layouts.app')

@section('title', 'Mis Reclamos | TreeBA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <main class="profile-page-container">
        <section class="profile-header reveal">
            <h1 class="hero-title">Mis Reclamos</h1>
            <p class="section-subtitle">Consulta el estado y avance de las incidencias que has reportado en la vía pública.</p>
        </section>

        <div class="tabs-container reveal delay-1">
            <div class="tramites-tabs">
                <button type="button" class="tab-btn active" data-target="tab-reclamos" onclick="switchProfileTab('tab-reclamos', this)">Ver Reclamos</button>
                <button type="button" class="tab-btn" data-target="tab-plantaciones" onclick="switchProfileTab('tab-plantaciones', this)">Ver Solicitudes de Plantación</button>
            </div>
            
            <div id="tab-reclamos" class="tab-content active">
                <div class="reclamos-container">
            @if(count($reclamos) === 0)
                <div class="no-records-card">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <h3>Aún no has registrado ningún reclamo</h3>
                    <p>Si observas un árbol dañado, ramas secas o problemas en planteras, puedes reportarlo.</p>
                    <a href="/tramites/reclamos" class="btn-main-cta" style="margin-top: 15px;">Crear Reclamo</a>
                </div>
            @else
                <div class="list-header-bar">
                    <p class="listing-count">Mostrando <span class="count-val">{{ count($reclamos) }}</span> reclamos</p>
                    <div class="filter-dropdown">
                        <select id="sort-reclamos" class="form-control sort-select" onchange="sortList('reclamos-list-container', this.value)">
                            <option value="desc">Más nuevo a más antiguo</option>
                            <option value="asc">Más antiguo a más nuevo</option>
                        </select>
                    </div>
                </div>
                
                <div class="reclamos-list" id="reclamos-list-container">
                    @foreach($reclamos as $rec)
                        @php
                            // Ajustes para soportar tanto objetos Eloquent como arrays de la simulacion
                            $id = is_array($rec) ? $rec['id'] : $rec->id;
                            $status = is_array($rec) ? $rec['status'] : $rec->status;
                            $typeName = is_array($rec) ? $rec['type_name'] : ($rec->Request_Type ? $rec->Request_Type->type_name : 'Reclamo General');
                            $streetName = is_array($rec) ? $rec['street_name'] : ($rec->street ? $rec->street->street_name . ' ' . $rec->street->street_number : 'Ubicación no especificada');
                            $description = is_array($rec) ? $rec['description'] : $rec->description;
                            $treeId = is_array($rec) ? $rec['tree_id'] : $rec->tree_id;
                            $treeSpecie = is_array($rec) ? $rec['tree_specie'] : ($rec->tree && $rec->tree->species ? $rec->tree->species->common_name : null);
                            $createdAt = is_array($rec) ? $rec['created_at'] : $rec->created_at->format('Y-m-d H:i:s');
                            $dateFormatted = date('d/m/Y', strtotime($createdAt));

                            // Clasificar el color del estado
                            $statusClass = 'open';
                            $statusText = 'En revisión';
                            if ($status === 'resolved' || $status === 'resolved') {
                                $statusClass = 'resolved';
                                $statusText = 'Completado';
                            } elseif ($status === 'discarded') {
                                $statusClass = 'discarded';
                                $statusText = 'Descartado';
                            }
                            $timestamp = strtotime($createdAt);
                        @endphp

                        <details class="reclamo-card {{ $statusClass }}" data-timestamp="{{ $timestamp }}">
                            <summary class="reclamo-card-summary">
                                <div class="card-summary-left">
                                    <span class="reclamo-id">#{{ $id }}</span>
                                    <div>
                                        <h3>{{ $typeName }}</h3>
                                        <p class="summary-meta">{{ $streetName }} • {{ $dateFormatted }}</p>
                                    </div>
                                </div>
                                <div class="card-summary-right">
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                    <span class="chevron-arrow">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </span>
                                </div>
                            </summary>
                            
                            <div class="reclamo-card-details">
                                <div class="details-section">
                                    <h4>Descripción de la Incidencia</h4>
                                    <p>{{ $description }}</p>
                                </div>

                                <div class="details-meta-grid">
                                    <div class="meta-box">
                                        <strong>Ubicación del Reporte</strong>
                                        <span>{{ $streetName }}</span>
                                    </div>
                                    <div class="meta-box">
                                        <strong>Fecha de Registro</strong>
                                        <span>{{ date('d/m/Y H:i', strtotime($createdAt)) }} hs</span>
                                    </div>
                                    @if($treeId)
                                        <div class="meta-box">
                                            <strong>Árbol Vinculado</strong>
                                            <span>ID #{{ $treeId }} @if($treeSpecie)({{ $treeSpecie }})@endif</span>
                                        </div>
                                    @endif
                                </div>

                                @if($statusClass === 'resolved')
                                    <div class="inspector-response-box">
                                        <div class="response-header">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            <strong>Resolución del Inspector</strong>
                                        </div>
                                        <p>
                                            El área operativa de la Comuna ha verificado y resuelto la incidencia reportada de forma satisfactoria. Las tareas de mantenimiento han finalizado.
                                        </p>
                                    </div>
                                @endif

                                <!-- Sección de Cancelación del Reclamo (para Vecinos) -->
                                @if($statusClass !== 'resolved' && $statusClass !== 'discarded')
                                    <div class="vecino-cancel-section" id="cancel-section-{{ $id }}" style="margin-top: 20px; border-top: 1px dashed rgba(45, 122, 79, 0.2); padding-top: 20px;">
                                        <!-- Botón inicial -->
                                        <button type="button" class="btn-main-cta btn-cancel-trigger" onclick="showCancelForm('{{ $id }}')" style="background-color: transparent; border: 2px solid #d32f2f; color: #d32f2f; font-size: 0.9rem; padding: 8px 16px; margin: 0; display: inline-block; cursor: pointer; border-radius: 8px; font-weight: 600;">
                                            Cancelar Reclamo
                                        </button>

                                        <!-- Formulario colapsado -->
                                        <div class="cancel-form-box" id="cancel-form-{{ $id }}" style="display: none; margin-top: 15px; background: rgba(211, 47, 47, 0.05); border: 1px solid rgba(211, 47, 47, 0.2); padding: 15px; border-radius: 8px;">
                                            <label for="cancel-reason-{{ $id }}" style="display: block; font-weight: 600; font-size: 0.85rem; color: #d32f2f; text-transform: uppercase; margin-bottom: 8px;">¿Por qué deseas cancelar este reclamo?</label>
                                            <p style="font-size: 0.85rem; color: var(--forest-night); margin-top: 0; margin-bottom: 10px; opacity: 0.8;">
                                                Por favor, indícanos el motivo (por ejemplo, si el trabajo será realizado por un privado/particular para registrar quién intervino el árbol).
                                            </p>
                                            <textarea id="cancel-reason-{{ $id }}" class="form-control" style="width: 100%; min-height: 80px; margin-bottom: 12px; padding: 10px; border-radius: 6px; border: 1px solid rgba(45, 122, 79, 0.25); box-sizing: border-box;" placeholder="Escribe aquí el motivo de la cancelación..."></textarea>
                                            <div style="display: flex; gap: 10px;">
                                                <button type="button" class="btn-main-cta" onclick="submitCancelClaim('{{ $id }}')" style="background-color: #d32f2f; border: none; font-size: 0.85rem; padding: 8px 16px; margin: 0; color: white; cursor: pointer; border-radius: 8px; font-weight: 600;">
                                                    Confirmar Cancelación
                                                </button>
                                                <button type="button" class="btn-main-cta" onclick="hideCancelForm('{{ $id }}')" style="background-color: transparent; border: 1px solid var(--living-moss); color: var(--living-moss); font-size: 0.85rem; padding: 8px 16px; margin: 0; cursor: pointer; border-radius: 8px; font-weight: 600;">
                                                    Volver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Sección para mostrar el motivo de cancelación si ya fue cancelado -->
                                <div class="cancellation-reason-display" id="cancellation-display-{{ $id }}" style="display: none; margin-top: 20px; background: rgba(211, 47, 47, 0.05); border: 1px solid rgba(211, 47, 47, 0.2); padding: 15px; border-radius: 8px;">
                                    <div style="display: flex; align-items: center; gap: 10px; color: #d32f2f; font-weight: 700; margin-bottom: 5px; font-size: 0.9rem;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        Reclamo Cancelado por el Vecino
                                    </div>
                                    <p id="cancellation-text-{{ $id }}" style="font-size: 0.95rem; color: #b71c1c; margin: 0; line-height: 1.5; font-style: italic;"></p>
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
            </div>
        </div>

        <div id="tab-plantaciones" class="tab-content">
            <div class="reclamos-container">
                @if(count($plantaciones) === 0)
                    <div class="no-records-card">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <h3>Aún no has registrado solicitudes de plantación</h3>
                        <p>Si deseas plantar un árbol en tu vereda, puedes enviar una solicitud.</p>
                        <a href="/tramites/plantacion" class="btn-main-cta" style="margin-top: 15px;">Solicitar Plantación</a>
                    </div>
                @else
                    <div class="list-header-bar">
                        <p class="listing-count">Mostrando <span class="count-val">{{ count($plantaciones) }}</span> solicitudes</p>
                        <div class="filter-dropdown">
                            <select id="sort-plantaciones" class="form-control sort-select" onchange="sortList('plantaciones-list-container', this.value)">
                                <option value="desc">Más nuevo a más antiguo</option>
                                <option value="asc">Más antiguo a más nuevo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="reclamos-list" id="plantaciones-list-container">
                        @foreach($plantaciones as $rec)
                            @php
                                $id = is_array($rec) ? $rec['id'] : $rec->id;
                                $status = is_array($rec) ? $rec['status'] : $rec->status;
                                $typeName = 'Solicitud de Plantación';
                                $streetName = is_array($rec) ? $rec['street_name'] : ($rec->street ? $rec->street->street_name . ' ' . $rec->street->street_number : 'Ubicación no especificada');
                                $description = is_array($rec) ? $rec['description'] : $rec->description;
                                $createdAt = is_array($rec) ? $rec['created_at'] : $rec->created_at->format('Y-m-d H:i:s');
                                $dateFormatted = date('d/m/Y', strtotime($createdAt));
                                $timestamp = strtotime($createdAt);

                                $statusClass = 'open';
                                $statusText = 'En revisión';
                                if ($status === 'resolved') {
                                    $statusClass = 'resolved';
                                    $statusText = 'Aprobada';
                                } elseif ($status === 'discarded') {
                                    $statusClass = 'discarded';
                                    $statusText = 'Rechazada';
                                }
                            @endphp

                            <details class="reclamo-card {{ $statusClass }}" data-timestamp="{{ $timestamp }}">
                                <summary class="reclamo-card-summary">
                                    <div class="card-summary-left">
                                        <span class="reclamo-id">#{{ $id }}</span>
                                        <div>
                                            <h3>{{ $typeName }}</h3>
                                            <p class="summary-meta">{{ $streetName }} • {{ $dateFormatted }}</p>
                                        </div>
                                    </div>
                                    <div class="card-summary-right">
                                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                        <span class="chevron-arrow">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </span>
                                    </div>
                                </summary>
                                
                                <div class="reclamo-card-details">
                                    <div class="details-section">
                                        <h4>Detalle de Solicitud</h4>
                                        <p>{{ $description }}</p>
                                    </div>

                                    <div class="details-meta-grid">
                                        <div class="meta-box">
                                            <strong>Ubicación Solicitada</strong>
                                            <span>{{ $streetName }}</span>
                                        </div>
                                        <div class="meta-box">
                                            <strong>Fecha de Solicitud</strong>
                                            <span>{{ date('d/m/Y H:i', strtotime($createdAt)) }} hs</span>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        function showCancelForm(id) {
            document.getElementById('cancel-form-' + id).style.display = 'block';
            const trigger = document.querySelector('#cancel-section-' + id + ' .btn-cancel-trigger');
            if (trigger) trigger.style.display = 'none';
        }

        function hideCancelForm(id) {
            document.getElementById('cancel-form-' + id).style.display = 'none';
            const trigger = document.querySelector('#cancel-section-' + id + ' .btn-cancel-trigger');
            if (trigger) trigger.style.display = 'inline-block';
        }

        function submitCancelClaim(id) {
            const reason = document.getElementById('cancel-reason-' + id).value.trim();
            if (!reason) {
                alert('Por favor ingrese el motivo de la cancelación.');
                return;
            }

            // Guardar en localStorage
            localStorage.setItem('cancelled_claim_' + id + '_reason', reason);

            // Actualizar la interfaz
            applyLocalCancellations();
            
            // Cerrar el formulario
            hideCancelForm(id);
        }

        function applyLocalCancellations() {
            const cards = document.querySelectorAll('details.reclamo-card');
            cards.forEach(card => {
                const idSpan = card.querySelector('.reclamo-id');
                if (!idSpan) return;
                const id = idSpan.innerText.replace('#', '').trim();

                const reason = localStorage.getItem('cancelled_claim_' + id + '_reason');
                if (reason) {
                    // Cambiar el badge de estado
                    const badge = card.querySelector('.status-badge');
                    if (badge) {
                        badge.innerText = 'CANCELADO';
                        badge.className = 'status-badge discarded';
                    }

                    // Ocultar sección de cancelar
                    const cancelSec = document.getElementById('cancel-section-' + id);
                    if (cancelSec) cancelSec.style.display = 'none';

                    // Mostrar el display del motivo de cancelación
                    const displayBox = document.getElementById('cancellation-display-' + id);
                    const displayText = document.getElementById('cancellation-text-' + id);
                    if (displayBox && displayText) {
                        displayText.innerText = '"' + reason + '"';
                        displayBox.style.display = 'block';
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            applyLocalCancellations();
        });

        function switchProfileTab(tabId, btnElement) {
            document.querySelectorAll('.tabs-container .tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tramites-tabs .tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabId).classList.add('active');
            btnElement.classList.add('active');
        }

        function sortList(containerId, order) {
            const container = document.getElementById(containerId);
            if (!container) return;
            const items = Array.from(container.querySelectorAll('.reclamo-card'));
            
            items.sort((a, b) => {
                const timeA = parseInt(a.getAttribute('data-timestamp'));
                const timeB = parseInt(b.getAttribute('data-timestamp'));
                if (order === 'desc') {
                    return timeB - timeA;
                } else {
                    return timeA - timeB;
                }
            });
            
            // Re-append in new order
            items.forEach(item => container.appendChild(item));
        }
    </script>
@endsection
