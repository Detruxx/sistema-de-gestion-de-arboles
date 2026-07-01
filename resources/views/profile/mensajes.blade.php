@extends('layouts.app')

@section('title', 'Bandeja de Mensajes | TreeBA')

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
                <p class="listing-count">Mostrando {{ count($mensajes) }} mensajes (Ordenados del más antiguo al más reciente)</p>
                
                <div class="reclamos-list">
                    @foreach($mensajes as $msg)
                        @php
                            $id = is_array($msg) ? $msg['id'] : $msg->id;
                            $userName = is_array($msg) ? $msg['user_name'] : ($msg->user ? $msg->user->name : 'Vecino Anónimo');
                            $userEmail = is_array($msg) ? $msg['user_email'] : ($msg->user ? $msg->user->email : 'sin-email@treeba.gob.ar');
                            $messageText = is_array($msg) ? $msg['message'] : $msg->message;
                            $status = is_array($msg) ? $msg['status'] : $msg->status;
                            $createdAt = is_array($msg) ? $msg['created_at'] : $msg->created_at->format('Y-m-d H:i:s');
                            $dateFormatted = date('d/m/Y H:i', strtotime($createdAt));

                            $statusClass = $status === 'unread' ? 'unread' : 'read';
                            $statusText = $status === 'unread' ? 'Nuevo' : 'Leído';
                        @endphp

                        <div class="message-card {{ $statusClass }}">
                            <div class="message-card-header">
                                <div class="message-sender-info">
                                    <div class="sender-avatar">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3>{{ $userName }}</h3>
                                        <p class="sender-meta">{{ $userEmail }} • {{ $dateFormatted }} hs</p>
                                    </div>
                                </div>
                                <div class="message-status">
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </div>
                            </div>
                            
                            <div class="message-card-body">
                                <p>{{ $messageText }}</p>
                            </div>

                            @if($status === 'unread')
                                <div class="message-card-footer">
                                    <form action="{{ route('contact.read', $id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-main-cta btn-action-read" style="padding: 8px 16px; font-size: 0.85rem; border-radius: 6px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Marcar como leído
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
@endsection
