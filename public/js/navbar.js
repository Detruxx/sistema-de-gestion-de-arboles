document.addEventListener('DOMContentLoaded', () => {
    // 1. Efecto Scroll en la barra de navegación
    const navbar = document.getElementById('navbar');
    const isHomePage = window.location.pathname === '/' || window.location.pathname === '/index.php' || window.location.pathname === '';
    
    function updateNavbar() {
        if (!isHomePage || window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
    
    window.addEventListener('scroll', updateNavbar);
    updateNavbar();

    // ================= LÓGICA DEL MENU DESPLEGABLE (DROPDOWN) =================
    const navDropdowns = document.querySelectorAll('.nav-dropdown');

    navDropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        if (trigger) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                
                // Cerrar los otros menús desplegables
                navDropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('active');
                        const otherTrigger = otherDropdown.querySelector('.dropdown-trigger');
                        if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                    }
                });

                const isActive = dropdown.classList.contains('active');
                if (isActive) {
                    dropdown.classList.remove('active');
                    trigger.setAttribute('aria-expanded', 'false');
                } else {
                    dropdown.classList.add('active');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });
    document.addEventListener('click', (e) => {
        navDropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
                const trigger = dropdown.querySelector('.dropdown-trigger');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // ================= LÓGICA DEL MENU HAMBURGUESA =================
    const navToggle = document.getElementById('nav-toggle');
    const navLinksContainer = document.getElementById('nav-links');

    if (navToggle && navLinksContainer) {
        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isActive = navToggle.classList.contains('active');
            if (isActive) {
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                navLinksContainer.classList.remove('active');
            } else {
                navToggle.classList.add('active');
                navToggle.setAttribute('aria-expanded', 'true');
                navLinksContainer.classList.add('active');
            }
        });

        // Cerrar menú al hacer clic en cualquier enlace (excluyendo disparadores de dropdowns)
        const links = navLinksContainer.querySelectorAll('.nav-pill:not(.dropdown-trigger), .dropdown-menu a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                navLinksContainer.classList.remove('active');
            });
        });

        // Cerrar al hacer clic fuera del menú
        document.addEventListener('click', (e) => {
            if (!navLinksContainer.contains(e.target) && !navToggle.contains(e.target)) {
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                navLinksContainer.classList.remove('active');
            }
        });
    }


});
