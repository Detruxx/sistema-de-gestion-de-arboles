/**
 * Vista de Autenticación: Lógica e interacciones para el inicio de sesión.
 */

// ================= LÓGICA DE LOGIN =================
function switchRole(role) {
    const tabVecino = document.getElementById('tab-vecino');
    const tabAdmin = document.getElementById('tab-admin');
    const subtitle = document.getElementById('role-subtitle');
    const emailInput = document.getElementById('email');
    const submitBtn = document.getElementById('submit-btn');

    if (role === 'vecino') {
        tabVecino.classList.add('active');
        tabAdmin.classList.remove('active');
        subtitle.innerText = 'Accede para ver el mapa, reportar incidentes y más';
        emailInput.placeholder = 'vecino@correo.com';
        submitBtn.innerText = 'Ingresar como Vecino';
    } else {
        tabAdmin.classList.add('active');
        tabVecino.classList.remove('active');
        subtitle.innerText = 'Acceso exclusivo para inspectores y administradores de la Comuna';
        emailInput.placeholder = 'admin@treeba.gob.ar';
        submitBtn.innerText = 'Ingresar como Administrador';
    }
}

// Inicializar estado por defecto
document.addEventListener('DOMContentLoaded', () => {
    switchRole('vecino');
});
