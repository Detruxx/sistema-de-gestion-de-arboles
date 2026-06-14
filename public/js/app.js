
document.addEventListener('DOMContentLoaded', () => {

    // 1. Efecto Scroll en la barra de navegación
    const navbar = document.getElementById('navbar');
    const isHomePage = window.location.pathname === '/' || window.location.pathname === '/index.php' || window.location.pathname === '';
    
    function updateNavbar() {
        if (!isHomePage || window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
    
    window.addEventListener('scroll', updateNavbar);
    updateNavbar();

    // 2. Animación de Partículas (Estilo orgánico/hojas flotantes) en el Canvas
    const canvas = document.getElementById('hero-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');

        let particlesArray;
        const aboutSection = document.querySelector('.about-section');
        const contactSection = document.querySelector('.contact-section');

        // Ajustar el tamaño del canvas a la ventana
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Clase constructora de partículas
        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 3 + 1; // Tamaño de la "hoja/polen"
                this.speedX = Math.random() * 1 - 0.5; // Movimiento horizontal
                this.speedY = Math.random() * -1 - 0.5; // Movimiento vertical (hacia arriba)
                this.opacity = Math.random() * 0.4 + 0.1;
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                // Si la partícula sale por arriba, reaparece abajo
                if (this.y < 0) {
                    this.y = canvas.height;
                    this.x = Math.random() * canvas.width;
                }
            }

            draw(aboutRect, contactRect) {
                let activeColor = `rgba(91, 191, 140, ${this.opacity})`; // Color verde brillante original

                if (aboutRect && contactRect) {
                    // Verificar si la coordenada Y de la partícula está sobre la sección blanca en el viewport
                    const overAbout = this.y >= aboutRect.top && this.y <= aboutRect.bottom;
                    const overContact = this.y >= contactRect.top && this.y <= contactRect.bottom;

                    if (overAbout || overContact) {
                        // Cambiar al color oscuro (--forest-night = #0A1A0D) en la sección clara con opacidad ligeramente mayor
                        activeColor = `rgba(10, 26, 13, ${this.opacity * 1.5})`;
                    }
                }

                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = activeColor;
                ctx.fill();
            }
        }

        // Inicializar el arreglo de partículas
        function initParticles() {
            particlesArray = [];
            let numberOfParticles = (canvas.width * canvas.height) / 9000;
            for (let i = 0; i < numberOfParticles; i++) {
                particlesArray.push(new Particle());
            }
        }

        // Ciclo de animación
        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Obtener bounding boxes una vez por frame
            const aboutRect = aboutSection ? aboutSection.getBoundingClientRect() : null;
            const contactRect = contactSection ? contactSection.getBoundingClientRect() : null;

            for (let i = 0; i < particlesArray.length; i++) {
                particlesArray[i].update();
                particlesArray[i].draw(aboutRect, contactRect);
            }
            requestAnimationFrame(animateParticles);
        }

        initParticles();
        animateParticles();
    }

    //======================= PAGINA MAPA

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

    // 3. ScrollSpy y gestión de clase activa en la barra de navegación
    const navLinks = document.querySelectorAll('.navbar .nav-pill');

    // Si estamos en la página del mapa, no queremos scrollspy para el inicio
    if (!window.location.pathname.includes('/mapa')) {
        // Lógica de click manual para destacar el ancla activa
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                let href = link.getAttribute('href');
                if (href && href.startsWith('/')) {
                    href = href.substring(1);
                }
                if (href && href.startsWith('#')) {
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                }
            });
        });

        // ScrollSpy automático con IntersectionObserver para todas las secciones, cabecera y footer
        const allSections = document.querySelectorAll('main > section, header, footer');
        if (allSections.length > 0) {
            const observerOptions = {
                root: null,
                rootMargin: '-30% 0px -40% 0px', // Detecta la sección predominante en la pantalla
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        let matched = false;
                        navLinks.forEach(link => {
                            let href = link.getAttribute('href');
                            if (href && href.startsWith('/')) {
                                href = href.substring(1);
                            }
                            if (id && href === `#${id}`) {
                                link.classList.add('active');
                                matched = true;
                            } else {
                                link.classList.remove('active');
                            }
                        });
                        
                        // Si la sección visible no tiene enlace (ej. hero, sobre-nosotros o footer)
                        // limpiamos el estado activo de todos los links
                        if (!matched) {
                            navLinks.forEach(link => link.classList.remove('active'));
                        }
                    }
                });
            }, observerOptions);

            allSections.forEach(section => observer.observe(section));
        }
    }

    // 4. Animación de revelado al hacer scroll (Reveal on Scroll)
    const revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    // Una vez revelado, dejamos de observarlo para optimizar rendimiento
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.15
        });
        revealElements.forEach(el => revealObserver.observe(el));
    }

    // ================= ANIMACIÓN DE HOJAS CAYENDO (Solo en la página de Cuidados) =================
    const cuidadosCanvas = document.getElementById('cuidados-canvas');
    if (cuidadosCanvas) {
        const ctx = cuidadosCanvas.getContext('2d');
        let leavesArray = [];

        function resizeCuidadosCanvas() {
            cuidadosCanvas.width = window.innerWidth;
            cuidadosCanvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCuidadosCanvas);
        resizeCuidadosCanvas();

        class Leaf {
            constructor() {
                this.x = Math.random() * cuidadosCanvas.width;
                this.y = Math.random() * -cuidadosCanvas.height; // Comienzan arriba, fuera de la pantalla
                this.size = Math.random() * 8 + 6; // Tamaño de la hoja
                this.speedY = Math.random() * 1 + 0.8; // Velocidad de caída constante
                this.windSpeed = Math.random() * 0.02 + 0.01; // Velocidad del balanceo
                this.windAngle = Math.random() * Math.PI * 2;
                this.swayDistance = Math.random() * 2 + 1; // Amplitud del vaivén
                this.rotation = Math.random() * 360;
                this.rotationSpeed = Math.random() * 1 - 0.5;
                
                // Variedad de verdes orgánicos para las hojas (más opacas y visibles)
                const greenShades = [
                    'rgba(45, 122, 79, 0.7)',    // living-moss
                    'rgba(26, 61, 40, 0.65)',   // deep-canopy
                    'rgba(91, 191, 140, 0.75)',  // spring-leaf
                    'rgba(10, 26, 13, 0.55)'     // forest-night
                ];
                this.color = greenShades[Math.floor(Math.random() * greenShades.length)];
            }

            update() {
                this.y += this.speedY;
                // Movimiento senoidal horizontal (vaivén)
                this.windAngle += this.windSpeed;
                this.x += Math.sin(this.windAngle) * this.swayDistance;
                this.rotation += this.rotationSpeed;

                // Reaparecer arriba si sale de la pantalla
                if (this.y > cuidadosCanvas.height + 20) {
                    this.y = -20;
                    this.x = Math.random() * cuidadosCanvas.width;
                }
            }

            draw() {
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.rotation * Math.PI / 180);
                
                // Dibujado de una forma de hoja
                ctx.beginPath();
                ctx.moveTo(0, -this.size);
                ctx.quadraticCurveTo(this.size * 0.7, -this.size * 0.3, 0, this.size);
                ctx.quadraticCurveTo(-this.size * 0.7, -this.size * 0.3, 0, -this.size);
                ctx.fillStyle = this.color;
                ctx.fill();
                
                // Nervadura central de la hoja
                ctx.beginPath();
                ctx.moveTo(0, -this.size);
                ctx.lineTo(0, this.size);
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
                ctx.lineWidth = 1.2;
                ctx.stroke();
                
                ctx.restore();
            }
        }

        function initLeaves() {
            leavesArray = [];
            // Densidad de hojas basada en el ancho de la pantalla
            const numberOfLeaves = Math.floor(cuidadosCanvas.width / 25);
            for (let i = 0; i < numberOfLeaves; i++) {
                leavesArray.push(new Leaf());
            }
        }

        function animateLeaves() {
            ctx.clearRect(0, 0, cuidadosCanvas.width, cuidadosCanvas.height);
            for (let i = 0; i < leavesArray.length; i++) {
                leavesArray[i].update();
                leavesArray[i].draw();
            }
            requestAnimationFrame(animateLeaves);
        }

        initLeaves();
        animateLeaves();
    }

    // ================= LÓGICA DEL MODAL DE DETALLES DE CUIDADO =================
    const careModal = document.getElementById('care-modal');
    if (careModal) {
        const modalBadge = document.getElementById('modal-badge');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        const modalTipsList = document.getElementById('modal-tips-list');
        const modalImage = document.getElementById('modal-image');
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const prevBtn = document.getElementById('modal-prev-btn');
        const nextBtn = document.getElementById('modal-next-btn');

        const tipCards = document.querySelectorAll('.tip-card');
        let currentCardIndex = -1;

        // Función para actualizar y mostrar los datos del modal
        function showCard(index) {
            if (index < 0) {
                index = tipCards.length - 1;
            } else if (index >= tipCards.length) {
                index = 0;
            }
            currentCardIndex = index;

            const card = tipCards[index];
            const badgeText = card.getAttribute('data-badge');
            const titleText = card.getAttribute('data-title');
            const descText = card.getAttribute('data-description');
            const imageSrc = card.getAttribute('data-image');
            const tipsString = card.getAttribute('data-tips');

            // Inyectar datos básicos
            modalBadge.textContent = badgeText;
            modalTitle.textContent = titleText;
            modalBody.textContent = descText;
            modalImage.src = imageSrc;
            modalImage.alt = titleText;

            // Ajustar estilo del badge según categoría
            if (badgeText === 'Normativa Legal' || badgeText === 'Requiere Permiso') {
                modalBadge.classList.add('warning');
            } else {
                modalBadge.classList.remove('warning');
            }

            // Inyectar lista de tips usando innerHTML para soportar enlaces
            modalTipsList.innerHTML = '';
            if (tipsString) {
                const tipsArray = tipsString.split(';');
                tipsArray.forEach(tip => {
                    if (tip.trim()) {
                        const li = document.createElement('li');
                        li.innerHTML = tip.trim();
                        modalTipsList.appendChild(li);
                    }
                });
            }
        }

        // Función para abrir el modal
        function openModal(index) {
            showCard(index);
            careModal.classList.add('active');
        }

        // Función para cerrar el modal
        function closeModal() {
            careModal.classList.remove('active');
        }

        // Asignar eventos a las tarjetas
        tipCards.forEach((card, index) => {
            card.addEventListener('click', () => {
                openModal(index);
            });
        });

        // Eventos de botones de navegación
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                showCard(currentCardIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                showCard(currentCardIndex + 1);
            });
        }

        modalCloseBtn.addEventListener('click', closeModal);

        // Cerrar al clickear fuera del contenedor del modal
        careModal.addEventListener('click', (e) => {
            if (e.target === careModal) {
                closeModal();
            }
        });

        // Eventos de teclado (Escape para cerrar, flechas para navegar)
        document.addEventListener('keydown', (e) => {
            if (!careModal.classList.contains('active')) return;

            if (e.key === 'Escape') {
                closeModal();
            } else if (e.key === 'ArrowLeft') {
                showCard(currentCardIndex - 1);
            } else if (e.key === 'ArrowRight') {
                showCard(currentCardIndex + 1);
            }
        });
    }

    // ================= LÓGICA DEL MENU DESPLEGABLE (DROPDOWN) =================
    const navDropdowns = document.querySelectorAll('.nav-dropdown');

    navDropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        if (trigger) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                
                // Cerrar los otros menús desplegables
                navDropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('active');
                        const otherTrigger = otherDropdown.querySelector('.dropdown-trigger');
                        if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                    }
                });

                const isActive = dropdown.classList.contains('active');
                if (isActive) {
                    dropdown.classList.remove('active');
                    trigger.setAttribute('aria-expanded', 'false');
                } else {
                    dropdown.classList.add('active');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });
    document.addEventListener('click', (e) => {
        navDropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
                const trigger = dropdown.querySelector('.dropdown-trigger');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // ================= LÓGICA DEL MENU HAMBURGUESA =================
    const navToggle = document.getElementById('nav-toggle');
    const navLinksContainer = document.getElementById('nav-links');

    if (navToggle && navLinksContainer) {
        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isActive = navToggle.classList.contains('active');
            if (isActive) {
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                navLinksContainer.classList.remove('active');
            } else {
                navToggle.classList.add('active');
                navToggle.setAttribute('aria-expanded', 'true');
                navLinksContainer.classList.add('active');
            }
        });

        // Cerrar menú al hacer clic en cualquier enlace (excluyendo disparadores de dropdowns)
        const links = navLinksContainer.querySelectorAll('.nav-pill:not(.dropdown-trigger), .dropdown-menu a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                navLinksContainer.classList.remove('active');
            });
        });

        // Cerrar al hacer clic fuera del menú
        document.addEventListener('click', (e) => {
            if (!navLinksContainer.contains(e.target) && !navToggle.contains(e.target)) {
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                navLinksContainer.classList.remove('active');
            }
        });
    }

});
