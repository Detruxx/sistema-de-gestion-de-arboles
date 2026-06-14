document.addEventListener('DOMContentLoaded', () => {
    // ================= LÓGICA DEL MAPA (Solo si existe el div del mapa) =================
    const mapElement = document.getElementById('tree-map');
    if (mapElement) {
        // Inicializar el mapa centrado en Palermo, CABA
        const map = L.map('tree-map', {
            zoomControl: false // Desactivamos el default para no chocar con el panel
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
        const panelFilters = document.getElementById('sidebar-panel-filters');
        const panelDetails = document.getElementById('sidebar-panel-details');
        const btnTreeBack = document.getElementById('btn-tree-back');

        // Función para abrir/cerrar panel con el botón
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-closed');
        });

        // Alternar entre panel de filtros y panel de detalles
        function switchPanel(panelName) {
            if (panelName === 'filters') {
                panelFilters.classList.add('active');
                panelDetails.classList.remove('active');
            } else if (panelName === 'details') {
                panelFilters.classList.remove('active');
                panelDetails.classList.add('active');
            }
        }

        if (btnTreeBack) {
            btnTreeBack.addEventListener('click', () => {
                switchPanel('filters');
            });
        }

        // Simulación de una base de datos de árboles ampliada
        const arboles = [
            { id: 1001, especie: 'Jacarandá', edad: 25, altura: 12.5, estado: 'Bueno', direccion: 'Plaza Armenia, Palermo, CABA', lat: -34.5888, lng: -58.4285, foto: 'https://images.unsplash.com/photo-1616781297592-fb2721868350?auto=format&fit=crop&w=600&q=80' },
            { id: 1002, especie: 'Ceibo', edad: 15, altura: 6.2, estado: 'Bueno', direccion: 'Av. Sarmiento 2400, Palermo, CABA', lat: -34.5795, lng: -58.4148, foto: 'https://images.unsplash.com/photo-1598902108854-10e335adac99?auto=format&fit=crop&w=600&q=80' },
            { id: 1003, especie: 'Fresno', edad: 40, altura: 16.0, estado: 'Regular', direccion: 'Defensa 850, San Telmo, CABA', lat: -34.6178, lng: -58.3712, foto: 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?auto=format&fit=crop&w=600&q=80' },
            { id: 1004, especie: 'Palo Borracho', edad: 30, altura: 14.2, estado: 'Malo', direccion: 'Plaza Francia, Recoleta, CABA', lat: -34.5835, lng: -58.3927, foto: 'https://images.unsplash.com/photo-1613967193442-19cfb77fdef5?auto=format&fit=crop&w=600&q=80' },
            { id: 1005, especie: 'Tilo', edad: 12, altura: 5.8, estado: 'Bueno', direccion: 'Juramento 1900, Belgrano, CABA', lat: -34.5615, lng: -58.4552, foto: 'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?auto=format&fit=crop&w=600&q=80' },
            { id: 1006, especie: 'Liquidámbar', edad: 18, altura: 11.0, estado: 'Bueno', direccion: 'Av. Del Libertador 3200, Palermo, CABA', lat: -34.5768, lng: -58.4063, foto: 'https://images.unsplash.com/photo-1507499739999-097706ad8914?auto=format&fit=crop&w=600&q=80' },
            { id: 1007, especie: 'Jacarandá', edad: 35, altura: 15.2, estado: 'Regular', direccion: 'Plaza Cortazar, Palermo, CABA', lat: -34.5915, lng: -58.4307, foto: 'https://images.unsplash.com/photo-1616781297592-fb2721868350?auto=format&fit=crop&w=600&q=80' },
            { id: 1008, especie: 'Fresno', edad: 8, altura: 4.5, estado: 'Bueno', direccion: 'Av. Cabildo 2100, Belgrano, CABA', lat: -34.5630, lng: -58.4568, foto: 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?auto=format&fit=crop&w=600&q=80' },
            { id: 1009, especie: 'Tilo', edad: 50, altura: 18.5, estado: 'Malo', direccion: 'Bolívar 600, San Telmo, CABA', lat: -34.6190, lng: -58.3735, foto: 'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?auto=format&fit=crop&w=600&q=80' },
            { id: 1010, especie: 'Ceibo', edad: 22, altura: 8.0, estado: 'Regular', direccion: 'Parque Rivadavia, Caballito, CABA', lat: -34.6185, lng: -58.4358, foto: 'https://images.unsplash.com/photo-1598902108854-10e335adac99?auto=format&fit=crop&w=600&q=80' },
            { id: 1011, especie: 'Liquidámbar', edad: 7, altura: 4.8, estado: 'Bueno', direccion: 'Juana Manso 1100, Puerto Madero, CABA', lat: -34.6120, lng: -58.3615, foto: 'https://images.unsplash.com/photo-1507499739999-097706ad8914?auto=format&fit=crop&w=600&q=80' },
            { id: 1012, especie: 'Palo Borracho', edad: 45, altura: 17.5, estado: 'Bueno', direccion: 'Av. 9 de Julio 1200, San Nicolás, CABA', lat: -34.6062, lng: -58.3816, foto: 'https://images.unsplash.com/photo-1613967193442-19cfb77fdef5?auto=format&fit=crop&w=600&q=80' }
        ];

        // Definición de iconos personalizados de Leaflet
        const greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const orangeIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const redIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Almacenar marcadores activos
        let mapMarkers = [];

        // Función para inyectar los datos en el panel al hacer clic
        function mostrarDatosArbol(arbol) {
            document.getElementById('t-id').textContent = `#${arbol.id}`;
            document.getElementById('t-estado').textContent = arbol.estado;
            document.getElementById('t-especie').textContent = arbol.especie;
            document.getElementById('t-edad').textContent = `${arbol.edad} años`;
            document.getElementById('t-altura').textContent = `${arbol.altura} m`;
            document.getElementById('t-direccion').textContent = arbol.direccion;
            document.getElementById('t-foto').src = arbol.foto;

            // Ajustar estilos del badge de estado
            const estadoBadge = document.getElementById('t-estado');
            estadoBadge.className = 'status-good';
            if (arbol.estado === 'Regular') {
                estadoBadge.className = 'status-regular';
            } else if (arbol.estado === 'Malo') {
                estadoBadge.className = 'status-bad';
            }

            // Cambiar a vista de detalles
            switchPanel('details');

            // Actualizar URL del botón de reclamo con datos del árbol
            const btnReclamar = document.getElementById('btn-reclamar-arbol');
            if (btnReclamar) {
                const params = new URLSearchParams({
                    arbol_id: arbol.id,
                    especie: arbol.especie,
                    direccion: arbol.direccion
                });
                btnReclamar.href = `/tramites/reclamos?${params.toString()}`;
            }

            // Abrir el panel si está cerrado
            sidebar.classList.remove('sidebar-closed');
        }

        // Función principal para filtrar y dibujar los marcadores
        function renderMapMarkers() {
            // Limpiar marcadores viejos del mapa
            mapMarkers.forEach(m => map.removeLayer(m));
            mapMarkers = [];

            // Obtener valores de los filtros
            const filterEspecie = document.getElementById('filter-especie').value;
            const filterAltura = document.getElementById('filter-altura').value;
            const filterEdad = document.getElementById('filter-edad').value;
            const filterEstado = document.getElementById('filter-estado').value;

            arboles.forEach(arbol => {
                // Filtro de especie
                if (filterEspecie && arbol.especie !== filterEspecie) return;

                // Filtro de altura
                if (filterAltura) {
                    if (filterAltura === 'bajo' && arbol.altura >= 6) return;
                    if (filterAltura === 'medio' && (arbol.altura < 6 || arbol.altura > 12)) return;
                    if (filterAltura === 'alto' && arbol.altura <= 12) return;
                }

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

                // Crear marcador en el mapa
                const marker = L.marker([arbol.lat, arbol.lng], { icon: chosenIcon }).addTo(map);

                marker.on('click', () => {
                    mostrarDatosArbol(arbol);
                    map.flyTo([arbol.lat, arbol.lng], 16, { duration: 0.5 });
                });

                mapMarkers.push(marker);
            });
        }

        // Buscar por dirección o especie
        function searchAddress() {
            const query = document.getElementById('map-search-input').value.toLowerCase().trim();
            if (!query) return;

            const matched = arboles.find(a => 
                a.direccion.toLowerCase().includes(query) || 
                a.especie.toLowerCase().includes(query)
            );

            if (matched) {
                mostrarDatosArbol(matched);
                map.flyTo([matched.lat, matched.lng], 16, { duration: 0.5 });
            } else {
                alert('No se encontró ningún árbol que coincida con la búsqueda.');
            }
        }

        // Vincular eventos a los filtros
        document.getElementById('filter-especie').addEventListener('change', renderMapMarkers);
        document.getElementById('filter-altura').addEventListener('change', renderMapMarkers);
        document.getElementById('filter-edad').addEventListener('change', renderMapMarkers);
        document.getElementById('filter-estado').addEventListener('change', renderMapMarkers);

        // Vincular eventos de búsqueda
        document.getElementById('map-search-btn').addEventListener('click', searchAddress);
        document.getElementById('map-search-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') searchAddress();
        });

        // Cargar marcadores iniciales
        renderMapMarkers();
    }

});
