@extends('layouts.app')

@section('title', 'Permisos y Normativas | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-permisos', 'active')

@section('content')
    <main class="tramites-page-container">
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Permisos y Normativas</h1>
            <p class="section-subtitle">
                De acuerdo con la Ley N° 3263, toda intervención sobre el arbolado público requiere de una autorización formal de la comuna.
            </p>
        </section>

        <section style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 40px; position: relative; z-index: 10;" class="reveal delay-1">
            
            <div style="background-color: #ffffff; border: 2px solid rgba(45, 122, 79, 0.2); border-radius: 16px; padding: 40px; box-shadow: 0 15px 35px rgba(45, 122, 79, 0.08);">
                <h3 style="font-family: var(--font-display); font-size: 1.4rem; color: var(--deep-canopy); margin-bottom: 20px; font-weight: 700;">
                    Tipos de Trámites Disponibles
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="border-left: 4px solid var(--spring-leaf); padding-left: 20px; margin-bottom: 10px;">
                        <h4 style="font-size: 1.1rem; color: var(--deep-canopy); margin-bottom: 5px; font-weight: 600;">1. Solicitud de Extracción o Traslado</h4>
                        <p style="color: var(--forest-night); font-size: 0.95rem; line-height: 1.5;">
                            Válido únicamente si el ejemplar compromete la seguridad de las personas, presenta una inclinación severa irreversible o produce daños estructurales irreparables en el frente edilicio.
                        </p>
                    </div>

                    <div style="border-left: 4px solid var(--living-moss); padding-left: 20px; margin-bottom: 10px;">
                        <h4 style="font-size: 1.1rem; color: var(--deep-canopy); margin-bottom: 5px; font-weight: 600;">2. Solicitud de Poda Excepcional</h4>
                        <p style="color: var(--forest-night); font-size: 0.95rem; line-height: 1.5;">
                            Reservado cuando las ramas interfieren con el cableado de alta tensión, luminarias públicas o interfieren con las cámaras de seguridad urbana.
                        </p>
                    </div>

                    <div style="border-left: 4px solid var(--forest-night); padding-left: 20px;">
                        <h4 style="font-size: 1.1rem; color: var(--deep-canopy); margin-bottom: 5px; font-weight: 600;">3. Modificación de Veredas (Cazuelas)</h4>
                        <p style="color: var(--forest-night); font-size: 0.95rem; line-height: 1.5;">
                            Permiso para agrandar la cazuela del árbol para optimizar el paso del agua de lluvia y evitar el levantamiento de baldosas.
                        </p>
                    </div>
                </div>
            </div>

            <div style="background-color: rgba(91, 191, 140, 0.08); border: 2px dashed var(--spring-leaf); border-radius: 16px; padding: 35px; text-align: center;">
                <h4 style="font-family: var(--font-display); font-size: 1.25rem; color: var(--deep-canopy); margin-bottom: 15px; font-weight: 700;">
                    Documentación Necesaria para iniciar el Trámite
                </h4>
                <ul style="list-style: none; padding: 0; display: inline-flex; flex-direction: column; text-align: left; gap: 10px; color: var(--forest-night); font-size: 0.95rem; margin-bottom: 25px;">
                    <li>✓ Documento Nacional de Identidad (DNI) del solicitante.</li>
                    <li>✓ Acreditación de domicilio (Ej: impuesto o título de propiedad).</li>
                    <li>✓ Fotografías claras del estado actual del árbol y el daño reclamado.</li>
                </ul>
                <div>
                    <a href="https://buenosaires.gob.ar/tramites" target="_blank" class="btn-main-cta">
                        Iniciar Trámite en GCBA
                    </a>
                </div>
            </div>

        </section>
    </main>
@endsection
