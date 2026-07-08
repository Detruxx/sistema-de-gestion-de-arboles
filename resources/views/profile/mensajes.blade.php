@extends('layouts.app')

@section('title', 'Bandeja de Mensajes | Arborea')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <main class="profile-page-container">
        <section class="profile-header reveal">
            <h1 class="hero-title">Mensajes Recibidos</h1>
            <p class="section-subtitle">Bandeja de entrada: Mensajes y consultas de vecinos de la Ciudad.</p>
        </section>

        <div class="reclamos-container reveal delay-1">
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 25px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($mensajes) === 0)
                <div class="no-records-card">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <h3>Bandeja de entrada vacía</h3>
                    <p>No tienes ningún mensaje pendiente de vecinos en el sistema.</p>
                </div>
            @else
                <div class="list-header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <p class="listing-count" style="margin: 0;">Mostrando <span class="count-val">{{ count($mensajes) }}</span> mensajes</p>
                    <div class="filter-dropdown">
                        <select id="sort-mensajes" class="form-control sort-select" onchange="sortList('mensajes-list-container', this.value)" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-family: inherit; font-size: 0.95rem; cursor: pointer; background-color: white;">
                            <option value="asc">Más antiguo a más nuevo</option>
                            <option value="desc">Más nuevo a más antiguo</option>
                            <option value="new">Nuevos/Sin Leer</option>
                        </select>
                    </div>
                </div>
                
                <div class="reclamos-list" id="mensajes-list-container">
                    @foreach($mensajes as $msg)
                        @php
                            $id = is_array($msg) ? $msg['id'] : $msg->id;
                            $userName = is_array($msg) ? $msg['user_name'] : ($msg->user ? $msg->user->name : 'Vecino Anónimo');
                            $userEmail = is_array($msg) ? $msg['user_email'] : ($msg->user ? $msg->user->email : 'sin-email@Arborea.gob.ar');
                            $messageText = is_array($msg) ? $msg['message'] : $msg->message;
                            $status = is_array($msg) ? $msg['status'] : $msg->status;
                            $createdAt = is_array($msg) ? $msg['created_at'] : $msg->created_at->format('Y-m-d H:i:s');
                            $dateFormatted = date('d/m/Y H:i', strtotime($createdAt));
                            $response = is_array($msg) ? ($msg['inspector_response'] ?? null) : ($msg->inspector_response ?? null);

                            $isAnswered = !empty($response);
                            $statusClass = $status === 'unread' ? 'unread' : ($isAnswered ? 'answered' : 'read');
                            $statusText = $status === 'unread' ? 'Nuevo' : ($isAnswered ? 'Respondido' : 'Leído');
                        @endphp

                        <details class="reclamo-card {{ $statusClass }}" style="margin-bottom: 15px;" data-timestamp="{{ strtotime($createdAt) }}" data-is-new="{{ $status === 'unread' ? 'true' : 'false' }}">
                            <summary class="reclamo-card-summary">
                                <div class="card-summary-left">
                                    <span class="reclamo-id" style="background-color: transparent; border: 1px solid var(--admin-border); padding: 8px; border-radius: 12px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3>{{ $userName }}</h3>
                                        <p class="summary-meta">{{ $userEmail }} • {{ $dateFormatted }} hs</p>
                                        <p class="msg-original-preview" style="color: #6b7280; font-size: 0.9rem; margin-top: 5px;">
                                            {{ \Illuminate\Support\Str::limit($messageText, 70) }}
                                        </p>
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
                                <h4 class="details-section-title" style="font-size: 0.85rem; color: #6b7280; text-transform: uppercase; margin-bottom: 10px;">Mensaje Completo:</h4>
                                <div class="message-original-content" style="padding: 15px; background-color: #f9fafb; border-left: 4px solid #d1d5db; border-radius: 4px; margin-bottom: 20px;">
                                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5;">{{ $messageText }}</p>
                                </div>

                                @if($isAnswered)
                                    <div style="padding: 15px; background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 4px;">
                                        <strong style="color: #166534; font-size: 0.85rem; display: block; margin-bottom: 6px; text-transform: uppercase;">Tu Respuesta Oficial:</strong>
                                        <p style="color: #15803d; font-size: 0.95rem; margin: 0; line-height: 1.5;">{{ $response }}</p>
                                    </div>
                                @else
                                    <div class="message-card-footer" style="flex-direction: column; align-items: flex-start; gap: 10px;">
                                        <form action="{{ route('contact.reply', $id) }}" method="POST" style="width: 100%;">
                                            @csrf
                                            <textarea name="reply_message" rows="3" placeholder="Escribe tu respuesta oficial aquí..." required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-family: inherit; font-size: 0.95rem; resize: vertical; margin-bottom: 10px;"></textarea>
                                            
                                            <div style="display: flex; gap: 10px; justify-content: flex-end; width: 100%;">
                                                <button type="submit" class="btn-main-cta" style="padding: 8px 16px; font-size: 0.85rem; border-radius: 6px; background-color: #16a34a; color: white;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                                                    Enviar Respuesta
                                                </button>
                                            </div>
                                        </form>

                                        @if($status === 'unread')
                                            <form action="{{ route('contact.read', $id) }}" method="POST" style="width: 100%; display: flex; justify-content: flex-end; margin-top: 5px;">
                                                @csrf
                                                <button type="submit" style="display: flex; align-items: center; padding: 6px 12px; font-size: 0.8rem; border-radius: 6px; background-color: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db; box-shadow: none; cursor: pointer; transition: all 0.2s ease;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    Archivar sin responder (Marcar como leído)
                                                </button>
                                            </form>
                                        @endif
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

@section('scripts')
    <script>
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
                        return timeB - timeA; // Más nuevo a más antiguo
                    } else {
                        return timeA - timeB; // Más antiguo a más nuevo
                    }
                });
                // Re-append in new order
                items.forEach(item => container.appendChild(item));
            }
        }
    </script>
@endsection


