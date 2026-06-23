@extends('layouts.app')

@section('canvas')
    <canvas id="hero-canvas"></canvas>
@endsection

@section('content')
    <main>
        <!-- Seccion hero -->
        <section class="hero">
            <div class="hero-split-container">
                <div class="hero-content-left">
                    <h1 class="hero-title">El bosque urbano<br>en tus <span>manos</span></h1>
                    <p class="hero-description">
                        Plataforma de ciencia ciudadana para mapear, reportar y aprender sobre el arbolado de la Ciudad de Buenos Aires.
                    </p>
                    <a href="/mapa" class="btn-main-cta">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" y1="3" x2="9" y2="18"></line><line x1="15" y1="6" x2="15" y2="21"></line></svg>
                        ABRIR MAPA
                    </a>
                </div>
                <div class="hero-image-right">
                    <div class="organic-image-mask">
                        <img src="{{ asset('img/copa_arboles.png') }}" alt="Arboles urbanos">
                    </div>
                </div>
            </div>
        </section>

        <!-- Seccion especies destacadas -->
        <section class="featured-species-section reveal">
            <!-- Divisor de onda SVG (De oscuro del Hero a claro de Especies) -->
            <div class="wave-divider wave-top">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"></path>
                </svg>
            </div>

            <h2 class="section-title">Especies Emblemáticas</h2>
            <p class="section-subtitle">Conoce las especies más características que embellecen y protegen las calles de Buenos Aires.</p>
            
            <div class="species-grid">
                <!-- Jacarandá -->
                <div class="species-card"
                      data-badge="8% de veredas"
                      data-title="Jacarandá"
                      data-scientific="Jacaranda mimosifolia"
                      data-description="Famoso por su espectacular floración violeta en noviembre. Aporta frescura y biodiversidad a las avenidas de la ciudad. Es un árbol originario de las selvas de montaña del noroeste argentino (Yungas), adaptado perfectamente al clima de Buenos Aires."
                      data-tips="Floración: Flores violáceas/azules en forma de campana que cubren la copa a fines de primavera.;Tamaño: Árbol mediano a grande, que puede alcanzar de 12 a 15 metros de altura.;Follaje: Hojas caducas de aspecto plumoso y delicado, que caen tarde en invierno.;Ubicación: Ideal para veredas anchas y avenidas debido a su copa abierta y raíces moderadas."
                      data-image="{{ asset('img/jacaranda_arbol.png') }}">
                    <div class="species-image">
                        <img src="{{ asset('img/jacaranda_arbol.png') }}" alt="Jacarandá">
                        <span class="species-percentage">8% de veredas</span>
                    </div>
                    <div class="species-info">
                        <h3>Jacarandá</h3>
                        <span class="scientific-name">Jacaranda mimosifolia</span>
                        <p>Famoso por su espectacular floración violeta en noviembre. Aporta frescura y biodiversidad a las avenidas de la ciudad.</p>
                        <span class="read-more-link">
                            Leer más
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </span>
                    </div>
                </div>
                
                <!-- Ceibo -->
                <div class="species-card"
                      data-badge="Flor Nacional"
                      data-title="Ceibo"
                      data-scientific="Erythrina crista-galli"
                      data-description="Nuestra flor nacional. De vistoso color rojo, crece principalmente en parques, plazas y zonas húmedas cercanas a la ribera. Es una especie autóctona de la región del Delta y del Río de la Plata, muy querida por su valor cultural."
                      data-tips="Floración: Flores de color rojo carmín intenso, agrupadas en racimos durante primavera y verano.;Tamaño: Árbol de tamaño mediano, de entre 5 a 10 metros de altura, de tronco tortuoso.;Corteza: Rugosa y suberosa (corchosa), muy característica del paisaje ribereño.;Ubicación: Se desarrolla óptimamente en terrenos húmedos, linderos a cursos de agua o en parques amplios."
                      data-image="{{ asset('img/ceibo_arbol.png') }}">
                    <div class="species-image">
                        <img src="{{ asset('img/ceibo_arbol.png') }}" alt="Ceibo">
                        <span class="species-percentage">Flor Nacional</span>
                    </div>
                    <div class="species-info">
                        <h3>Ceibo</h3>
                        <span class="scientific-name">Erythrina crista-galli</span>
                        <p>Nuestra flor nacional. De vistoso color rojo, crece principalmente en parques, plazas y zonas húmedas cercanas a la ribera.</p>
                        <span class="read-more-link">
                            Leer más
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </span>
                    </div>
                </div>
                
                <!-- Fresno -->
                <div class="species-card"
                      data-badge="38% de veredas"
                      data-title="Fresno Americano"
                      data-scientific="Fraxinus pennsylvanica"
                      data-description="El árbol más abundante en las veredas de Buenos Aires. Provee una sombra tupida en verano y un tono dorado en otoño. Es una especie exótica originaria de América del Norte que se ha adaptado masivamente al ejido urbano porteño."
                      data-tips="Follaje: Hojas caducas que se tornan de color amarillo dorado en otoño antes de caer.;Sombra: Copa globosa muy tupida que reduce la radiación solar sobre el pavimento y fachadas en verano.;Resistencia: Excelente tolerancia a la polución urbana, la poda severa y la compactación del suelo.;Frecuencia: Constituye casi 4 de cada 10 árboles plantados en las veredas de la ciudad."
                      data-image="{{ asset('img/fresno_arbol.png') }}">
                    <div class="species-image">
                        <img src="{{ asset('img/fresno_arbol.png') }}" alt="Fresno">
                        <span class="species-percentage">38% de veredas</span>
                    </div>
                    <div class="species-info">
                        <h3>Fresno Americano</h3>
                        <span class="scientific-name">Fraxinus pennsylvanica</span>
                        <p>El árbol más abundante en las veredas de Buenos Aires. Provee una sombra tupida en verano y un tono dorado en otoño.</p>
                        <span class="read-more-link">
                            Leer más
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seccion sobre nosotros -->
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

                <!-- Estadisticas -->
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

        <!-- Seccion de contacto -->
        <section id="contacto" class="contact-section">
            <div class="contact-container reveal">
                <h2 class="section-title text-center">Escríbenos</h2>
                <p class="section-subtitle">¿Tienes alguna duda o sugerencia sobre el proyecto? Ponte en contacto con nosotros.</p>
                
                <!-- Si esta logueado muestra el formulario, si no muestra el mensaje de que inicie sesion -->
                @auth
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form class="contact-form" action="{{ route('contacto.store') }}" method="POST">
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
        <!-- Modal de Detalles de Especie -->
        <div id="species-modal" class="care-modal-overlay">
            <!-- Botones de navegación del modal -->
            <button class="modal-nav-btn prev" id="species-prev-btn" aria-label="Anterior">&lsaquo;</button>
            <button class="modal-nav-btn next" id="species-next-btn" aria-label="Siguiente">&rsaquo;</button>

            <div class="care-modal-container">
                <button class="care-modal-close" id="species-close-btn">&times;</button>
                <div class="care-modal-layout">
                    <div class="care-modal-content">
                        <span class="care-modal-badge" id="species-modal-badge">CATEGORÍA</span>
                        <h2 class="care-modal-title" id="species-modal-title">Título de la Especie</h2>
                        <p class="care-modal-body" id="species-modal-body">
                            Aquí va el texto detallado...
                        </p>
                        <div class="care-modal-action-box">
                            <h4>Características Principales:</h4>
                            <ul class="care-modal-tips-list" id="species-modal-tips-list">
                                <!-- Lista de tips/características -->
                            </ul>
                        </div>
                    </div>
                    <div class="care-modal-image-panel">
                        <img id="species-modal-image" src="" alt="Especie de árbol">
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/home/hero-canvas.js') }}"></script>
    <script src="{{ asset('js/home/welcome.js') }}"></script>
@endsection