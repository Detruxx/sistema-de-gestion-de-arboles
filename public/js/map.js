document.addEventListener('DOMContentLoaded', () => {
    // ================= LÓGICA DEL MAPA (Solo si existe el div del mapa) =================
    const mapElement = document.getElementById('tree-map');
    if (mapElement) {
        // Inicializar el mapa centrado en Palermo, CABA
        const map = L.map('tree-map', {
            zoomControl: false, // Desactivamos el default para no chocar con el panel
            preferCanvas: true   // Renderizado de alto rendimiento para miles de marcadores
        }).setView([-34.5888, -58.4285], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Mover controles de zoom a la derecha (para que no los tape el panel)
        L.control.zoom({ position: 'topright' }).addTo(map);

        // Elementos del DOM del Panel Lateral
        const sidebar = document.getElementById('tree-sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');

        // Función para abrir/cerrar panel con el botón
        if(toggleBtn && sidebar) {
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
                document.getElementById('t-edad').textContent = arbol.degree ? `${arbol.degree} años` : 'Desconocida';
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
                // Calcular el radio inicial según el nivel de zoom actual
                const currentZoom = map.getZoom();
                let initialRadius = 1;
                if (currentZoom >= 16) initialRadius = 5;
                else if (currentZoom >= 14) initialRadius = 3;
                else if (currentZoom >= 12) initialRadius = 2;

                const marker = L.circleMarker([arbol.latitude, arbol.longitude], {
                    radius: initialRadius,
                    fillColor: '#2d7a4f',
                    color: '#ffffff',
                    weight: 0.5, // Bordes delgados para que no se empaste tanto
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map);


                marker.on('click', () => {
                    mostrarDatosArbol(arbol.id); // Pasamos solo el ID para que haga fetch del detalle
                    map.flyTo([arbol.latitude, arbol.longitude], 16, { duration: 0.5 });
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

        // Ajustar el tamaño de los puntos dinámicamente al hacer zoom
        map.on('zoomend', () => {
            const zoom = map.getZoom();
            let newRadius = 1;
            if (zoom >= 16) newRadius = 5;
            else if (zoom >= 14) newRadius = 3;
            else if (zoom >= 12) newRadius = 2;

            mapMarkers.forEach(marker => {
                marker.setRadius(newRadius);
            });
        });

        // Cargar marcadores iniciales desde la Base de Datos
        loadTreesFromDatabase();
    }

});
