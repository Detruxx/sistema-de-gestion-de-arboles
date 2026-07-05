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
            const dni = document.getElementById('dni').value.trim();
            const dob = new Date(document.getElementById('dob').value);
            const today = new Date();

            // 1. Validar coincidencia de contraseña
            if (password !== passwordConf) {
                errors.push('Las contraseñas ingresadas no coinciden.');
            }

            // 2. Validar largo de contraseña
            if (password.length < 8) {
                errors.push('La contraseña debe tener al menos 8 caracteres.');
            }

            // 3. Validar DNI (solo números, entre 7 y 9 dígitos)
            const dniRegex = /^\d{7,9}$/;
            if (!dniRegex.test(dni)) {
                errors.push('El DNI debe contener únicamente números y tener entre 7 y 9 dígitos.');
            }

            // 4. Validar fecha de nacimiento (debe ser menor a la fecha actual y mayor de 16 años por ejemplo)
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            if (isNaN(dob.getTime())) {
                errors.push('La fecha de nacimiento ingresada no es válida.');
            } else if (dob >= today) {
                errors.push('La fecha de nacimiento no puede ser en el futuro.');
            } else if (age < 16) {
                errors.push('Debes ser mayor de 16 años para registrarte en el sistema.');
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
