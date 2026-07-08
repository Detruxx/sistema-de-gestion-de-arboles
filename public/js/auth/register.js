/**
 * Vista de Autenticación: Lógica e interacciones para el registro de usuarios.
 */

// ================= LÓGICA DE REGISTRO =================

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
    const errorBox = document.getElementById('js-error-box');
    const errorList = document.getElementById('js-error-list');

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            // Limpiar errores previos
            errorBox.style.display = 'none';
            errorList.innerHTML = '';
            let errors = [];

            // Obtener inputs
            const password = document.getElementById('password').value;
            const passwordConf = document.getElementById('password_confirmation').value;

            // 1. Validar coincidencia de contraseña
            if (password !== passwordConf) {
                errors.push('Las contraseñas ingresadas no coinciden.');
            }

            // 2. Validar largo de contraseña
            if (password.length < 8) {
                errors.push('La contraseña debe tener al menos 8 caracteres.');
            }



            // Si hay errores, frenar envío y mostrarlos
            if (errors.length > 0) {
                e.preventDefault();
                errors.forEach(err => {
                    const li = document.createElement('li');
                    li.textContent = err;
                    errorList.appendChild(li);
                });
                errorBox.style.display = 'block';
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
});
