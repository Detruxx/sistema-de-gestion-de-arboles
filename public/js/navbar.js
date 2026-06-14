document.addEventListener('DOMContentLoaded', () => {
    // 1. Efecto Scroll en la barra de navegación
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 2. ScrollSpy y gestión de clase activa en la barra de navegación
    const navLinks = document.querySelectorAll('.navbar .nav-pill');

    // Si estamos en la página del mapa, no queremos scrollspy para el inicio
    if (!window.location.pathname.includes('/mapa') && navLinks.length > 0) {
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
