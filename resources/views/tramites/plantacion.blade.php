@extends('layouts.app')

@section('title', 'Solicitud de Plantación | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')
@section('active-plantacion', 'active')

@section('content')
    <main class="tramites-page-container">
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Solicitud de Plantación</h1>
            <p class="section-subtitle">
                Solicita la plantación de un nuevo ejemplar en la vereda de tu hogar. La comuna evaluará y proveerá la especie adecuada.
            </p>
        </section>

        <section style="max-width: 800px; margin: 0 auto; position: relative; z-index: 10;" class="reveal delay-1">
            <form class="contact-form" onsubmit="event.preventDefault(); alert('Solicitud enviada con éxito (Simulación).');">
                <div class="form-group">
                    <label for="ancho-vereda">Ancho Estimado de la Vereda</label>
                    <select id="ancho-vereda" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%;" required>
                        <option value="">Selecciona una opción...</option>
                        <option value="angosta">Angosta (Menos de 2 metros)</option>
                        <option value="media">Media (Entre 2 y 3.5 metros)</option>
                        <option value="ancha">Ancha (Más de 3.5 metros)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="cazuela-estado">¿La cazuela (espacio de tierra) está disponible?</label>
                    <select id="cazuela-estado" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%;" required>
                        <option value="">Selecciona una opción...</option>
                        <option value="si">Sí, está abierta y con tierra suelta</option>
                        <option value="cemento">No, la vereda está completamente cementada</option>
                        <option value="tocon">No, hay un tronco/muñón viejo que debe extraerse primero</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="direccion-solicitud">Dirección Exacta</label>
                    <input type="text" id="direccion-solicitud" placeholder="Ej: Av. Rivadavia 4800, Caballito" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%;" required>
                </div>

                <div class="form-group" style="margin-top: 25px; display: flex; flex-direction: row; align-items: flex-start; gap: 10px;">
                    <input type="checkbox" id="compromiso" style="margin-top: 6px; cursor: pointer;" required>
                    <label for="compromiso" style="font-weight: 500; font-size: 0.95rem; color: var(--forest-night); line-height: 1.4;">
                        Me comprometo a cuidar y regar el árbol regularmente durante sus primeros 3 años de vida para asegurar su crecimiento saludable.
                    </label>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-main-cta">Enviar Solicitud</button>
                </div>
            </form>
        </section>
    </main>
@endsection
