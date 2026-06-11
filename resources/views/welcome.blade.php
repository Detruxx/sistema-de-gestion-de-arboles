@extends('layouts.app')

@section('content')
    <main>
        <section class="hero">
            <canvas id="hero-canvas"></canvas>
            
            <div class="hero-content">
                <h1 class="hero-title">El bosque urbano<br>en tus manos</h1>
                <p class="hero-description">
                    Plataforma de ciencia ciudadana para mapear, reportar y aprender sobre el arbolado de la Ciudad de Buenos Aires.
                </p>
                <a href="/mapa" class="btn-main-cta">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" y1="3" x2="9" y2="18"></line><line x1="15" y1="6" x2="15" y2="21"></line></svg>
                    ABRIR MAPA
                </a>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/navbar.js') }}"></script>
    <script src="{{ asset('js/hero-canvas.js') }}"></script>
@endpush