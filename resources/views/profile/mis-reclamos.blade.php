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

        <div class="reclamos-container reveal delay-1">
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
                <p class="listing-count">Mostrando {{ count($reclamos) }} reclamos (Ordenados del más reciente al más antiguo)</p>
                
                <div class="reclamos-list">
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
                        @endphp

                        <details class="reclamo-card {{ $statusClass }}">
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
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
@endsection
