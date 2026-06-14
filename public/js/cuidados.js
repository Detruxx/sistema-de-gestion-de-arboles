document.addEventListener('DOMContentLoaded', () => {
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

});
