document.addEventListener('DOMContentLoaded', () => {
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
});
