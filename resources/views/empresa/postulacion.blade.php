@extends('layouts.app')

@section('title', 'Postulación de Empresa | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-tramites', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/empresa/postulacion.css') }}">
@endsection

@section('content')
    <main class="tramites-page-container" style="position: relative; overflow: hidden;">
        <div class="bg-blurred-image plantacion-bg" style="background: linear-gradient(135deg, rgba(45, 122, 79, 0.05) 0%, rgba(20, 50, 30, 0.08) 100%);"></div>
        
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Postulación de Empresa</h1>
            <p class="section-subtitle">
                ¿Querés ser contratista oficial de TreeBA? Completá los datos de tu empresa para sumarte al mantenimiento del arbolado público de la ciudad.
            </p>
        </section>

        <section class="plantacion-form-container reveal delay-1" style="max-width: 700px; margin: 0 auto; background: var(--paper-white); padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid rgba(45, 122, 79, 0.1); z-index: 5; margin-bottom: 80px;">
            
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

                <!-- Especialidades -->
                <div class="form-group" style="margin-top: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: bold;">Especialidad / Servicios Disponibles</label>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="services" value="Poda y Balanceo" style="width: 18px; height: 18px;">
                            <span>Poda Correctiva y Balanceo de Copa</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="services" value="Extracción" style="width: 18px; height: 18px;">
                            <span>Extracción de Árboles Secos / Destoconado</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="services" value="Cazuelas" style="width: 18px; height: 18px;">
                            <span>Saneamiento, Ensanche y Reparación de Cazuelas</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="services" value="Tratamientos" style="width: 18px; height: 18px;">
                            <span>Tratamientos Fitosanitarios contra Plagas</span>
                        </label>
                    </div>
                </div>

                <!-- Acciones del Formulario -->
                <div class="form-actions" style="margin-top: 35px; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 25px; display: flex; justify-content: flex-end;">
                    <button type="submit" id="btn-submit-postulation" class="btn-main-cta" style="width: 100%; padding: 15px; font-size: 1.1rem; cursor: not-allowed; opacity: 0.6;" disabled>
                        Enviar Postulación
                    </button>
                </div>

            </form>
        </section>
    </main>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        let cuitVerified = false;

        async function verifyCuit() {
            const cuitInput = document.getElementById('company-cuit');
            const cuitVal = cuitInput.value.trim();
            const msgEl = document.getElementById('cuit-validation-msg');
            const nameInput = document.getElementById('company-name');
            const submitBtn = document.getElementById('btn-submit-postulation');
            const btnText = document.getElementById('btn-verify-text');
            const btnSpinner = document.getElementById('btn-verify-spinner');

            if (!cuitVal) {
                msgEl.style.display = 'block';
                msgEl.style.color = '#ef4444';
                msgEl.innerHTML = 'Por favor, ingrese un CUIT.';
                return;
            }

            // Simple CUIT format validation
            const cleanCuit = cuitVal.replace(/[^0-9]/g, '');
            if (cleanCuit.length !== 11) {
                msgEl.style.display = 'block';
                msgEl.style.color = '#ef4444';
                msgEl.innerHTML = 'CUIT inválido. Debe contener 11 dígitos.';
                return;
            }

            // UI feedback
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-block';
            msgEl.style.display = 'none';

            // Simulate API call to AFIP
            await new Promise(resolve => setTimeout(resolve, 1500));

            btnSpinner.style.display = 'none';
            btnText.style.display = 'inline-block';

            // Mock check results
            cuitVerified = true;
            msgEl.style.display = 'block';
            msgEl.style.color = '#22c55e';
            msgEl.innerHTML = '✓ CUIT verificado en AFIP y Registro Comunal. Estado: Activo.';
            
            // Auto fill reason / company name based on cuit ending or defaults
            nameInput.disabled = false;
            if (cleanCuit.endsWith('3')) {
                nameInput.value = 'Mantenimiento y Espacios Verdes del Norte';
            } else if (cleanCuit.endsWith('7')) {
                nameInput.value = 'Arbolado Urbano Rioplatense S.A.';
            } else {
                nameInput.value = 'Servicios Forestales ' + cleanCuit.substring(2, 6) + ' S.R.L.';
            }
            
            cuitInput.disabled = true;
            document.getElementById('btn-verify-cuit').disabled = true;
            document.getElementById('btn-verify-cuit').style.opacity = '0.7';
            document.getElementById('btn-verify-cuit').style.cursor = 'default';
            submitBtn.disabled = false;
            submitBtn.style.cursor = 'pointer';
            submitBtn.style.opacity = '1';
        }

        function handlePostulationSubmit(event) {
            event.preventDefault();
            if (!cuitVerified) return;

            const name = document.getElementById('company-name').value;
            const cuit = document.getElementById('company-cuit').value;
            const email = document.getElementById('company-email').value;
            const phone = document.getElementById('company-phone').value;
            const address = document.getElementById('company-address').value;

            // Get selected services
            const checkedServices = [];
            const checkboxes = document.querySelectorAll('input[name="services"]:checked');
            checkboxes.forEach(cb => checkedServices.push(cb.value));

            // Create new company application object
            const newApp = {
                id: Date.now(),
                company_name: name,
                cuit: cuit,
                contact_email: email,
                phone: phone,
                address: address,
                services: checkedServices,
                status: 'Pendiente',
                total_expenses: 0,
                work_orders: []
            };

            // Save to localStorage
            let currentApps = JSON.parse(localStorage.getItem('company_applications') || '[]');
            currentApps.push(newApp);
            localStorage.setItem('company_applications', JSON.stringify(currentApps));

            // Show success card
            document.getElementById('company-postulation-form').style.display = 'none';
            document.getElementById('postulacion-success-card').style.display = 'block';
            document.getElementById('success-company-name').innerText = name;
            document.getElementById('success-ref-id').innerText = 'REF-' + newApp.id;
        }
    </script>
@endsection
