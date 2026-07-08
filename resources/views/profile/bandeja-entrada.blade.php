@extends('layouts.app')

@section('title', 'Bandeja de Entrada | TreeBA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/bandeja-entrada.css') }}">
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
                            <option value="new">Nuevos/Sin Leer</option>
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
                            
                            if ($status === 'answered' || $response) {
                                $statusClass = 'resolved';
                                $statusText = 'Respondido';
                            } elseif ($status === 'read') {
                                $statusClass = 'open'; // Mantenemos el estilo de pendiente/abierto
                                $statusText = 'En Revisión'; // Pero indicamos que ya lo leyeron
                            }

                            // SKELETON PARA EL BACKEND: Reemplazar false por la lógica real
                            $isNew = is_array($msg) ? ($msg['is_new'] ?? false) : false;
                        @endphp

                        <details class="reclamo-card {{ $statusClass }}" data-timestamp="{{ $timestamp }}" data-is-new="{{ $isNew ? 'true' : 'false' }}" data-type="mensaje" data-id="{{ $id }}" style="position: relative;">
                            @if($isNew)
                                <div class="new-dot-indicator" style="position: absolute; left: -14px; top: 15px;">
                                    <x-layouts.notification-badge isDot="true" />
                                </div>
                            @endif
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
                                @if($statusClass === 'resolved' && $response)
                                    <div class="message-full-response" style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 15px; margin-bottom: 15px; border-radius: 4px;">
                                        <h4 style="color: #166534; margin-top: 0; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                            Respuesta Oficial del Inspector
                                        </h4>
                                        <p style="color: #15803d; font-size: 0.9rem; margin-bottom: 0;">{{ $response }}</p>
                                    </div>
                                    <h4 class="details-section-title" style="font-size: 0.85rem; color: #6b7280; text-transform: uppercase;">Tu mensaje original:</h4>
                                @else
                                    <div class="message-full-response">
                                        <h4 class="details-section-title">Aviso Importante</h4>
                                        <p>Tu consulta fue recibida y se encuentra en revisión. En breve recibirás una respuesta oficial por este medio.</p>
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
            
            items.forEach(item => {
                if (order === 'new') {
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
                
                items.forEach(item => container.appendChild(item));
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const detailCards = document.querySelectorAll('.reclamo-card');
            detailCards.forEach(card => {
                card.addEventListener('toggle', function() {
                    if (this.open && this.getAttribute('data-is-new') === 'true') {
                        // Marcarlo como leido localmente
                        this.setAttribute('data-is-new', 'false');
                        
                        // Remover el puntito rojo flotante
                        const dot = this.querySelector('.new-dot-indicator');
                        if (dot) dot.remove();

                        // Restar del menu superior (Bandeja de Entrada)
                        const msgsBadge = document.getElementById('badge-unread-messages');
                        if (msgsBadge) {
                            let count = parseInt(msgsBadge.innerText) || 0;
                            count--;
                            if (count <= 0) {
                                msgsBadge.remove();
                            } else {
                                msgsBadge.innerText = count;
                            }
                        }

                        // Revisar si ya no hay más notificaciones
                        checkGlobalDot();

                        // SKELETON PARA EL BACKEND
                        // const id = this.getAttribute('data-id');
                        // fetch(`/api/mark-read/mensaje/${id}`, { method: 'POST' });
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
