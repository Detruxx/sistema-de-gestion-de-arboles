@extends('layouts.app')

@section('title', 'Mis Reclamos | Arborea')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/mis-reclamos.css') }}">
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
                            <option value="new">Nuevos/Sin Leer</option>
                        </select>
                    </div>
                </div>
                
                <div class="reclamos-list" id="reclamos-list-container">
                    @foreach($reclamos as $rec)
                        @php
                            $id = $rec->id;
                            $statusObj = $rec->status;
                            $statusSlug = $statusObj ? $statusObj->slug : 'open';
                            
                            // Mapear descartado a denied para consistencia
                            if ($statusSlug === 'discarded') {
                                $statusSlug = 'denied';
                            }
                            
                            $typeName = $rec->Request_Type ? $rec->Request_Type->type_name : 'Reclamo General';
                            $streetName = $rec->street ? $rec->street->street_name . ' ' . $rec->street->street_number : 'Ubicación no especificada';
                            $description = $rec->description;
                            $treeId = $rec->tree_id;
                            $treeSpecie = $rec->tree && $rec->tree->species ? $rec->tree->species->common_name : null;
                            $createdAt = $rec->created_at->format('Y-m-d H:i:s');
                            $dateFormatted = date('d/m/Y', strtotime($createdAt));

                            // Clasificar el color y badge de la tarjeta
                            $statusClass = 'open';
                            $statusText = 'En revisión';
                            if ($statusSlug === 'resolved' || $statusSlug === 'certified') {
                                $statusClass = 'resolved';
                                $statusText = 'Completado';
                            } elseif ($statusSlug === 'denied' || $statusSlug === 'vinculated') {
                                $statusClass = 'discarded';
                                $statusText = $statusSlug === 'denied' ? 'Rechazado' : 'Vinculado';
                            }
                            $timestamp = strtotime($createdAt);
                            
                            $isNew = $rec->is_new_for_user ?? false; 

                            // Stepper de estados lineales (1 al 6)
                            $linearSteps = [
                                ['status_name' => 'Pendiente', 'slug' => 'open', 'sequence' => 1, 'color' => '#eab308'],
                                ['status_name' => 'Relevado / Inspeccionado', 'slug' => 'relevated', 'sequence' => 2, 'color' => '#ea580c'],
                                ['status_name' => 'Programado', 'slug' => 'scheduled', 'sequence' => 3, 'color' => '#6b21a8'],
                                ['status_name' => 'En curso', 'slug' => 'in_progress', 'sequence' => 4, 'color' => '#2563eb'],
                                ['status_name' => 'Completado', 'slug' => 'resolved', 'sequence' => 5, 'color' => '#22c55e'],
                                ['status_name' => 'Certificado', 'slug' => 'certified', 'sequence' => 6, 'color' => '#15803d'],
                            ];

                            // Buscar si el estado es de excepción terminal
                            $terminalStatus = null;
                            if ($statusSlug === 'denied') {
                                $terminalStatus = ['status_name' => 'Denegado', 'slug' => 'denied', 'color' => '#ef4444'];
                            } elseif ($statusSlug === 'vinculated') {
                                $terminalStatus = ['status_name' => 'Vinculado (Duplicado)', 'slug' => 'vinculated', 'color' => '#d946ef'];
                            } elseif ($statusSlug === 'cancelled') {
                                $terminalStatus = ['status_name' => 'Cancelado', 'slug' => 'cancelled', 'color' => '#6b7280'];
                                $statusClass = 'discarded';
                                $statusText = 'Cancelado';
                            }

                            $isTerminalException = ($terminalStatus !== null);

                            // Obtener la secuencia actual
                            $currentSeq = 0;
                            $currentStatusColor = '#eab308';
                            $currentStatusName = 'Pendiente';
                            if (!$isTerminalException) {
                                foreach ($linearSteps as $ls) {
                                    if ($ls['slug'] === $statusSlug) {
                                        $currentSeq = $ls['sequence'];
                                        $currentStatusColor = $ls['color'];
                                        $currentStatusName = $ls['status_name'];
                                        break;
                                    }
                                }
                            } else {
                                $currentStatusColor = $terminalStatus['color'];
                                $currentStatusName = $terminalStatus['status_name'];
                            }

                            // Calcular porcentaje de relleno
                            $progressPercent = 0;
                            if (!$isTerminalException && $currentSeq > 1) {
                                $progressPercent = (($currentSeq - 1) / (count($linearSteps) - 1)) * 100;
                            }
                            $lineBg = $isTerminalException ? $currentStatusColor : '#15803d';

                            // Historial de cambios
                            $histories = $rec->histories ?? [];
                        @endphp

                        <details class="reclamo-card {{ $statusClass }}" data-timestamp="{{ $timestamp }}" data-is-new="{{ $isNew ? 'true' : 'false' }}" data-type="reclamo" data-id="{{ $id }}" style="position: relative;">
                            @if($isNew)
                                <div class="new-dot-indicator" style="position: absolute; left: -14px; top: 15px;">
                                    <x-layouts.notification-badge isDot="true" />
                                </div>
                            @endif
                            <summary class="reclamo-card-summary">
                                <div class="card-summary-left">
                                    <span class="reclamo-id" style="font-size: 0.85rem; color: #64748b; font-weight: 500;">{{ $rec->tracking_code }}</span>
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

                                <!-- Stepper de Progreso (Lectura) -->
                                <div class="claim-progress-container">
                                    <div class="claim-progress-title">Progreso del Reclamo</div>
                                    <div class="claim-steps-wrapper">
                                        <div class="claim-progress-line">
                                            <div class="claim-progress-line-fill" style="width: {{ $progressPercent }}%; background: {{ $lineBg }};"></div>
                                        </div>
                                        @foreach($linearSteps as $step)
                                            @php
                                                $isActive = !$isTerminalException && $step['sequence'] === $currentSeq;
                                                $isPassed = !$isTerminalException && $step['sequence'] < $currentSeq;

                                                $bgNum = '#ffffff';
                                                $borderNum = '#e5e7eb';
                                                $colorNum = '#9ca3af';
                                                $colorLbl = '#9ca3af';
                                                $fontLbl = '500';

                                                $labelText = $step['status_name'];
                                                $numText = $step['sequence'];
                                                $nodeClass = '';

                                                if ($isTerminalException && $step['sequence'] === 1) {
                                                    $bgNum = $currentStatusColor;
                                                    $borderNum = $currentStatusColor;
                                                    $colorNum = '#ffffff';
                                                    $colorLbl = $currentStatusColor;
                                                    $labelText = $currentStatusName;
                                                    $numText = $statusSlug === 'denied' ? '✖' : '●';
                                                    $nodeClass = 'active';
                                                } elseif ($isActive) {
                                                    $bgNum = $currentStatusColor;
                                                    $borderNum = $currentStatusColor;
                                                    $colorNum = '#ffffff';
                                                    $colorLbl = $currentStatusColor;
                                                    $fontLbl = '700';
                                                    $nodeClass = 'active';
                                                } elseif ($isPassed) {
                                                    $bgNum = '#15803d';
                                                    $borderNum = '#15803d';
                                                    $colorNum = '#ffffff';
                                                    $colorLbl = '#15803d';
                                                    $nodeClass = 'passed';
                                                }
                                            @endphp
                                            <div class="claim-step-node {{ $nodeClass }}">
                                                <div class="claim-step-circle {{ $isTerminalException && $step['sequence'] === 1 && $statusSlug === 'denied' ? 'is-denied' : '' }}" style="background: {{ $bgNum }}; border-color: {{ $borderNum }}; color: {{ $colorNum }};">
                                                    {{ $numText }}
                                                </div>
                                                <span class="claim-step-label" style="color: {{ $colorLbl; }} font-weight: {{ $fontLbl; }};">
                                                    {{ $labelText }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Admin Response Box -->
                                @php
                                    $latestHistory = count($histories) > 0 ? $histories->last() : null;
                                    $adminReply = $latestHistory && $latestHistory->justification ? $latestHistory->justification : 'Aún no se ha redactado ninguna respuesta oficial para esta solicitud.';
                                @endphp
                                <div class="admin-reply-box" style="margin-top: 20px; background-color: #f8fafc; border-left: 4px solid var(--living-moss); padding: 15px; border-radius: 4px;">
                                    <h4 class="admin-reply-title" style="display: flex; align-items: center; gap: 8px; color: var(--forest-night); margin-bottom: 8px; font-size: 1rem;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--living-moss)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                        Respuesta del Administrador
                                    </h4>
                                    <p class="admin-reply-text" style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin: 0;">
                                        {{ $adminReply }}
                                    </p>
                                </div>

                                <!-- Historial de Cambios / Mensajes del Inspector -->
                                <div class="claim-history-container">
                                    <div class="claim-history-title">Historial de Actualizaciones</div>
                                    <div class="claim-history-list">
                                        @if(count($histories) === 0)
                                            <p style="color: #6b7280; font-style: italic; font-size: 0.9rem;">No hay actualizaciones registradas para este reclamo.</p>
                                        @else
                                            @foreach($histories as $history)
                                                @php
                                                    $statusSlugMap = [
                                                        'open'             => 'Pendiente',
                                                        'relevated'        => 'Relevado / Inspeccionado',
                                                        'scheduled'        => 'Programado',
                                                        'in_progress'      => 'En curso',
                                                        'resolved'         => 'Completado',
                                                        'certified'        => 'Certificado',
                                                        'denied'           => 'Denegado',
                                                        'vinculated'       => 'Vinculado (Duplicado)',
                                                        'cancelled'        => 'Cancelado por Vecino',
                                                        'cancel_requested' => 'Cancelación Solicitada',
                                                    ];

                                                    $hStatusName = $history->status ? $history->status->status_name : 'Actualización';
                                                    if (isset($statusSlugMap[$hStatusName])) {
                                                        $hStatusName = $statusSlugMap[$hStatusName];
                                                    }
                                                    $hColor = $history->status ? $history->status->color : '#6b7280';
                                                    $hDate = $history->created_at ? \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') : '';

                                                    // Traducir justificaciones automáticas que hayan guardado el slug en inglés
                                                    $historyText = $history->justification;
                                                    if ($historyText) {
                                                        foreach ($statusSlugMap as $slug => $traducido) {
                                                            $historyText = preg_replace('/(a:\s*)' . preg_quote($slug, '/') . '$/i', '$1' . $traducido, $historyText);
                                                            $historyText = preg_replace('/(a:\s*)' . preg_quote($slug, '/') . '\b/i', '$1' . $traducido, $historyText);
                                                        }
                                                    } else {
                                                        $historyText = 'Estado de la solicitud actualizado por el área técnica.';
                                                    }
                                                @endphp
                                                <div class="claim-history-item">
                                                    <div class="history-meta">
                                                        <span style="font-weight: 600;">{{ $hDate }} hs</span>
                                                        <span style="font-size: 0.75rem; color: #9ca3af;">Por Inspector</span>
                                                    </div>
                                                    <div class="history-content">
                                                        <span class="history-status-badge" style="background-color: {{ $hColor }};">
                                                            {{ $hStatusName }}
                                                        </span>
                                                        <p class="history-justification">
                                                            {{ $historyText }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Sección de Cancelación del Reclamo (para Vecinos) -->
                                @if($statusClass !== 'resolved' && $statusClass !== 'discarded')
                                    <div class="vecino-cancel-section" id="cancel-section-{{ $id }}" style="margin-top: 20px; border-top: 1px dashed rgba(45, 122, 79, 0.2); padding-top: 20px;">
                                        <!-- Botón inicial -->
                                        <button type="button" class="btn-main-cta btn-cancel-trigger" onclick="showCancelForm('{{ $id }}')" style="background-color: transparent; border: 2px solid #d32f2f; color: #d32f2f; font-size: 0.9rem; padding: 8px 16px; margin: 0; display: inline-block; cursor: pointer; border-radius: 8px; font-weight: 600;">
                                            Cancelar Reclamo
                                        </button>

                                        <!-- Formulario colapsado -->
                                        @if($statusSlug === 'open')
                                            <div class="cancel-form-box" id="cancel-form-{{ $id }}" style="display: none; margin-top: 15px; background: rgba(211, 47, 47, 0.05); border: 1px solid rgba(211, 47, 47, 0.2); padding: 15px; border-radius: 8px;">
                                                <label for="cancel-reason-{{ $id }}" style="display: block; font-weight: 600; font-size: 0.85rem; color: #d32f2f; text-transform: uppercase; margin-bottom: 8px;">¿Por qué deseas cancelar este reclamo?</label>
                                                <p style="font-size: 0.85rem; color: var(--forest-night); margin-top: 0; margin-bottom: 10px; opacity: 0.8;">
                                                    Por favor, indícanos el motivo (por ejemplo, si el trabajo será realizado por un privado/particular para registrar quién intervino el árbol).<br><br>
                                                    <strong>Aviso importante:</strong> La cancelación automática solo puede suceder si el reclamo está en estado "Pendiente".
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
                                        @else
                                            <div class="cancel-form-box" id="cancel-form-{{ $id }}" style="display: none; margin-top: 15px; background: rgba(211, 47, 47, 0.05); border: 1px solid rgba(211, 47, 47, 0.2); padding: 15px; border-radius: 8px;">
                                                <p style="font-size: 0.85rem; color: #d32f2f; font-weight: 600; margin-top: 0; margin-bottom: 10px;">
                                                    ⚠️ Este reclamo ya avanzó a una etapa operativa y no puede ser cancelado automáticamente.
                                                </p>
                                                <p style="font-size: 0.85rem; color: var(--forest-night); margin-top: 0; margin-bottom: 10px; opacity: 0.8;">
                                                    Por el momento, solo es posible cancelar solicitudes que se encuentren en estado Pendiente. Más adelante implementaremos la opción de enviar una solicitud formal de baja al inspector a cargo.
                                                </p>
                                                <button type="button" class="btn-main-cta" onclick="hideCancelForm('{{ $id }}')" style="background-color: transparent; border: 1px solid var(--living-moss); color: var(--living-moss); font-size: 0.85rem; padding: 8px 16px; margin: 0; cursor: pointer; border-radius: 8px; font-weight: 600;">
                                                    Entendido
                                                </button>
                                            </div>
                                        @endif
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
                                <option value="new">Nuevos/Sin Leer</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="reclamos-list" id="plantaciones-list-container">
                        @foreach($plantaciones as $rec)
                            @php
                                $id = $rec->id;
                                $status = $rec->status;
                                $typeName = 'Solicitud de Plantación';
                                $streetName = $rec->street ? $rec->street->street_name . ' ' . $rec->street->street_number : 'Ubicación no especificada';
                                $description = $rec->description;
                                $createdAt = $rec->created_at->format('Y-m-d H:i:s');
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
                                
                                $isNew = $rec->is_new_for_user ?? false;
                            @endphp

                            <details class="reclamo-card {{ $statusClass }}" data-timestamp="{{ $timestamp }}" data-is-new="{{ $isNew ? 'true' : 'false' }}" data-type="plantacion" data-id="{{ $id }}" style="position: relative;">
                                @if($isNew)
                                    <div class="new-dot-indicator" style="position: absolute; left: -14px; top: 15px;">
                                        <x-layouts.notification-badge isDot="true" />
                                    </div>
                                @endif
                                <summary class="reclamo-card-summary">
                                    <div class="card-summary-left">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                            <span class="reclamo-id" style="font-size: 0.85rem; color: #64748b; font-weight: 500;">{{ $rec->tracking_code }}</span>
                                        </div>
                                        <div>
                                            <h3>{{ $typeName }}</h3>
                                            <p class="summary-meta">{{ $streetName }} • {{ $dateFormatted }}</p>
                                        </div>
                                    </div>
                                    <div class="card-summary-right">
                                        <span class="status-badge {{ $statusClass }}" style="white-space: nowrap; text-align: center;">{{ $statusText }}</span>
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

        async function submitCancelClaim(id) {
            const reason = document.getElementById('cancel-reason-' + id).value.trim();
            if (!reason) {
                alert('Por favor ingrese el motivo de la cancelación.');
                return;
            }

            try {
                const response = await fetch(`/api/reclamos/${id}/cancelar`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ cancellation_reason: reason })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Reclamo cancelado exitosamente.');
                    location.reload(); // Recargar para ver los cambios
                } else {
                    alert(data.message || 'Error al cancelar el reclamo.');
                }
            } catch (error) {
                console.error(error);
                alert('Ocurrió un error al intentar cancelar.');
            }
        }

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
            
            items.forEach(item => {
                if (order === 'new') {
                    // Ocultar los que no son nuevos
                    if (item.getAttribute('data-is-new') === 'true') {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                } else {
                    item.style.display = 'block';
                }
            });

            if (order !== 'new') {
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
        }

        document.addEventListener('DOMContentLoaded', () => {
            applyLocalCancellations();

            // Logica para marcar como leido al abrir un <details>
            const detailCards = document.querySelectorAll('.reclamo-card');
            detailCards.forEach(card => {
                card.addEventListener('toggle', function() {
                    if (this.open && this.getAttribute('data-is-new') === 'true') {
                        // Marcarlo como leido localmente
                        this.setAttribute('data-is-new', 'false');
                        
                        // Remover el puntito rojo flotante
                        const dot = this.querySelector('.new-dot-indicator');
                        if (dot) dot.remove();

                        // Restar del menu superior (Mis Reclamos)
                        const claimsBadge = document.getElementById('badge-unread-claims');
                        if (claimsBadge) {
                            let count = parseInt(claimsBadge.innerText) || 0;
                            count--;
                            if (count <= 0) {
                                claimsBadge.remove(); // Si llega a 0, borrar la burbuja entera
                            } else {
                                claimsBadge.innerText = count;
                            }
                        }

                        // Revisar si ya no hay más notificaciones de NADA para borrar el global dot
                        checkGlobalDot();

                        // Llamada para guardar en la BD
                        const type = this.getAttribute('data-type');
                        const id = this.getAttribute('data-id');
                        if (type === 'reclamo') {
                            fetch(`/reclamos/${id}/mark-seen-by-user`, { 
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            });
                        }
                    }
                });
            });
        });

        function checkGlobalDot() {
            const claimsBadge = document.getElementById('badge-unread-claims');
            const msgsBadge = document.getElementById('badge-unread-messages');
            if (!claimsBadge && !msgsBadge) {
                const globalDot = document.getElementById('badge-global-dot');
                if (globalDot) globalDot.remove();
            }
        }
    </script>
@endsection

