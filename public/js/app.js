
document.addEventListener('DOMContentLoaded', () => {

    // 1. Efecto Scroll en la barra de navegación
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // 2. Animación de Partículas (Estilo orgánico/hojas flotantes) en el Canvas
    const canvas = document.getElementById('hero-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');

        let particlesArray;

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
                // Color verde translúcido
                this.color = `rgba(91, 191, 140, ${Math.random() * 0.5 + 0.1})`;
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

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
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
            for (let i = 0; i < particlesArray.length; i++) {
                particlesArray[i].update();
                particlesArray[i].draw();
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
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-closed');
        });

        // Simulación de una base de datos de árboles
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
            sidebar.classList.remove('sidebar-closed');
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

    // 3. ScrollSpy y gestión de clase activa en la barra de navegación
    const navLinks = document.querySelectorAll('.navbar .nav-pill');

    // Si estamos en la página del mapa, no queremos scrollspy para el inicio
    if (!window.location.pathname.includes('/mapa')) {
        // Lógica de click manual para destacar el ancla activa
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                const href = link.getAttribute('href');
                if (href && href.startsWith('#')) {
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                }
            });
        });

        // ScrollSpy automático con IntersectionObserver para las secciones que existan
        const sections = [];
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const sec = document.querySelector(href);
                if (sec) {
                    sections.push(sec);
                }
            }
        });

        if (sections.length > 0) {
            const observerOptions = {
                root: null,
                rootMargin: '-20% 0px -60% 0px',
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        navLinks.forEach(link => {
                            if (link.getAttribute('href') === `#${id}`) {
                                link.classList.add('active');
                            } else {
                                link.classList.remove('active');
                            }
                        });
                    }
                });
            }, observerOptions);

            sections.forEach(section => observer.observe(section));
        }
    }

});
