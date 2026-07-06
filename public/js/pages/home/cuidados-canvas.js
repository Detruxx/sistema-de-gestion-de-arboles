/**
 * Vista (Página Inicio): Lógica y animación con Canvas API para la sección de cuidados.
 */

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('cuidados-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        let time = 0;

        function animateTopographic() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Cantidad de líneas a dibujar
            const numLines = 25; 
            const spacing = canvas.height / (numLines - 5);
            
            ctx.lineWidth = 1.2;
            // Verde pálido un poco más intenso (Opacidad 0.35)
            ctx.strokeStyle = 'rgba(91, 191, 140, 0.35)'; 

            for (let i = 0; i < numLines; i++) {
                ctx.beginPath();
                
                let yBase = (i * spacing) - 100;
                
                // Dibujar una línea ondulada continua a lo largo del ancho de la pantalla
                for (let x = 0; x <= canvas.width + 50; x += 30) {
                    // Combinamos múltiples funciones seno/coseno para crear un efecto orgánico 
                    // que simule los anillos de la madera o las curvas de nivel topográficas
                    const wave1 = Math.sin(x * 0.003 + time * 0.4 + i * 0.3) * 50;
                    const wave2 = Math.sin(x * 0.008 + time * 0.2 + i * 0.1) * 25;
                    const wave3 = Math.cos(x * 0.001 - time * 0.1) * 80;
                    
                    const y = yBase + wave1 + wave2 + wave3;
                    
                    if (x === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        // Suavizar las curvas
                        ctx.lineTo(x, y);
                    }
                }
                ctx.stroke();
            }

            // Aumentar el tiempo muy lentamente para que la animación sea relajante
            time += 0.015;
            requestAnimationFrame(animateTopographic);
        }

        animateTopographic();
    }
});
