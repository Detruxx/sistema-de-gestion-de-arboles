@extends('layouts.app')

@section('title', 'Bandeja de Reclamos | TreeBA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <main class="profile-page-container">
        <section class="profile-header reveal">
            <h1 class="hero-title">Gestión de Reclamos</h1>
            <p class="section-subtitle">Panel del Inspector: Revisa, despacha, completa o descarta reclamos de vecinos.</p>
        </section>

        <div class="reclamos-container reveal delay-1">
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 25px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($reclamos) === 0)
                <div class="no-records-card">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h3>No hay reclamos pendientes de revisión</h3>
                    <p>¡Buen trabajo! Todas las incidencias reportadas han sido procesadas.</p>
                </div>
            @else
                <p class="listing-count">Mostrando {{ count($reclamos) }} reclamos pendientes (Ordenados del más antiguo al más reciente)</p>
                
                <div class="reclamos-list">
                    @foreach($reclamos as $rec)
                        @php
                            $id = is_array($rec) ? $rec['id'] : $rec->id;
                            $status = is_array($rec) ? $rec['status'] : $rec->status;
                            $typeName = is_array($rec) ? $rec['type_name'] : ($rec->Request_Type ? $rec->Request_Type->type_name : 'Reclamo General');
                            $streetName = is_array($rec) ? $rec['street_name'] : ($rec->street ? $rec->street->street_name . ' ' . $rec->street->street_number : 'Ubicación no especificada');
                            $description = is_array($rec) ? $rec['description'] : $rec->description;
                            $treeId = is_array($rec) ? $rec['tree_id'] : $rec->tree_id;
                            $treeSpecie = is_array($rec) ? $rec['tree_specie'] : ($rec->tree && $rec->tree->species ? $rec->tree->species->common_name : null);
                            $userName = is_array($rec) ? $rec['user_name'] : ($rec->user ? $rec->user->name : 'Vecino Anónimo');
                            $userEmail = is_array($rec) ? $rec['user_email'] : ($rec->user ? $rec->user->email : 'sin-email@treeba.gob.ar');
                            $createdAt = is_array($rec) ? $rec['created_at'] : $rec->created_at->format('Y-m-d H:i:s');
                            $dateFormatted = date('d/m/Y', strtotime($createdAt));

                            $statusClass = 'open';
                            $statusText = 'En revisión';
                            if ($status === 'resolved') {
                                $statusClass = 'resolved';
                                $statusText = 'Completado';
                            }
                        @endphp

                        <details class="reclamo-card {{ $statusClass }}">
                            <summary class="reclamo-card-summary">
                                <div class="card-summary-left">
                                    <span class="reclamo-id">#{{ $id }}</span>
                                    <div>
                                        <h3>{{ $typeName }}</h3>
                                        <p class="summary-meta">{{ $streetName }} • por <strong>{{ $userName }}</strong></p>
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
                                    <h4>Detalles del Reclamo</h4>
                                    <p>{{ $description }}</p>
                                </div>

                                <div class="details-meta-grid">
                                    <div class="meta-box">
                                        <strong>Dirección del reporte</strong>
                                        <span>{{ $streetName }}</span>
                                    </div>
                                    <div class="meta-box">
                                        <strong>Fecha y Hora de ingreso</strong>
                                        <span>{{ date('d/m/Y H:i', strtotime($createdAt)) }} hs</span>
                                    </div>
                                    <div class="meta-box">
                                        <strong>Reportado por</strong>
                                        <span>{{ $userName }} ({{ $userEmail }})</span>
                                    </div>
                                    @if($treeId)
                                        <div class="meta-box">
                                            <strong>Árbol Afectado</strong>
                                            <span>ID #{{ $treeId }} @if($treeSpecie)({{ $treeSpecie }})@endif</span>
                                        </div>
                                    @endif
                                </div>

                                @if($status === 'open')
                                    <div class="action-buttons-box">
                                        <!-- Botón Completar -->
                                        <form action="{{ route('profile.reclamo.status', $id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="action" value="completar">
                                            <button type="submit" class="btn-main-cta btn-action-success">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                Completar Reclamo
                                            </button>
                                        </form>

                                        <!-- Botón Descartar -->
                                        <form action="{{ route('profile.reclamo.status', $id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="action" value="descartar">
                                            <button type="submit" class="btn-main-cta btn-action-danger">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                Descartar Reclamo
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="inspector-response-box">
                                        <div class="response-header">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            <strong>Reclamo Completado</strong>
                                        </div>
                                        <p>Has marcado este reclamo como resuelto con éxito.</p>
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
