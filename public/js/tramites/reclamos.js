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

    // --- LÓGICA DEL SELECTOR DE MAPA ESTILO UBER ---
    const btnSelectMap = document.getElementById('btn-select-map');
    const mapModal = document.getElementById('address-map-modal');
    const mapModalClose = document.getElementById('address-map-modal-close');
    const btnConfirmAddress = document.getElementById('btn-confirm-address');
    const previewText = document.getElementById('address-preview-text');
    const addressMapBody = document.querySelector('.address-map-body');
    
    let selectorMap = null;
    let currentCoordsAddress = '';
    let debounceTimer = null;

    function initSelectorMap() {
        if (selectorMap) return;

        // Centrar en Plaza Armenia, Palermo (-34.5888, -58.4285)
        selectorMap = L.map('address-map-canvas', {
            zoomControl: false
        }).setView([-34.5888, -58.4285], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(selectorMap);

        L.control.zoom({ position: 'topright' }).addTo(selectorMap);

        // Función de geocodificación reversa usando Nominatim
        function reverseGeocode(lat, lng) {
            previewText.textContent = 'Buscando dirección...';
            btnConfirmAddress.disabled = true;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        const road = data.address.road || data.address.pedestrian || data.address.path || '';
                        const number = data.address.house_number || '';
                        const suburb = data.address.suburb || data.address.neighbourhood || '';
                        
                        if (road) {
                            currentCoordsAddress = road + (number ? ' ' + number : '') + (suburb ? ', ' + suburb : '');
                        } else {
                            currentCoordsAddress = data.display_name.split(',').slice(0, 3).join(',').trim();
                        }
                    } else {
                        currentCoordsAddress = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    }
                    previewText.textContent = currentCoordsAddress;
                    btnConfirmAddress.disabled = false;
                })
                .catch(err => {
                    console.error('Nominatim error, usando fallback:', err);
                    // Fallback de simulación en Palermo según cercanía
                    const fallbacks = [
                        { lat: -34.5888, lng: -58.4285, address: 'Costa Rica 4600' },
                        { lat: -34.5795, lng: -58.4148, address: 'Av. Sarmiento 2400' },
                        { lat: -34.6178, lng: -58.3712, address: 'Defensa 850' },
                        { lat: -34.5835, lng: -58.3927, address: 'Plaza Francia 1100' },
                        { lat: -34.5615, lng: -58.4552, address: 'Juramento 1900' }
                    ];
                    
                    let closest = fallbacks[0];
                    let minDist = Infinity;
                    fallbacks.forEach(f => {
                        let dist = Math.pow(f.lat - lat, 2) + Math.pow(f.lng - lng, 2);
                        if (dist < minDist) {
                            minDist = dist;
                            closest = f;
                        }
                    });
                    
                    const simulatedNumber = Math.floor(100 + Math.random() * 800) * 10;
                    const streetName = closest.address.split(' ').slice(0, -1).join(' ') || closest.address.split(' ')[0];
                    currentCoordsAddress = streetName + ' ' + simulatedNumber + ', Palermo, CABA';
                    previewText.textContent = currentCoordsAddress;
                    btnConfirmAddress.disabled = false;
                });
        }

        // Cargar dirección inicial
        const initialCenter = selectorMap.getCenter();
        reverseGeocode(initialCenter.lat, initialCenter.lng);

        // Añadir efectos físicos de salto al pin
        selectorMap.on('movestart', () => {
            if(addressMapBody) addressMapBody.classList.add('map-moving');
        });

        selectorMap.on('moveend', () => {
            if(addressMapBody) addressMapBody.classList.remove('map-moving');
            
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const center = selectorMap.getCenter();
                reverseGeocode(center.lat, center.lng);
            }, 500);
        });
    }

    if (btnSelectMap) {
        btnSelectMap.addEventListener('click', () => {
            if(mapModal) mapModal.classList.add('active');
            setTimeout(() => {
                initSelectorMap();
                if (selectorMap) {
                    selectorMap.invalidateSize();
                }
            }, 100);
        });
    }

    if (mapModalClose) {
        mapModalClose.addEventListener('click', () => {
            if(mapModal) mapModal.classList.remove('active');
        });
    }

    if (btnConfirmAddress) {
        btnConfirmAddress.addEventListener('click', () => {
            if (currentCoordsAddress) {
                if(inputDireccion) {
                    inputDireccion.value = currentCoordsAddress;
                    inputDireccion.readOnly = false;
                    inputDireccion.classList.remove('readonly-input');
                }
                if(inputArbolId) inputArbolId.value = ''; 
                if(banner) banner.style.display = 'none';
                if(helpText) helpText.style.display = 'none';
            }
            if(mapModal) mapModal.classList.remove('active');
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

            const data = {
                categoria: document.getElementById('tipo-reclamo').value,
                arbol_id: inputArbolId ? inputArbolId.value.trim() : null,
                direccion: inputDireccion ? inputDireccion.value.trim() : '',
                descripcion: document.getElementById('descripcion') ? document.getElementById('descripcion').value.trim() : '',
                especie: urlParams.get('especie') || (inputArbolId && inputArbolId.value ? 'Especie ID ' + inputArbolId.value : 'No especificada'),
                vecino: 'Vecino de Comuna 13',
                email: 'vecino.comuna13@gmail.com'
            };

            try {
                const response = await fetch('/api/reclamos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    alert(`Reclamo registrado con éxito bajo el ID: ${result.data.id}`);
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
window.switchTab = function(tabName) {
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
window.trackComplaint = async function() {
    const inputEl = document.getElementById('track-id-input');
    if(!inputEl) return;
    
    const inputVal = inputEl.value.trim().toUpperCase();
    const errorDiv = document.getElementById('track-error');
    const resultDiv = document.getElementById('track-result');

    if (!inputVal) {
        alert('Por favor ingresa un código de reclamo.');
        return;
    }

    if(errorDiv) errorDiv.style.display = 'none';
    if(resultDiv) resultDiv.style.display = 'none';

    try {
        const response = await fetch(`/api/reclamos/${inputVal}`);
        if (response.ok) {
            const result = await response.json();
            const claim = result.data;

            // Poblar información
            const dirEl = document.getElementById('track-direccion');
            if(dirEl) dirEl.textContent = claim.direccion;
            
            const catEl = document.getElementById('track-categoria');
            if(catEl) catEl.textContent = `${claim.categoria} (${claim.fecha})`;
            
            // Texto de respuesta
            const replyTextEl = document.getElementById('track-admin-reply');
            const adminReplyBox = document.getElementById('admin-reply-box');
            if (claim.respuesta_admin) {
                if(replyTextEl) replyTextEl.textContent = claim.respuesta_admin;
                if(adminReplyBox) adminReplyBox.style.borderColor = 'var(--living-moss)';
            } else {
                if(replyTextEl) replyTextEl.textContent = "La solicitud fue recibida correctamente. Aún no se ha redactado una respuesta oficial por los inspectores.";
                if(adminReplyBox) adminReplyBox.style.borderColor = '#e5e7eb';
            }

            // Resaltar el paso del proceso
            const steps = ['recibido', 'inspeccion', 'poda', 'resuelto'];
            const currentIdx = steps.indexOf(claim.estado);

            steps.forEach((step, idx) => {
                const stepEl = document.getElementById(`step-${step}`);
                if(!stepEl) return;
                
                const numEl = stepEl.querySelector('.step-num');
                const lblEl = stepEl.querySelector('.step-lbl');

                if (idx <= currentIdx) {
                    // Paso completado o activo
                    if(numEl) {
                        numEl.style.background = 'var(--living-moss)';
                        numEl.style.borderColor = 'var(--living-moss)';
                        numEl.style.color = '#fff';
                    }
                    if(lblEl) {
                        lblEl.style.color = 'var(--deep-canopy)';
                        lblEl.style.fontWeight = '700';
                    }
                } else {
                    // Paso futuro
                    if(numEl) {
                        numEl.style.background = '#e5e7eb';
                        numEl.style.borderColor = '#e5e7eb';
                        numEl.style.color = '#9ca3af';
                    }
                    if(lblEl) {
                        lblEl.style.color = '#9ca3af';
                        lblEl.style.fontWeight = '500';
                    }
                }
            });

            // Actualizar línea de fondo según el progreso
            const line = document.getElementById('track-step-line');
            if(line) {
                let progressPercent = 0;
                if (currentIdx === 1) progressPercent = 33;
                else if (currentIdx === 2) progressPercent = 66;
                else if (currentIdx === 3) progressPercent = 100;
                
                line.style.background = `linear-gradient(to right, var(--living-moss) ${progressPercent}%, #e5e7eb ${progressPercent}%)`;
            }

            // Mostrar contenedor de resultados
            if(resultDiv) resultDiv.style.display = 'block';
        } else {
            if(errorDiv) errorDiv.style.display = 'block';
        }
    } catch (err) {
        console.error("Error tracking claim:", err);
        alert('Ocurrió un error al conectar con el servidor.');
    }
};
