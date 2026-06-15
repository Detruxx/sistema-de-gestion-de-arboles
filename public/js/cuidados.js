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



    });
