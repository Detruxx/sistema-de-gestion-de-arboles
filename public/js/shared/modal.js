/**
 * Componente Compartido: Lógica global para instanciar, mostrar y ocultar modales a lo largo de la aplicación.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ================= LÓGICA DEL MODAL DE DETALLES DE CUIDADO =================
    const careModal = document.getElementById('care-modal');
    if (careModal) {
        const modalBadge = document.getElementById('modal-badge');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        const modalTipsList = document.getElementById('modal-tips-list');
        const modalImage = document.getElementById('modal-image');
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const prevBtn = document.getElementById('modal-prev-btn');
        const nextBtn = document.getElementById('modal-next-btn');

        const tipCards = document.querySelectorAll('.tip-card');
        let currentCardIndex = -1;

        // Función para actualizar y mostrar los datos del modal
        function showCard(index) {
            if (index < 0) {
                index = tipCards.length - 1;
            } else if (index >= tipCards.length) {
                index = 0;
            }
            currentCardIndex = index;

            const card = tipCards[index];
            const badgeText = card.getAttribute('data-badge');
            const titleText = card.getAttribute('data-title');
            const descText = card.getAttribute('data-description');
            const imageSrc = card.getAttribute('data-image');
            const tipsString = card.getAttribute('data-tips');

            // Inyectar datos básicos
            modalBadge.textContent = badgeText;
            modalTitle.textContent = titleText;
            modalBody.textContent = descText;
            modalImage.src = imageSrc;
            modalImage.alt = titleText;

            // Ajustar estilo del badge según categoría
            if (badgeText === 'Normativa Legal' || badgeText === 'Requiere Permiso') {
                modalBadge.classList.add('warning');
            } else {
                modalBadge.classList.remove('warning');
            }

            // Inyectar lista de tips usando innerHTML para soportar enlaces
            modalTipsList.innerHTML = '';
            if (tipsString) {
                const tipsArray = tipsString.split(';');
                tipsArray.forEach(tip => {
                    if (tip.trim()) {
                        const li = document.createElement('li');
                        li.innerHTML = tip.trim();
                        modalTipsList.appendChild(li);
                    }
                });
            }
        }

        // Función para abrir el modal
        function openModal(index) {
            showCard(index);
            careModal.classList.add('active');
        }

        // Función para cerrar el modal
        function closeModal() {
            careModal.classList.remove('active');
        }

        // Asignar eventos a las tarjetas
        tipCards.forEach((card, index) => {
            card.addEventListener('click', () => {
                openModal(index);
            });
        });

        // Eventos de botones de navegación
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                showCard(currentCardIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                showCard(currentCardIndex + 1);
            });
        }

        modalCloseBtn.addEventListener('click', closeModal);

        // Cerrar al clickear fuera del contenedor del modal
        careModal.addEventListener('click', (e) => {
            if (e.target === careModal) {
                closeModal();
            }
        });

        // Eventos de teclado (Escape para cerrar, flechas para navegar)
        document.addEventListener('keydown', (e) => {
            if (!careModal.classList.contains('active')) return;

            if (e.key === 'Escape') {
                closeModal();
            } else if (e.key === 'ArrowLeft') {
                showCard(currentCardIndex - 1);
            } else if (e.key === 'ArrowRight') {
                showCard(currentCardIndex + 1);
            }
        });
    }

});
