document.addEventListener('DOMContentLoaded', () => {
    const speciesModal = document.getElementById('species-modal');
    if (speciesModal) {
        const modalBadge = document.getElementById('species-modal-badge');
        const modalTitle = document.getElementById('species-modal-title');
        const modalBody = document.getElementById('species-modal-body');
        const modalTipsList = document.getElementById('species-modal-tips-list');
        const modalImage = document.getElementById('species-modal-image');
        const modalCloseBtn = document.getElementById('species-close-btn');
        const prevBtn = document.getElementById('species-prev-btn');
        const nextBtn = document.getElementById('species-next-btn');

        const speciesCards = document.querySelectorAll('.species-card');
        let currentCardIndex = -1;

        function showCard(index) {
            if (index < 0) {
                index = speciesCards.length - 1;
            } else if (index >= speciesCards.length) {
                index = 0;
            }
            currentCardIndex = index;

            const card = speciesCards[index];
            const badgeText = card.getAttribute('data-badge');
            const titleText = card.getAttribute('data-title');
            const scientificName = card.getAttribute('data-scientific');
            const descText = card.getAttribute('data-description');
            const imageSrc = card.getAttribute('data-image');
            const tipsString = card.getAttribute('data-tips');

            // Inyectar datos básicos
            modalBadge.textContent = badgeText;
            modalTitle.innerHTML = `${titleText} <span class="modal-scientific-name">${scientificName}</span>`;
            modalBody.textContent = descText;
            modalImage.src = imageSrc;
            modalImage.alt = titleText;

            // Inyectar lista de tips
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

        function openModal(index) {
            showCard(index);
            speciesModal.classList.add('active');
        }

        function closeModal() {
            speciesModal.classList.remove('active');
        }

        speciesCards.forEach((card, index) => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', () => {
                openModal(index);
            });
        });

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

        speciesModal.addEventListener('click', (e) => {
            if (e.target === speciesModal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (!speciesModal.classList.contains('active')) return;

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
