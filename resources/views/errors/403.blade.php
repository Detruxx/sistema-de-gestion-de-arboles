@extends('layouts.app')

@section('title', 'Acceso Denegado | TreeBA')
@section('navbar-class', 'scrolled')

@section('content')
<main class="error-page-container reveal">
    <div style="margin-bottom: 20px;">
        <img src="{{ asset('img/errors/403.png') }}" alt="403 Acceso Denegado" style="max-width: 280px; opacity: 0.95;">
    </div>
    <div class="error-code">403</div>
    <h1 class="error-title">Acceso Denegado</h1>
    <p class="error-message">
        No cuenta con los permisos necesarios para visualizar esta sección. Si cree que esto es un error, por favor inicie sesión o contacte al administrador.
    </p>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="btn-main-cta">Volver al Inicio</a>
        <a href="{{ route('login') }}" class="btn-secondary">Iniciar Sesión</a>
    </div>
</main>
@endsection
