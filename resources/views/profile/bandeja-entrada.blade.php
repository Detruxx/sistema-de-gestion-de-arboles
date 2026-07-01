@extends('layouts.app')

@section('title', 'Bandeja de Entrada | TreeBA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <style>
        .msg-response-preview {
            font-size: 0.95rem;
            color: var(--forest-night);
            font-style: italic;
            border-left: 3px solid var(--living-moss);
            padding-left: 10px;
            margin-top: 8px;
        }
        .msg-original-preview {
            font-size: 0.95rem;
            color: var(--forest-night);
            opacity: 0.8;
            margin-top: 8px;
        }
        
        .message-full-response {
            background-color: #e8f5e9;
            border: 1px solid #a5d6a7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .message-full-response h4 {
            color: #2e7d32;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font-display);
        }
        .message-full-response p {
            color: #1b5e20;
            font-size: 1.05rem;
            margin: 0;
            line-height: 1.6;
        }
        
        .message-original-content {
            background-color: rgba(45, 122, 79, 0.05);
            border-left: 4px solid rgba(45, 122, 79, 0.3);
            padding: 15px 20px;
            border-radius: 0 8px 8px 0;
        }
        .message-original-content h5 {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: var(--deep-canopy);
            margin-bottom: 8px;
            opacity: 0.8;
            font-family: var(--font-display);
        }
    </style>
@endsection

@section('content')
    <main class="profile-page-container">
        <section class="profile-header reveal">
            <h1 class="hero-title">Bandeja de Entrada</h1>
            <p class="section-subtitle">Revisa el historial de tus consultas y las respuestas oficiales de la comuna.</p>
        </section>

        <div class="reclamos-container reveal delay-1">
            @if(count($mensajes) === 0)
                <div class="no-records-card">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <h3>No tienes mensajes</h3>
                    <p>Cualquier consulta que envíes a través del formulario de contacto aparecerá aquí.</p>
                    <a href="/#contacto" class="btn-main-cta" style="margin-top: 15px;">Ir a Contacto</a>
                </div>
            @else
                <div class="list-header-bar">
                    <p class="listing-count">Mostrando <span class="count-val">{{ count($mensajes) }}</span> mensajes</p>
                    <div class="filter-dropdown">
                        <select id="sort-mensajes" class="form-control sort-select" onchange="sortList('mensajes-list-container', this.value)">
                            <option value="desc">Más nuevo a más antiguo</option>
                            <option value="asc">Más antiguo a más nuevo</option>
                        </select>
                    </div>
                </div>
                
                <div class="reclamos-list" id="mensajes-list-container">
                    @foreach($mensajes as $msg)
                        @php
                            $id = is_array($msg) ? $msg['id'] : $msg->id;
                            $status = is_array($msg) ? $msg['status'] : $msg->status;
                            $message = is_array($msg) ? $msg['message'] : $msg->message;
                            $response = is_array($msg) ? ($msg['inspector_response'] ?? null) : null;
                            $createdAt = is_array($msg) ? $msg['created_at'] : $msg->created_at->format('Y-m-d H:i:s');
                            $dateFormatted = date('d/m/Y', strtotime($createdAt));
                            $timestamp = strtotime($createdAt);

                            $statusClass = 'open';
                            $statusText = 'Pendiente';
                            if ($status === 'answered' || $status === 'read' || $response) {
                                $statusClass = 'resolved';
                                $statusText = 'Respondido';
                            }
                        @endphp

                        <details class="reclamo-card {{ $statusClass }}" data-timestamp="{{ $timestamp }}">
                            <summary class="reclamo-card-summary">
                                <div class="card-summary-left">
                                    <span class="reclamo-id" style="background-color: transparent; border: 1px solid var(--living-moss); padding: 8px; border-radius: 12px;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            @if($statusClass === 'resolved')
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                <path d="M9 10h.01"></path><path d="M15 10h.01"></path><path d="M12 10h.01"></path>
                                            @else
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            @endif
                                        </svg>
                                    </span>
                                    <div>
                                        <h3>{{ $statusClass === 'resolved' ? 'Respuesta del Inspector' : 'Consulta Enviada' }}</h3>
                                        <p class="summary-meta">{{ $dateFormatted }}</p>
                                        @if($statusClass === 'resolved')
                                            <p class="msg-response-preview">{{ \Illuminate\Support\Str::limit($response, 60) }}</p>
                                        @else
                                            <p class="msg-original-preview">{{ \Illuminate\Support\Str::limit($message, 60) }}</p>
                                        @endif
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
                                @if($statusClass === 'resolved')
                                    <div class="message-full-response">
                                        <h4>
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                            Respuesta Oficial
                                        </h4>
                                        <p>{{ $response }}</p>
                                    </div>
                                @else
                                    <div class="details-section" style="margin-bottom: 20px;">
                                        <div style="display: flex; align-items: center; gap: 8px; color: #f57f17; font-weight: 600; margin-bottom: 10px;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            Aguardando respuesta de la Comuna...
                                        </div>
                                    </div>
                                @endif

                                <div class="message-original-content">
                                    <h5>Tu Mensaje Original:</h5>
                                    <p style="margin: 0; color: var(--forest-night); font-size: 0.95rem; line-height: 1.5;">{{ $message }}</p>
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
@endsection

@section('scripts')
    <script>
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
            
            items.forEach(item => container.appendChild(item));
        }
    </script>
@endsection
