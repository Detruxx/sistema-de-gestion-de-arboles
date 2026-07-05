/**
 * Componente (Formulario Reclamos): Lógica para la selección de especies y características de árboles.
 */

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

export function findTreeById(id) {
    return arboles.find(a => a.id == id) || null;
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

        const matched = findTreeById(arbolIdParam);
        if (matched) {
            setSeleccionArbolCallback(matched);
        }
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
