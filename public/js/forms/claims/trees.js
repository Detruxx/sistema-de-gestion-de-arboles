/**
 * Componente (Formulario Reclamos): Lógica para la selección de especies y características de árboles.
 */

let arboles = [];

// Base de datos de árboles real para validación (se carga al inicio)
async function loadTrees() {
    try {
        const response = await fetch('/api/arboles/pines');
        if (!response.ok) throw new Error('Error al obtener árboles');
        const data = await response.json();

        if (data.status === 'success' && data.data) {
            arboles = data.data.map(t => {
                let direccion = '';
                if (t.street && t.street.street_name) {
                    direccion = `${t.street.street_name} ${t.street.street_number || ''}`.trim();
                    if (t.street.door_plate) direccion += ` (Frente ${t.street.door_plate})`;
                    direccion += ', CABA';
                } else if (t.park && t.park.park_name) {
                    direccion = t.park.park_name + ', CABA';
                }

                return {
                    id: t.id,
                    especie: t.specie ? t.specie.common_name : 'Desconocida',
                    direccion: direccion || 'Ubicación no especificada',
                    latitude: t.latitude,
                    longitude: t.longitude
                };
            });
        }
    } catch (e) {
        console.error('Error cargando los árboles reales:', e);
    }
}

// Cargar los árboles apenas se importa el módulo
loadTrees();

export function findTreeById(id) {
    return arboles.find(a => a.id == id) || null;
}

export function getArboles() {
    return arboles;
}

export function initTreeSelectionLogic(setSeleccionArbolCallback) {
    const inputArbolId = document.getElementById('arbol-id');
    const helpText = document.getElementById('arbol-id-help');

    // 1. Verificar si viene con ID preseleccionado del mapa (URL Param)
    const urlParams = new URLSearchParams(window.location.search);
    const arbolIdParam = urlParams.get('arbol_id');

    if (arbolIdParam && inputArbolId) {
        inputArbolId.value = arbolIdParam;
        inputArbolId.readOnly = true;
        inputArbolId.classList.add('readonly-input');

        // loadlTrees es asincrónico, reintentamos hasta que termine de cargar
        const checkAndSet = () => {
            const matched = findTreeById(arbolIdParam);
            if (matched) {
                setSeleccionArbolCallback(matched);
            } else if (arboles.length === 0) {
                setTimeout(checkAndSet, 100);
            }
        };
        checkAndSet();
    }

    // 2. Controlar ingreso manual de ID
    if (inputArbolId) {
        inputArbolId.addEventListener('input', () => {
            if (inputArbolId.readOnly) return;

            const typedVal = inputArbolId.value.trim();
            if (!typedVal) {
                setSeleccionArbolCallback(null);
                if (helpText) helpText.style.display = 'none';
                return;
            }

            const matched = findTreeById(typedVal);
            if (matched) {
                setSeleccionArbolCallback(matched);
                if (helpText) helpText.style.display = 'none';
            } else {
                setSeleccionArbolCallback(null);
                if (helpText) {
                    helpText.style.display = 'block';
                    helpText.textContent = 'El ID ingresado no corresponde a ningún árbol del censo. El reclamo se registrará por ubicación manual.';
                }
            }
        });
    }
}
