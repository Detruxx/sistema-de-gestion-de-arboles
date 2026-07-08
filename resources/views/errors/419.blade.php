@extends('layouts.app')

@section('title', 'Sesión Expirada | Arborea')
@section('navbar-class', 'scrolled')

@section('content')
<main class="error-page-container reveal">
    <div class="error-layout">
        <div class="error-image-col">
            <img src="{{ asset('img/components/warning-tree.webp') }}" alt="419 Sesión Expirada">
        </div>
        <div class="error-text-col">
            <div>
                <div class="error-code">419</div>
                <h1 class="error-title">Sesión Expirada</h1>
                <p class="error-message">
                    Tu sesión ha expirado por inactividad o porque abriste el sistema en otra pestaña. Por razones de seguridad, necesitamos que vuelvas a iniciar sesión para continuar.
                </p>
            </div>
            <div class="error-actions">
                <a href="{{ route('login') }}" class="btn-main-cta">Volver al Login</a>
            </div>
        </div>
    </div>
</main>
@endsection

