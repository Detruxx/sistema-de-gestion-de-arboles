/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

import { openSidebar } from './ui.js';
import { renderMapMarkers, getArboles, setArboles } from './markers.js';

export async function loadTreesFromDatabase() {
    try {
        const response = await fetch('/api/arboles/pines');
        if (!response.ok) throw new Error('Error al cargar pines');

        const result = await response.json();
        setArboles(result.data);
        renderMapMarkers();
    } catch (error) {
        console.error("Error al obtener los árboles de la base de datos:", error);
    }
}

export async function mostrarDatosArbol(arbolId) {
    try {
        const response = await fetch(`/api/arboles/${arbolId}`);
        if (!response.ok) throw new Error('No se pudo obtener el detalle del árbol');

        const result = await response.json();
        const arbol = result.data;

        // 1. Armar la dirección (vereda o plaza)
        let direccionCompleta = 'Sin dirección';
        let direccionLabel = 'Dirección:';
        
        if (arbol.park) {
            direccionCompleta = arbol.park.park_name;
            direccionLabel = 'Parque:';
        } else if (arbol.street) {
            direccionCompleta = `${arbol.street.street_name} ${arbol.street.street_number}`;
            if (arbol.reference) {
                direccionCompleta += ` (${arbol.reference})`;
            }
        }

        // 2. Inyectar datos en el DOM
        document.getElementById('t-id').textContent = `#${arbol.id}`;
        document.getElementById('t-especie').textContent = arbol.specie ? arbol.specie.common_name : 'Desconocida';
        document.getElementById('t-altura').textContent = `${arbol.height} m`;
        document.getElementById('t-dap').textContent = arbol.dap ? `${arbol.dap} cm` : '-';

        let vitalidadTexto = '-';
        if (arbol.vitality) {
            if (Array.isArray(arbol.vitality) && arbol.vitality.length > 0) {
                vitalidadTexto = arbol.vitality.join(', ');
            } else if (typeof arbol.vitality === 'object' && Object.keys(arbol.vitality).length > 0) {
                vitalidadTexto = Object.values(arbol.vitality).join(', ');
            } else if (typeof arbol.vitality === 'string' && arbol.vitality.trim() !== '' && arbol.vitality !== '[]' && arbol.vitality !== '{}') {
                try {
                    const parsed = JSON.parse(arbol.vitality);
                    vitalidadTexto = Object.values(parsed).join(', ') || '-';
                } catch (e) {
                    vitalidadTexto = arbol.vitality;
                }
            }
        }
        document.getElementById('t-vitalidad').textContent = vitalidadTexto;

        document.getElementById('t-mantenimiento').textContent = arbol.maintenance_status || '-';
        document.getElementById('t-estructura').textContent = arbol.structure || '-';
        document.getElementById('t-observaciones').textContent = arbol.observations || '-';
        
        const labelElem = document.getElementById('t-direccion-label');
        if (labelElem) labelElem.textContent = direccionLabel;
        
        document.getElementById('t-direccion').textContent = direccionCompleta;

        // Foto por defecto
        document.getElementById('t-foto').src = 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?w=600';

        // Botón de reclamo
        const btnReclamar = document.getElementById('btn-reclamar-arbol');
        if (btnReclamar) {
            const params = new URLSearchParams({
                arbol_id: arbol.id,
                especie: arbol.specie ? arbol.specie.common_name : 'Desconocida',
                direccion: direccionCompleta
            });
            btnReclamar.href = `/tramites/reclamos?${params.toString()}`;
        }

        // Abrir el panel
        openSidebar();

    } catch (error) {
        console.error("Error al cargar detalles del árbol:", error);
    }
}
