/**
 * Buscador de calles con autocompletado utilizando la API de USIG (GCBA).
 * Reutilizable en cualquier formulario de la plataforma TreeBA.
 */

window.initAddressAutocomplete = function(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    // Evitar inicializar dos veces
    if (input.dataset.autocompleteInitialized) return;
    input.dataset.autocompleteInitialized = 'true';

    // Crear wrapper y dropdown
    const wrapper = document.createElement('div');
    wrapper.className = 'autocomplete-wrapper';
    
    // Mover el input dentro del wrapper
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);

    const dropdown = document.createElement('ul');
    dropdown.className = 'autocomplete-dropdown';
    wrapper.appendChild(dropdown);

    let timeout = null;

    const SVG_PIN = `<svg class="autocomplete-item-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>`;

    input.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(timeout);
        
        if (query.length < 3) {
            dropdown.classList.remove('active');
            return;
        }

        dropdown.innerHTML = `<li class="autocomplete-loading">
            <svg style="animation: spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--spring-leaf)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
            Buscando...
        </li>`;
        dropdown.classList.add('active');

        timeout = setTimeout(async () => {
            try {
                const response = await fetch(`https://servicios.usig.buenosaires.gob.ar/normalizar/?direccion=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                dropdown.innerHTML = '';
                
                if (data && data.direccionesNormalizadas && data.direccionesNormalizadas.length > 0) {
                    data.direccionesNormalizadas.forEach(dir => {
                        const li = document.createElement('li');
                        li.className = 'autocomplete-item';
                        li.innerHTML = `${SVG_PIN} <span>${dir.direccion}</span>`;
                        
                        li.addEventListener('click', async () => {
                            input.value = dir.direccion;
                            dropdown.classList.remove('active');
                            // Trigger input event in case other scripts are listening
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            input.dispatchEvent(new Event('change', { bubbles: true }));

                            // Emitir evento de selección simple
                            input.dispatchEvent(new CustomEvent('addressSelected', { 
                                bubbles: true, 
                                detail: { address: dir.direccion }
                            }));

                            // Obtener coordenadas con Nominatim para el mapa
                            try {
                                const nomRes = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(dir.direccion + ', Buenos Aires, Argentina')}&limit=1`);
                                const nomData = await nomRes.json();
                                if (nomData && nomData.length > 0) {
                                    input.dispatchEvent(new CustomEvent('addressGeocoded', { 
                                        bubbles: true, 
                                        detail: { 
                                            address: dir.direccion, 
                                            lat: parseFloat(nomData[0].lat), 
                                            lng: parseFloat(nomData[0].lon) 
                                        }
                                    }));
                                }
                            } catch (e) {
                                console.error('Error obteniendo coordenadas:', e);
                            }
                        });
                        
                        dropdown.appendChild(li);
                    });
                } else {
                    dropdown.innerHTML = '<li class="autocomplete-no-results">No se encontraron direcciones en CABA</li>';
                }
            } catch (error) {
                console.error("Error al buscar dirección:", error);
                dropdown.innerHTML = '<li class="autocomplete-no-results">Error al consultar el servicio de calles</li>';
            }
        }, 400); // 400ms debounce
    });

    // Cerrar al hacer clic afuera
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

    // Mostrar al volver a hacer foco si hay texto
    input.addEventListener('focus', function() {
        if (input.value.trim().length >= 3 && dropdown.children.length > 0) {
            dropdown.classList.add('active');
        }
    });
};
