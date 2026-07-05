/**
 * Contiene lógica de JavaScript para interactividad en la interfaz.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 3. ScrollSpy y gestión de clase activa en la barra de navegación
    const navLinks = document.querySelectorAll('.navbar .nav-pill');

    // Solo activamos ScrollSpy y la lógica de anclas si existen esas secciones en la página actual
    const hasScrollSpySections = document.getElementById('sobre-nosotros') || document.getElementById('contacto');
    if (hasScrollSpySections) {
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

});
