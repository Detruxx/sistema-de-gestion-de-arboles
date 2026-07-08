@extends('layouts.app')

@section('title', 'Página no encontrada | Arborea')
@section('navbar-class', 'scrolled')

@section('content')
<main class="error-page-container reveal">
    <div class="error-layout">
        <div class="error-image-col">
            <img src="{{ asset('img/errors/404.webp') }}" alt="404 No encontrado">
        </div>
        <div class="error-text-col">
            <div>
                <div class="error-code">404</div>
                <h1 class="error-title">Página no encontrada</h1>
                <p class="error-message">
                    La dirección a la que intenta acceder no existe o ha sido movida. Por favor, verifique el enlace o regrese a la página principal.
                </p>
            </div>
            <div class="error-actions">
                <a href="{{ url('/') }}" class="btn-main-cta">Volver al Inicio</a>
            </div>
        </div>
    </div>
</main>
@endsection

