// Lógica para Trámite de Reclamos
document.addEventListener('DOMContentLoaded', () => {
    // Base de datos de árboles simulada para validación
    const arboles = [
        { id: 1001, especie: 'Jacarandá', direccion: 'Plaza Armenia, Palermo, CABA' },
        { id: 1002, especie: 'Ceibo', direccion: 'Av. Sarmiento 2400, Palermo, CABA' },
        { id: 1003, especie: 'Fresno', direccion: 'Defensa 850, San Telmo, CABA' },
        { id: 1004, especie: 'Palo Borracho', direccion: 'Plaza Francia, Recoleta, CABA' },
        { id: 1005, especie: 'Tilo', direccion: 'Juramento 1900, Belgrano, CABA' },
        { id: 1006, especie: 'Liquidámbar', direccion: 'Av. Del Libertador 3200, Palermo, CABA' },
        { id: 1007, especie: 'Jacarandá', direccion: 'Plaza Cortazar, Palermo, CABA' },
        { id: 1008, especie: 'Fresno', direccion: 'Av. Cabildo 2100, Belgrano, CABA' },
        { id: 1009, especie: 'Tilo', direccion: 'Bolívar 600, San Telmo, CABA' },
        { id: 1010, especie: 'Ceibo', direccion: 'Parque Rivadavia, Caballito, CABA' },
        { id: 1011, especie: 'Liquidámbar', direccion: 'Juana Manso 1100, Puerto Madero, CABA' },
        { id: 1012, especie: 'Palo Borracho', direccion: 'Av. 9 de Julio 1200, San Nicolás, CABA' }
    ];

    //////////  Trae los tipos de reclamo desde la API
    const selectTipoReclamo = document.getElementById('tipo-reclamo');

    fetch('/api/request-types')
        .then(response => response.json())
        .then(types => {
            // Limpiamos opciones y dejamos solo el placeholder
            selectTipoReclamo.innerHTML = '<option value="">Seleccione una opcion...</option>';

            // Agregamos una opcion por cada tipo cargado en la BDD
            types.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = type.task_description;

                // Aca se muestran las opciones agregandolas al SELECT HTML
                selectTipoReclamo.appendChild(option);

            });

        })
        .catch(err => console.error('Error al cargar tipos de reclamo:', err));
    /////////////////////////////////////

    const inputArbolId = document.getElementById('arbol-id');
    const inputDireccion = document.getElementById('direccion');
    const banner = document.getElementById('selected-tree-banner');
    const bannerText = document.getElementById('selected-tree-text');
    const helpText = document.getElementById('arbol-id-help');

    function setSeleccionArbol(arbol) {
        if (arbol) {
            inputDireccion.value = arbol.direccion;
            inputDireccion.readOnly = true;
            inputDireccion.classList.add('readonly-input');

            banner.style.display = 'flex';
            bannerText.innerHTML = `Estás registrando un reclamo para el árbol <strong>ID #${arbol.id} (${arbol.especie})</strong> ubicado en <strong>${arbol.direccion}</strong>.`;
            helpText.style.display = 'none';
        } else {
            inputDireccion.readOnly = false;
            inputDireccion.classList.remove('readonly-input');

            banner.style.display = 'none';
            bannerText.textContent = '';
        }
    }

    // 1. Verificar si viene con ID preseleccionado del mapa
    const urlParams = new URLSearchParams(window.location.search);
    const arbolIdParam = urlParams.get('arbol_id');

    if (arbolIdParam && inputArbolId) {
        inputArbolId.value = arbolIdParam;
        inputArbolId.readOnly = true;
        inputArbolId.classList.add('readonly-input');

        const matched = arboles.find(a => a.id == arbolIdParam);
        if (matched) {
            setSeleccionArbol(matched);
        }
    }

    // 2. Controlar ingreso manual de ID
    if (inputArbolId) {
        inputArbolId.addEventListener('input', () => {
            if (inputArbolId.readOnly) return;

            const typedVal = inputArbolId.value.trim();
            if (!typedVal) {
                setSeleccionArbol(null);
                helpText.style.display = 'none';
                return;
            }

            const matched = arboles.find(a => a.id == typedVal);
            if (matched) {
                setSeleccionArbol(matched);
            } else {
                setSeleccionArbol(null);
                helpText.style.display = 'block';
                helpText.textContent = 'El ID ingresado no corresponde a ningún árbol del censo. El reclamo se registrará por ubicación manual.';
            }
        });
    }

    // Lógica de envío de formulario a la API
    const reclamoForm = document.getElementById('reclamo-form');
    if (reclamoForm) {
        reclamoForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Requerimos el token CSRF desde el meta tag si existe, de lo contrario esto puede fallar si se depende de blade aquí
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '';

            // Usamos FormData en vez de Json para soportar subida de fotos
            const formData = new FormData();
            formData.append('request_type_id', document.getElementById('tipo-reclamo').value);

            if (inputArbolId && inputArbolId.value.trim() !== '') {
                formData.append('tree_id', inputArbolId.value.trim());
            }

            formData.append('address', inputDireccion ? inputDireccion.value.trim() : '');
            formData.append('description', document.getElementById('descripcion') ? document.getElementById('descripcion').value.trim() : '');

            const fileInput = document.getElementById('foto');
            if (fileInput && fileInput.files.length > 0) {
                formData.append('foto', fileInput.files[0]);
            }

            try {
                const response = await fetch('/requests', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                        // Al usar FormData NO se debe poner 'Content-Type': 'application/json',
                        // el navegador seteará automáticamente 'multipart/form-data' y el boundary.
                    },
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json();
                    alert(`Reclamo registrado con éxito bajo el ID: ${result.data.tracking_code}`);
                    reclamoForm.reset();
                    setSeleccionArbol(null);
                } else {
                    alert('Error al registrar el reclamo en el servidor.');
                }
            } catch (err) {
                console.error('Submit error:', err);
                alert('Error de red al intentar registrar el reclamo.');
            }
        });
    }
});

// Lógica de cambio de pestañas
window.switchTab = function (tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });
    // Mostrar el contenido seleccionado
    const selectedContent = document.getElementById(`section-${tabName}`);
    if (selectedContent) selectedContent.style.display = 'block';

    // Alternar clases activas en los botones de pestañas
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    const activeBtn = document.getElementById(`tab-btn-${tabName}`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
};

// Consultar estado de un reclamo individual
window.trackComplaint = async function () {
    const inputEl = document.getElementById('track-id-input');
    if (!inputEl) return;

    const inputVal = inputEl.value.trim().toUpperCase();
    const errorDiv = document.getElementById('track-error');
    const resultDiv = document.getElementById('track-result');

    if (!inputVal) {
        alert('Por favor ingresa un código de reclamo.');
        return;
    }

    if (errorDiv) errorDiv.style.display = 'none';
    if (resultDiv) resultDiv.style.display = 'none';

    try {
        const response = await fetch(`/api/reclamos/${inputVal}`);
        if (response.ok) {
            const result = await response.json();
            const claim = result.data;

            // Poblar información
            const dirEl = document.getElementById('track-direccion');
            if (dirEl) dirEl.textContent = claim.direccion;

            const catEl = document.getElementById('track-categoria');
            if (catEl) catEl.textContent = `${claim.categoria} (${claim.fecha})`;

            // Texto de respuesta
            const replyTextEl = document.getElementById('track-admin-reply');
            const adminReplyBox = document.getElementById('admin-reply-box');
            if (claim.respuesta_admin) {
                if (replyTextEl) replyTextEl.textContent = claim.respuesta_admin;
                if (adminReplyBox) adminReplyBox.style.borderColor = 'var(--living-moss)';
            } else {
                if (replyTextEl) replyTextEl.textContent = "La solicitud fue recibida correctamente. Aún no se ha redactado una respuesta oficial por los inspectores.";
                if (adminReplyBox) adminReplyBox.style.borderColor = '#e5e7eb';
            }

            // Buscar estados de la BD dinámicamente
            const statusRes = await fetch('/api/request-statuses');
            if (!statusRes.ok) throw new Error("Error cargando estados");
            const statusResult = await statusRes.json();
            const requestStatuses = statusResult.data;

            // Renderizar la barra de progreso
            const container = document.getElementById('dynamic-stepper-container');
            if (container) {
                // Filtramos solo los estados lineales (los que tienen secuencia definida)
                const linearSteps = requestStatuses.filter(s => s.sequence !== null).sort((a,b) => a.sequence - b.sequence);
                
                // Determinamos el estado actual
                const currentState = requestStatuses.find(s => s.slug === claim.estado);
                const isTerminalException = currentState && currentState.is_terminal && currentState.sequence === null;
                
                // Obtenemos la secuencia en la que nos encontramos
                let currentSeq = 0;
                if (!isTerminalException && currentState) {
                    currentSeq = currentState.sequence;
                }

                let html = '<div class="track-step-line" id="track-step-line"></div>';
                
                linearSteps.forEach((step) => {
                    const isCompleted = step.sequence <= currentSeq;
                    
                    let bgNum = isCompleted ? 'var(--living-moss)' : '#e5e7eb';
                    let colorNum = isCompleted ? '#fff' : '#9ca3af';
                    let colorLbl = isCompleted ? 'var(--deep-canopy)' : '#9ca3af';
                    let fontLbl = isCompleted ? '700' : '500';
                    
                    let labelText = step.status_name;

                    // Manejo visual de Excepciones (Denegado/Vinculado)
                    if (isTerminalException) {
                        // Resaltamos de color distinto el primer paso y le cambiamos el nombre
                        if (step.sequence === 1) {
                            bgNum = currentState.slug === 'denied' ? '#ef4444' : '#6b7280'; // rojo o gris
                            colorNum = '#fff';
                            colorLbl = bgNum;
                            labelText = currentState.status_name;
                        } else {
                            bgNum = '#e5e7eb'; colorNum = '#9ca3af'; colorLbl = '#9ca3af'; fontLbl = '500';
                        }
                    }

                    html += `
                        <div class="track-step-item" id="step-${step.slug}">
                            <div class="step-num" style="background:${bgNum}; border-color:${bgNum}; color:${colorNum}">${step.sequence}</div>
                            <span class="step-lbl" style="color:${colorLbl}; font-weight:${fontLbl}">${labelText}</span>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
                
                // Actualizar porcentaje de la línea
                const line = document.getElementById('track-step-line');
                if (line) {
                    let progressPercent = 0;
                    if (!isTerminalException && currentSeq > 1) {
                        progressPercent = ((currentSeq - 1) / (linearSteps.length - 1)) * 100;
                    }
                    let lineBg = 'var(--living-moss)';
                    if (isTerminalException) {
                        lineBg = currentState.slug === 'denied' ? '#ef4444' : '#6b7280';
                        progressPercent = 0; // Línea frena al inicio
                    }
                    line.style.background = `linear-gradient(to right, ${lineBg} ${progressPercent}%, #e5e7eb ${progressPercent}%)`;
                }
            }

            // Mostrar contenedor de resultados
            if (resultDiv) resultDiv.style.display = 'block';
        } else {
            if (errorDiv) errorDiv.style.display = 'block';
        }
    } catch (err) {
        console.error("Error tracking claim:", err);
        alert('Ocurrió un error al conectar con el servidor.');
    }
};
