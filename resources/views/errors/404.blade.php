@extends('layouts.app')

@section('title', 'Página no encontrada | TreeBA')
@section('navbar-class', 'scrolled')

@section('content')
<main class="error-page-container reveal">
    <div style="margin-bottom: 20px;">
        <img src="{{ asset('img/errors/404.png') }}" alt="404 No encontrado" style="max-width: 280px; opacity: 0.95;">
    </div>
    <div class="error-code">404</div>
    <h1 class="error-title">Página no encontrada</h1>
    <p class="error-message">
        La dirección a la que intenta acceder no existe o ha sido movida. Por favor, verifique el enlace o regrese a la página principal.
    </p>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="btn-main-cta">Volver al Inicio</a>
    </div>
</main>
@endsection
