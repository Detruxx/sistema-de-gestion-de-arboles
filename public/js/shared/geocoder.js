/**
 * geocoder.js
 * 
 * Servicio global para manejar la Geocodificación Inversa (Reverse Geocoding).
 * Básicamente, acá tomamos unas coordenadas geográficas (Latitud y Longitud) y las 
 * convertimos en una dirección legible (como "Calle Falsa 123, CABA").
 * Hacemos esto utilizando la API gratuita de Nominatim de OpenStreetMap.
 * 
 * Aplicamos el principio SRP (Single Responsibility Principle) para aislar toda
 * esta lógica de transformación, así podemos reutilizarla fácilmente en cualquier mapa.
 */

window.reverseGeocodeService = async function(lat, lng) {
    try {
        // Acá hacemos la petición a la API de Nominatim pasándole nuestras coordenadas
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
        
        // Si el servidor nos responde con algún error (ej. error 500), lanzamos una excepción para atraparla abajo
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Acá convertimos la respuesta que nos llega en texto puro a un objeto JSON fácil de leer
        const data = await response.json();
        
        // Verificamos que los datos hayan llegado bien y que traigan información de la dirección
        if (data && data.address) {
            // Intentamos extraer las partes de la calle de la manera más limpia posible.
            // A veces nos viene como 'road', otras como 'pedestrian' o 'path'.
            const road = data.address.road || data.address.pedestrian || data.address.path || '';
            const number = data.address.house_number || '';
            const suburb = data.address.suburb || data.address.neighbourhood || '';
            
            // Si logramos identificar la calle, armamos el string final concatenando el nombre, el número y el barrio
            if (road) {
                return road + (number ? ' ' + number : '') + (suburb ? ', ' + suburb : '');
            } else if (data.display_name) {
                // Si Nominatim no nos dio detalles tan finos de calle, recurrimos al "display_name" general.
                // Como suele ser un texto muy largo, lo cortamos para quedarnos sólo con las primeras 3 partes (ej: "Plaza de Mayo, San Nicolás, CABA").
                return data.display_name.split(',').slice(0, 3).join(',').trim();
            }
        }
        
        // Si por alguna razón la respuesta no trajo datos legibles de calles,
        // devolvemos las coordenadas matemáticas formateadas para no dejar el campo vacío.
        return `Ubicación: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        
    } catch (err) {
        // Si ocurre algún fallo de red, se cae la API o algo se rompe, atrapamos el error acá.
        // En lugar de inventar una dirección falsa (mocking duro), simplemente devolvemos las 
        // coordenadas matemáticas precisas de forma segura.
        console.error('Error en Reverse Geocoding (Nominatim):', err);
        return `Ubicación: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    }
};
