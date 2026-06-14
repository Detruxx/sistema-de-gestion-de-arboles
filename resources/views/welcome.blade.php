@extends('layouts.app')

@section('canvas')
    <canvas id="hero-canvas"></canvas>
@endsection

@section('content')
    <main>
        <section class="hero">
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

        <section id="sobre-nosotros" class="about-section">
            <div class="about-container">
                <div class="about-text reveal">
                    <h2 class="section-title">Nuestra Misión</h2>
                    <p>
                        TreeBA es un proyecto de ciencia ciudadana y colaboración abierta. Buscamos involucrar a los vecinos de la Ciudad de Buenos Aires en el cuidado, reporte y aprendizaje sobre los árboles de su entorno.
                    </p>
                    <p>
                        Creemos que un bosque urbano saludable mejora la calidad de vida de todos, disminuye la temperatura de la ciudad, purifica el aire y embellece nuestras calles.
                    </p>
                </div>
                <div class="about-stats">
                    <div class="stat-card reveal delay-1">
                        <span class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8 21h8M12 21v-6m-3-2.5L12 15l3-2.5"/><path d="M12 3a6 6 0 0 0-5.36 3.29A4 4 0 0 0 3 10a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4 4 4 0 0 0-3.64-3.71A6 6 0 0 0 12 3z"/></svg>
                        </span>
                        <h3 class="stat-number">+350k</h3>
                        <p class="stat-desc">Árboles censados</p>
                    </div>
                    <div class="stat-card reveal delay-2">
                        <span class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </span>
                        <h3 class="stat-number">+12k</h3>
                        <p class="stat-desc">Vecinos activos</p>
                    </div>
                    <div class="stat-card reveal delay-3">
                        <span class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                        </span>
                        <h3 class="stat-number">+8k</h3>
                        <p class="stat-desc">Reclamos resueltos</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contacto" class="contact-section">
            <div class="contact-container reveal">
                <h2 class="section-title text-center">Escríbenos</h2>
                <p class="section-subtitle">¿Tienes alguna duda o sugerencia sobre el proyecto? Ponte en contacto con nosotros.</p>
                
                @auth
                    <form class="contact-form" action="/contacto" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="message">Tu Mensaje</label>
                            <textarea id="message" name="mensaje" placeholder="Escribe tu mensaje aquí..." required rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn-main-cta">Enviar Mensaje</button>
                    </form>
                @else
                    <div class="contact-login-card">
                        <span class="lock-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </span>
                        <p>Para enviarnos un mensaje directo, por favor inicia sesión en tu cuenta.</p>
                        <a href="/login" class="btn-main-cta">Iniciar Sesión</a>
                    </div>
                @endauth
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/hero-canvas.js') }}"></script>
@endsection