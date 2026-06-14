document.addEventListener('DOMContentLoaded', () => {
    // ================= LÓGICA DEL MAPA =================
    const mapElement = document.getElementById('tree-map');
    if (mapElement) {
        // Inicializar el mapa
        const map = L.map('tree-map', {
            zoomControl: false // Desactivamos el default para no chocar con el panel
        }).setView([-34.6037, -58.3816], 13);

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
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar-closed');
            });
        }

        // Simulación de una base de datos de árboles (sin tocar, según requerimiento)
        const arboles = [
            {
                lat: -34.6037, lng: -58.3816,
                id: "#0012", estado: "Saludable", especie: "Jacarandá", edad: "15 años", reclamos: "0",
                foto: "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/Jacaranda_mimosifolia_en_Buenos_Aires.jpg/800px-Jacaranda_mimosifolia_en_Buenos_Aires.jpg"
            },
            {
                lat: -34.6100, lng: -58.3900,
                id: "#0084", estado: "Riesgo", especie: "Palo Borracho", edad: "25 años", reclamos: "3",
                foto: "https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/Ceiba_speciosa_%28Palo_borracho%29_en_Buenos_Aires.jpg/800px-Ceiba_speciosa_%28Palo_borracho%29_en_Buenos_Aires.jpg"
            }
        ];

        // Función para inyectar los datos en el panel al hacer clic
        function mostrarDatosArbol(arbol) {
            document.getElementById('t-id').textContent = arbol.id;
            document.getElementById('t-estado').textContent = arbol.estado;
            document.getElementById('t-especie').textContent = arbol.especie;
            document.getElementById('t-edad').textContent = arbol.edad;
            document.getElementById('t-reclamos').textContent = arbol.reclamos;
            document.getElementById('t-foto').src = arbol.foto;

            // Abrir el panel si está cerrado
            if(sidebar) {
                sidebar.classList.remove('sidebar-closed');
            }
        }

        // Cargar los marcadores en el mapa
        arboles.forEach(arbol => {
            const marker = L.marker([arbol.lat, arbol.lng]).addTo(map);

            // Al hacer clic en el marcador
            marker.on('click', () => {
                mostrarDatosArbol(arbol);
                // Centrar el mapa suavemente en el árbol clickeado
                map.flyTo([arbol.lat, arbol.lng], 16, { duration: 0.5 });
            });
        });
    }
});
