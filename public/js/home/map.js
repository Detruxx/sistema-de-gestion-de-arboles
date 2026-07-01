document.addEventListener('DOMContentLoaded', () => {
    // ================= LÓGICA DEL MAPA (Solo si existe el div del mapa) =================
    const mapElement = document.getElementById('tree-map');
    if (mapElement) {
        // Crear un renderizador Canvas con alta "tolerancia" para clics (15px extra de área invisible)
        const canvasRenderer = L.canvas({ padding: 0.5, tolerance: 15 });

        // Inicializar el mapa centrado en Palermo, CABA
        const map = L.map('tree-map', {
            zoomControl: false, // Desactivamos el default para no chocar con el panel
            renderer: canvasRenderer, // Usamos el renderizador con tolerancia táctil en vez de preferCanvas: true
            zoomSnap: 0.1,        // Permite niveles de zoom intermedios (ej. 13.1, 13.2)
            zoomDelta: 0.25,      // Cada "clic" de la rueda del ratón avanza un cuarto de nivel en vez de un nivel entero
            wheelPxPerZoomLevel: 120 // Hace que el giro físico de la rueda se sienta más lento/pesado (por defecto es 60)
        }).setView([-34.5888, -58.4285], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Mover controles de zoom al extremo inferior derecho (evita solaparse con sidebar izquierda y filtros arriba a la derecha)
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Elementos del DOM
        const sidebar = document.getElementById('tree-sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');
        const panelDetails = document.getElementById('sidebar-panel-details');
        const btnTreeBack = document.getElementById('btn-tree-back');

        // Nuevos elementos de Filtros Flotantes
        const btnToggleFilters = document.getElementById('btn-toggle-filters');
        const filterDropdownMenu = document.getElementById('filter-dropdown-menu');

        // Función para abrir/cerrar panel de detalles
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar-closed');
            });
        }

        // Alternar el menú desplegable de filtros
        if (btnToggleFilters && filterDropdownMenu) {
            btnToggleFilters.addEventListener('click', (e) => {
                e.stopPropagation();
                btnToggleFilters.classList.toggle('active');
                filterDropdownMenu.classList.toggle('active');
            });

            // Cerrar el menú si se hace clic fuera del mismo
            document.addEventListener('click', (e) => {
                if (!filterDropdownMenu.contains(e.target) && e.target !== btnToggleFilters && !btnToggleFilters.contains(e.target)) {
                    btnToggleFilters.classList.remove('active');
                    filterDropdownMenu.classList.remove('active');
                }
            });
        }

        if (btnTreeBack) {
            btnTreeBack.addEventListener('click', () => {
                sidebar.classList.add('sidebar-closed');
            });
        }

        // Definición de icono personalizado de Leaflet (Verde)
        const greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Almacenar marcadores activos
        let mapMarkers = [];

        let arboles = [];

        // Exponer la función globalmente para poder llamarla desde el HTML del popup
        window.abrirDetalleArbol = function(arbolId, lat, lng) {
            mostrarDatosArbol(arbolId);
            if (lat && lng) {
                map.flyTo([lat, lng], 16, { duration: 0.5 });
            }
        };

        // Función para inyectar los datos en el panel al hacer clic
        async function mostrarDatosArbol(arbolId) {
            try {
                const response = await fetch(`/api/arboles/${arbolId}`);
                if (!response.ok) throw new Error('No se pudo obtener el detalle del árbol');

                const result = await response.json();
                const arbol = result.data; // El árbol completo con relaciones

                // 1. Armar la dirección (vereda o plaza)
                let direccionCompleta = 'Sin dirección';
                if (arbol.park) {
                    direccionCompleta = arbol.park.park_name;
                } else if (arbol.street) {
                    direccionCompleta = `${arbol.street.street_name} ${arbol.street.street_number}`;
                    if (arbol.reference) {
                        direccionCompleta += ` (${arbol.reference})`;
                    }
                }

                // 2. Inyectar datos en el DOM (se removió t-estado)
                document.getElementById('t-id').textContent = `#${arbol.id}`;
                document.getElementById('t-especie').textContent = arbol.specie ? arbol.specie.common_name : 'Desconocida';
                document.getElementById('t-edad').textContent = arbol.degree ? `${arbol.degree} años` : 'Desconocida'; // corregir
                document.getElementById('t-altura').textContent = `${arbol.height} m`;
                document.getElementById('t-direccion').textContent = direccionCompleta;

                // Aca puede ir la foto si tenemos una, sino una de prueba:
                document.getElementById('t-foto').src = 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?w=600';

                // Actualizar URL del botón de reclamo con datos del árbol
                const btnReclamar = document.getElementById('btn-reclamar-arbol');
                if (btnReclamar) {
                    const params = new URLSearchParams({
                        arbol_id: arbol.id,
                        especie: arbol.specie ? arbol.specie.common_name : 'Desconocida',
                        direccion: direccionCompleta
                    });
                    btnReclamar.href = `/tramites/reclamos?${params.toString()}`;
                }

                // Abrir el panel si está cerrado
                sidebar.classList.remove('sidebar-closed');

            } catch (error) {
                console.error("Error al cargar detalles del árbol:", error);
            }
        }


        // Función principal para filtrar y dibujar los marcadores
        function renderMapMarkers() {
            // Limpiar marcadores viejos del mapa
            mapMarkers.forEach(m => map.removeLayer(m));
            mapMarkers = [];

            // Obtener valores de los filtros
            const filterEspecie = document.getElementById('filter-especie').value;
            const filterAltura = document.getElementById('filter-altura').value;
            //const filterEdad = document.getElementById('filter-edad').value;
            //const filterEstado = document.getElementById('filter-estado').value;

            arboles.forEach(arbol => {
                // Filtro de especie (comparamos con el nombre de la especie relacionada)
                const nombreEspecie = arbol.specie ? arbol.specie.common_name : '';
                if (filterEspecie && nombreEspecie !== filterEspecie) return;

                // Filtro de altura
                if (filterAltura) {
                    const altura = parseFloat(arbol.height);
                    if (filterAltura === 'bajo' && altura >= 6) return;
                    if (filterAltura === 'medio' && (altura < 6 || altura > 12)) return;
                    if (filterAltura === 'alto' && altura <= 12) return;
                }
                /* Esto por ahora no lo usamos
                // Filtro de edad
                if (filterEdad) {
                    if (filterEdad === 'joven' && arbol.edad >= 10) return;
                    if (filterEdad === 'maduro' && (arbol.edad < 10 || arbol.edad > 30)) return;
                    if (filterEdad === 'centenario' && arbol.edad <= 30) return;
                }

                // Filtro de estado
                if (filterEstado && arbol.estado !== filterEstado) return;

                // Definir icono según estado
                let chosenIcon = greenIcon;
                if (arbol.estado === 'Regular') chosenIcon = orangeIcon;
                if (arbol.estado === 'Malo') chosenIcon = redIcon;
                */
                // Calcular el estilo inicial copiando la lógica de interpolación del mapa de referencia
                function getMarkerStyle(zoom, totalFeatures) {
                    let radius, weight;
                    // Radio (interpolación lineal)
                    if (totalFeatures < 20000) {
                        if (zoom <= 10) radius = 2;
                        else if (zoom <= 14) radius = 2 + (6 - 2) * ((zoom - 10) / (14 - 10));
                        else if (zoom <= 21) radius = 6 + (8 - 6) * ((zoom - 14) / (21 - 14));
                        else radius = 8;
                    } else {
                        if (zoom <= 12) radius = 0.8;
                        else if (zoom <= 14) radius = 0.8 + (5 - 0.8) * ((zoom - 12) / (14 - 12));
                        else if (zoom <= 21) radius = 5 + (8 - 5) * ((zoom - 14) / (21 - 14));
                        else radius = 8;
                    }

                    // Grosor del borde (stroke-width)
                    if (zoom <= 12) weight = 0;
                    else if (zoom <= 18) weight = 0 + (1 - 0) * ((zoom - 12) / (18 - 12));
                    else weight = 1;

                    return { radius, weight };
                }

                const currentZoom = map.getZoom();
                const style = getMarkerStyle(currentZoom, arboles.length);

                const marker = L.circleMarker([arbol.latitude, arbol.longitude], {
                    radius: style.radius,
                    fillColor: '#2d7a4f',
                    color: '#ffffff',
                    weight: style.weight,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map);

                let direccionBasica = 'Sin dirección';
                if (arbol.park) {
                    direccionBasica = arbol.park.park_name;
                } else if (arbol.street) {
                    direccionBasica = `${arbol.street.street_name} ${arbol.street.street_number || ''}`.trim();
                }
                const nombreEspeciePopup = arbol.specie ? arbol.specie.common_name : 'Desconocida';

                const template = document.getElementById('tree-popup-template');
                let popupNode = document.createElement('div');
                
                if (template) {
                    const clone = template.content.cloneNode(true);
                    clone.querySelector('.tree-popup-title').textContent = nombreEspeciePopup;
                    clone.querySelector('.address-text').textContent = direccionBasica;
                    const btn = clone.querySelector('.tree-popup-btn');
                    // Asignamos el evento de esta manera manteniendo separada la lógica
                    btn.onclick = () => window.abrirDetalleArbol(arbol.id, arbol.latitude, arbol.longitude);
                    popupNode.appendChild(clone);
                }

                marker.bindPopup(popupNode, {
                    closeButton: false,
                    className: 'custom-tree-popup',
                    offset: [0, -5]
                });

                // Ya no abrimos el sidebar inmediatamente ni forzamos flyTo aquí, 
                // Leaflet abre automáticamente el popup.
                marker.on('click', () => {
                    // map.flyTo([arbol.latitude, arbol.longitude], 16, { duration: 0.5 });
                });

                mapMarkers.push(marker);
            });
        }


        // Buscar por dirección o especie
        function searchAddress() {
            const query = document.getElementById('map-search-input').value.toLowerCase().trim();
            if (!query) return;

            const matched = arboles.find(a => {
                const nombreEspecie = a.specie ? a.specie.common_name.toLowerCase() : '';
                const nombreCalle = a.street ? a.street.street_name.toLowerCase() : '';
                const nombrePlaza = a.park ? a.park.park_name.toLowerCase() : '';
                return nombreCalle.includes(query) || nombrePlaza.includes(query) || nombreEspecie.includes(query);
            });

            if (matched) {
                mostrarDatosArbol(matched.id);
                map.flyTo([matched.latitude, matched.longitude], 16, { duration: 0.5 });
            } else {
                alert('No se encontró ningún árbol que coincida con la búsqueda.');
            }
        }

        // Cargar pines desde la base de datos
        async function loadTreesFromDatabase() {
            try {
                const response = await fetch('/api/arboles/pines');
                if (!response.ok) throw new Error('Error al cargar pines');

                const result = await response.json();
                arboles = result.data;
                renderMapMarkers();
            } catch (error) {
                console.error("Error al obtener los árboles de la base de datos:", error);
            }
        }

        // Vincular eventos a los filtros, si se tocan los filtros se vuelve a renderizar
        document.getElementById('filter-especie').addEventListener('change', renderMapMarkers);
        document.getElementById('filter-altura').addEventListener('change', renderMapMarkers);
        //document.getElementById('filter-edad').addEventListener('change', renderMapMarkers);
        //document.getElementById('filter-estado').addEventListener('change', renderMapMarkers);

        // Vincular eventos de búsqueda
        document.getElementById('map-search-btn').addEventListener('click', searchAddress);
        document.getElementById('map-search-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') searchAddress();
        });

        // Ajustar el tamaño dinámicamente al hacer zoom copiando la interpolación
        map.on('zoomend', () => {
            const zoom = map.getZoom();

            // Reutilizamos la lógica de interpolación (duplicada aquí por scope, o podríamos extraerla)
            function getMarkerStyle(zoom, totalFeatures) {
                let radius, weight;
                if (totalFeatures < 20000) {
                    if (zoom <= 10) radius = 2;
                    else if (zoom <= 14) radius = 2 + (6 - 2) * ((zoom - 10) / (14 - 10));
                    else if (zoom <= 21) radius = 6 + (8 - 6) * ((zoom - 14) / (21 - 14));
                    else radius = 8;
                } else {
                    if (zoom <= 12) radius = 0.8;
                    else if (zoom <= 14) radius = 0.8 + (5 - 0.8) * ((zoom - 12) / (14 - 12));
                    else if (zoom <= 21) radius = 5 + (8 - 5) * ((zoom - 14) / (21 - 14));
                    else radius = 8;
                }

                if (zoom <= 12) weight = 0;
                else if (zoom <= 18) weight = 0 + (1 - 0) * ((zoom - 12) / (18 - 12));
                else weight = 1;

                return { radius, weight };
            }

            const style = getMarkerStyle(zoom, arboles.length);

            mapMarkers.forEach(marker => {
                marker.setRadius(style.radius);
                marker.setStyle({ weight: style.weight });
            });
        });

        // Cargar marcadores iniciales desde la Base de Datos
        loadTreesFromDatabase();
    }

});
