@extends('layouts.app')

@section('title', 'Error del Servidor | TreeBA')
@section('navbar-class', 'scrolled')

@section('content')
<main class="error-page-container reveal">
    <div style="margin-bottom: 20px;">
        <img src="{{ asset('img/errors/500.png') }}" alt="500 Error Interno" style="max-width: 280px; opacity: 0.95;">
    </div>
    <div class="error-code">500</div>
    <h1 class="error-title">Error Interno del Servidor</h1>
    <p class="error-message">
        Hemos detectado un inconveniente técnico en nuestro sistema. Nuestro equipo ya ha sido notificado y se encuentra trabajando en la solución. Disculpe las molestias.
    </p>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="btn-main-cta">Volver al Inicio</a>
    </div>
</main>
@endsection
