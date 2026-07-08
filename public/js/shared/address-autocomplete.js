/**
 * Buscador de calles con autocompletado inteligente.
 * Acá nos encargamos de que el usuario empiece a tipear una dirección
 * y le sugiramos calles reales para evitar errores de tipeo.
 * Usamos principalmente la API de USIG (Gobierno de la Ciudad) por ser rapidísima,
 * y tenemos un respaldo (fallback) hacia Nominatim para atrapar direcciones
 * de todo el AMBA y la Provincia de Buenos Aires.
 */

window.initAddressAutocomplete = function(inputId) {
    // Primero, buscamos el input (el campo de texto) en la pantalla usando su ID.
    const input = document.getElementById(inputId);
    if (!input) return;

    // Acá chequeamos si este input ya fue inicializado antes.
    // Hacemos esto para evitar duplicar el autocompletado si la página se recarga o el script corre dos veces.
    if (input.dataset.autocompleteInitialized) return;
    input.dataset.autocompleteInitialized = 'true';

    // Creamos un contenedor (wrapper) alrededor del input para poder posicionar
    // el menú desplegable (dropdown) justo debajo de él.
    const wrapper = document.createElement('div');
    wrapper.className = 'autocomplete-wrapper';
    
    // Metemos el input adentro de nuestro nuevo wrapper
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);

    // Creamos la lista desordenada (ul) que va a actuar como nuestro menú desplegable de sugerencias
    const dropdown = document.createElement('ul');
    dropdown.className = 'autocomplete-dropdown';
    wrapper.appendChild(dropdown);

    // Guardamos una variable para el temporizador (debounce). 
    // Esto es vital para no saturar al servidor pidiéndole datos con cada letra que el usuario teclea.
    let timeout = null;

    // Este es el ícono del pin (mapa) que mostramos al lado de cada sugerencia de calle
    const SVG_PIN = `<svg class="autocomplete-item-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>`;

    // Escuchamos cada vez que el usuario escribe algo en el input
    input.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Limpiamos el temporizador anterior. Si el usuario escribe rápido, cancelamos las búsquedas viejas.
        clearTimeout(timeout);
        
        // Si escribió menos de 3 letras, cerramos el menú porque es muy pronto para buscar algo útil
        if (query.length < 3) {
            dropdown.classList.remove('active');
            return;
        }

        // Mostramos un mensaje de "Buscando..." con una animación de carga mientras esperamos al servidor
        dropdown.innerHTML = `<li class="autocomplete-loading">
            <svg style="animation: spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--spring-leaf)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
            Buscando...
        </li>`;
        dropdown.classList.add('active');

        // Configuramos el temporizador: esperamos 400 milisegundos después de la última vez que tocó una tecla para ir a buscar
        timeout = setTimeout(async () => {
            try {
                // Primero intentamos buscar en la API de USIG (que abarca CABA y es ultra rápida)
                const response = await fetch(`https://servicios.usig.buenosaires.gob.ar/normalizar/?direccion=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                // Vaciamos el desplegable para poner los nuevos resultados
                dropdown.innerHTML = '';
                
                if (data && data.direccionesNormalizadas && data.direccionesNormalizadas.length > 0) {
                    // Si USIG encontró direcciones en CABA, armamos la lista para mostrarlas
                    renderOptions(data.direccionesNormalizadas.map(d => ({
                        name: d.direccion,
                        searchQuery: d.direccion + ', Buenos Aires, Argentina'
                    })));
                } else {
                    // Acá entra el respaldo (Fallback). 
                    // Si USIG no encontró nada (probablemente el usuario busca una calle en AMBA o Provincia), le preguntamos a Nominatim.
                    const nomRes = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Buenos Aires, Argentina')}&limit=5`);
                    const nomData = await nomRes.json();
                    
                    if (nomData && nomData.length > 0) {
                        // Si Nominatim encontró la dirección en Provincia, la parseamos para que quede linda y la mostramos
                        renderOptions(nomData.map(d => {
                            const nameParts = d.display_name.split(',');
                            const cleanName = nameParts.slice(0, 3).join(',').trim();
                            return {
                                name: cleanName,
                                searchQuery: cleanName,
                                lat: parseFloat(d.lat),
                                lng: parseFloat(d.lon)
                            };
                        }));
                    } else {
                        // Si ninguna de las dos APIs encontró nada, avisamos que no hay resultados
                        dropdown.innerHTML = '<li class="autocomplete-no-results">No se encontraron direcciones</li>';
                    }
                }
            } catch (error) {
                // Si se nos cae el internet o alguna de las APIs falla, mostramos un error amigable
                console.error("Error al buscar dirección:", error);
                dropdown.innerHTML = '<li class="autocomplete-no-results">Error al consultar el servicio de calles</li>';
            }
        }, 400); // <-- 400ms es el tiempo exacto de debounce
        
        // Esta es una función auxiliar donde creamos los "botones" del menú por cada dirección que encontramos
        function renderOptions(options) {
            options.forEach(opt => {
                const li = document.createElement('li');
                li.className = 'autocomplete-item';
                li.innerHTML = `${SVG_PIN} <span>${opt.name}</span>`;
                
                // Qué pasa cuando el usuario hace clic en una calle sugerida:
                li.addEventListener('click', async () => {
                    // 1. Rellenamos el input con el nombre de la calle elegida
                    input.value = opt.name;
                    // 2. Escondemos el menú
                    dropdown.classList.remove('active');
                    // 3. Avisamos al navegador que cambiamos el valor del input, por si alguien más está escuchando
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));

                    // 4. Disparamos nuestro propio evento 'addressSelected' para saber qué calle eligió exactamente
                    input.dispatchEvent(new CustomEvent('addressSelected', { 
                        bubbles: true, 
                        detail: { address: opt.name }
                    }));

                    // 5. Acá resolvemos las coordenadas (Latitud y Longitud) para poder poner el pin en el mapa
                    if (opt.lat && opt.lng) {
                        // Si los resultados vinieron de Nominatim, ya tenemos las coordenadas, así que las emitimos directo
                        input.dispatchEvent(new CustomEvent('addressGeocoded', { 
                            bubbles: true, 
                            detail: { address: opt.name, lat: opt.lat, lng: opt.lng }
                        }));
                    } else {
                        // Si los resultados vinieron de USIG, USIG no nos da las coordenadas puras gratis tan fácil.
                        // Entonces, le pedimos amablemente a Nominatim las coordenadas de esa dirección que acabamos de elegir.
                        try {
                            const nomRes = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(opt.searchQuery)}&limit=1`);
                            const nomData = await nomRes.json();
                            if (nomData && nomData.length > 0) {
                                input.dispatchEvent(new CustomEvent('addressGeocoded', { 
                                    bubbles: true, 
                                    detail: { 
                                        address: opt.name, 
                                        lat: parseFloat(nomData[0].lat), 
                                        lng: parseFloat(nomData[0].lon) 
                                    }
                                }));
                            }
                        } catch (e) {
                            console.error('Error obteniendo coordenadas:', e);
                        }
                    }
                });
                
                // Finalmente agregamos este "botón" de calle a la lista visual
                dropdown.appendChild(li);
            });
        }
    });

    // Acá nos aseguramos de que si el usuario hace clic en cualquier otro lado de la página,
    // el menú desplegable se cierre automáticamente para no molestar.
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

    // Y acá hacemos lo contrario: si el usuario vuelve a hacer clic (foco) en el input 
    // y ya tenía algo escrito (más de 3 letras), le volvemos a abrir las sugerencias.
    input.addEventListener('focus', function() {
        if (input.value.trim().length >= 3 && dropdown.children.length > 0) {
            dropdown.classList.add('active');
        }
    });
};
