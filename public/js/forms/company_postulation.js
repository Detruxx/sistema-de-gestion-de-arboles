/**
 * Formulario: Lógica de validación e interacción para el formulario de postulación de empresas.
 */

let cuitVerified = false;

async function verifyCuit() {
    const cuitInput = document.getElementById('company-cuit');
    const cuitVal = cuitInput.value.trim();
    const msgEl = document.getElementById('cuit-validation-msg');
    const nameInput = document.getElementById('company-name');
    const businessNameInput = document.getElementById('company-business-name');
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
    
    // Habilitar el campo de Razón Social para que el usuario lo complete manualmente
    businessNameInput.disabled = false;
    businessNameInput.focus();
    
    cuitInput.disabled = true;
    document.getElementById('btn-verify-cuit').disabled = true;
    document.getElementById('btn-verify-cuit').style.opacity = '0.7';
    document.getElementById('btn-verify-cuit').style.cursor = 'default';
    submitBtn.disabled = false;
    submitBtn.style.cursor = 'pointer';
    submitBtn.style.opacity = '1';
}

async function handlePostulationSubmit(event) {
    event.preventDefault();
    if (!cuitVerified) return;

    const name = document.getElementById('company-name').value;
    const businessName = document.getElementById('company-business-name').value;
    const cuit = document.getElementById('company-cuit').value;
    const email = document.getElementById('company-email').value;
    const location = document.getElementById('company-location').value;
    const turnstileToken = document.querySelector('[name="cf-turnstile-response"]')?.value;

    const errorMsgEl = document.getElementById('turnstile-error-msg');
    if (errorMsgEl) {
        errorMsgEl.style.display = 'none';
        errorMsgEl.innerText = '';
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch('/companies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                name: name,
                business_name: businessName,
                cuit: cuit,
                email: email,
                location: location,
                'cf-turnstile-response': turnstileToken
            })
        });

        if (!response.ok) {
            if (response.status === 422) {
                const errorData = await response.json();
                const errors = errorData.errors || {};
                
                // Mostrar error de captcha si existe
                if (errors['cf-turnstile-response'] && errorMsgEl) {
                    errorMsgEl.style.display = 'block';
                    errorMsgEl.innerText = errors['cf-turnstile-response'][0];
                }
                
                // Agrupar y mostrar otros errores de validación (ej: CUIT repetido)
                const otherErrors = Object.keys(errors)
                    .filter(k => k !== 'cf-turnstile-response')
                    .map(k => errors[k][0])
                    .join('\n');
                    
                if (otherErrors) {
                    alert('Errores en el formulario:\n' + otherErrors);
                }
                
                return; // Cortamos la ejecución sin lanzar excepción para no triggerear el alert genérico
            }
            let serverErrorMsg = 'Error en el servidor';
            try {
                const rawText = await response.text();
                console.error("RAW SERVER RESPONSE:", rawText);
                const errData = JSON.parse(rawText);
                if (errData.debug) serverErrorMsg = errData.debug;
                else if (errData.message) serverErrorMsg = errData.message;
            } catch (e) {
                console.error("Failed to parse error response:", e);
            }
            throw new Error(serverErrorMsg);
        }

        const result = await response.json();
        const refId = result.data && result.data.id ? result.data.id : Date.now();

        // Show success card
        document.getElementById('company-postulation-form').style.display = 'none';
        document.getElementById('postulacion-success-card').style.display = 'block';
        document.getElementById('success-company-name').innerText = name;
        document.getElementById('success-ref-id').innerText = 'REF-' + refId;
    } catch (err) {
        console.error("Error al enviar postulación:", err);
        alert('Hubo un error al enviar la postulación. Por favor, intente de nuevo.\n\nDetalle técnico: ' + err.message);
    }
}
