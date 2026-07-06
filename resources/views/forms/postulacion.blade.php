@extends('layouts.app')

@section('title', 'Postulación de Empresa | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/forms/company_postulation.css') }}">
@endsection

@section('content')
    <main class="split-layout-container">
        <div class="split-left">
            <section class="cuidados-header reveal">
                <h1 class="hero-title" style="color: var(--deep-canopy) !important; text-align: left;">Postulación de Empresa</h1>
                <p class="section-subtitle" style="color: var(--forest-night) !important; text-align: left;">
                    ¿Querés ser contratista oficial de TreeBA? Completá los datos de tu empresa para sumarte al mantenimiento del arbolado público de la ciudad.
                </p>
            </section>

            <section class="plantacion-form-container reveal delay-1">
                
                <div id="postulacion-success-card" style="display: none; text-align: center; padding: 20px 0;">
                    <div style="width: 80px; height: 80px; background: rgba(34, 197, 94, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h2 style="font-family: var(--font-display); color: var(--forest-night); margin-bottom: 12px; font-size: 2rem;">¡Postulación Recibida!</h2>
                    <p style="color: var(--forest-night); opacity: 0.8; font-size: 1.1rem; line-height: 1.6; max-width: 500px; margin: 0 auto 30px;">
                        La postulación de <strong id="success-company-name"></strong> se ha registrado correctamente en nuestro sistema y está en estado <strong>Pendiente de Validación</strong>.
                    </p>
                    <div style="background: rgba(45, 122, 79, 0.05); border-radius: 12px; padding: 20px; text-align: left; max-width: 450px; margin: 0 auto 30px; border: 1px solid rgba(45, 122, 79, 0.1);">
                        <p style="margin: 0 0 8px 0; font-size: 0.9rem; color: #555;"><strong>Número de Referencia:</strong> <span id="success-ref-id"></span></p>
                        <p style="margin: 0; font-size: 0.9rem; color: #555;"><strong>Próximos pasos:</strong> Un Inspector del área de Arbolado de la Comuna 13 verificará la documentación y la habilitará en el Panel de Control.</p>
                    </div>
                    <a href="/" class="btn-main-cta" style="display: inline-block; text-decoration: none; padding: 12px 30px;">Volver al Inicio</a>
                </div>

                <form id="company-postulation-form" class="contact-form" onsubmit="handlePostulationSubmit(event)">
                    
                    <!-- CUIT / Verificación -->
                    <div class="form-group">
                        <label for="company-cuit" style="display: block; margin-bottom: 8px; font-weight: 600;">CUIT de la Empresa</label>
                        <div class="input-with-button" style="display: flex; gap: 10px;">
                            <input type="text" id="company-cuit" class="form-control" placeholder="Ej: 30-12345678-9" required style="flex-grow: 1;">
                            <button type="button" id="btn-verify-cuit" class="btn-main-cta" onclick="verifyCuit()" style="background-color: var(--deep-canopy); border: 1px solid var(--living-moss); padding: 12px 20px; font-size: 0.95rem; border-radius: 8px; flex-shrink: 0; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <span id="btn-verify-text">Verificar CUIT</span>
                                <span id="btn-verify-spinner" style="display: none; width: 16px; height: 16px; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite;"></span>
                            </button>
                        </div>
                        <div id="cuit-validation-msg" style="margin-top: 8px; font-size: 0.9rem; font-weight: 500; display: none;"></div>
                    </div>

                    <!-- Razón Social -->
                    <div class="form-group">
                        <label for="company-name" style="display: block; margin-bottom: 8px; font-weight: 600;">Razón Social / Nombre de la Empresa</label>
                        <input type="text" id="company-name" class="form-control" placeholder="Se completará al verificar el CUIT o ingrese manualmente" required disabled>
                    </div>

                    <!-- Email de contacto -->
                    <div class="form-group">
                        <label for="company-email" style="display: block; margin-bottom: 8px; font-weight: 600;">Correo Electrónico de Contacto</label>
                        <input type="email" id="company-email" class="form-control" placeholder="Ej: contacto@miempresa.com" required>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group">
                        <label for="company-phone" style="display: block; margin-bottom: 8px; font-weight: 600;">Teléfono de Contacto</label>
                        <input type="tel" id="company-phone" class="form-control" placeholder="Ej: +54 11 4444-4444" required>
                    </div>

                    <!-- Dirección de Sede -->
                    <div class="form-group">
                        <label for="company-address" style="display: block; margin-bottom: 8px; font-weight: 600;">Dirección Fiscal / Sede Operativa</label>
                        <input type="text" id="company-address" class="form-control" placeholder="Ej: Av. del Libertador 4500, CABA" required>
                    </div>

                    <!-- Acciones del Formulario -->
                    <div class="form-actions" style="margin-top: 20px; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 15px; display: flex; justify-content: flex-end;">
                        <button type="submit" id="btn-submit-postulation" class="btn-main-cta" style="width: 100%; padding: 12px; font-size: 1.05rem; cursor: not-allowed; opacity: 0.6;" disabled>
                            Enviar Postulación
                        </button>
                    </div>

                </form>
            </section>
        </div>
        <div class="split-right"></div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/forms/company_postulation.js') }}"></script>
@endsection

