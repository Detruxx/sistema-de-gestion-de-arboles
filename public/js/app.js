
//Diccionario hash de calles
const diccionarioCalles =
{
    "11 De September De 1888": "11 de Septiembre de 1888",
    "3 De Febrero": "3 de Febrero",
    "Agote Pedro": "Pedro Agote",
    "Aguilar": "Aguilar",
    "Alsina, Valentin Av.": "Avenida Valentín Alsina",
    "Alvarez Thomas Av.": "Avenida Álvarez Thomas",
    "Amenabar": "Amenábar",
    "Arcos": "Arcos",
    "Arenal, Concepcion": "Concepción Arenal",
    "Arias": "Arias",
    "Armauer Hansen, Gerardo, Dr.": "Dr. Gerardo Armauer Hansen",
    "Arredondo, Virrey": "Virrey Arredondo",
    "Arribeños": "Arribeños",
    "Artilleros": "Artilleros",
    "Aubain, Teodoro Dr.": "Dr. Teodoro Aubain",
    "Aviles, Virrey": "Virrey Avilés",
    "Azurduy Juana": "Juana Azurduy",
    "Balbin, Ricardo, Dr. Av.": "Avenida Dr. Ricardo Balbín",
    "Barilari, Atilio S., Alte.": "Almirante Atilio S. Barilari",
    "Basavilbaso, Leopoldo": "Leopoldo Basavilbaso",
    "Bavio, Ernesto A.": "Ernesto A. Bavio",
    "Besares": "Besares",
    "Betbeder, Onofre, Alte.": "Almirante Onofre Betbeder",
    "Blanco Encalada": "Blanco Encalada",
    "Borches, Jose": "José Borches",
    "Cabildo Av.": "Avenida Cabildo",
    "Calzadilla, Santiago": "Santiago Calzadilla",
    "Campos Salles": "Campos Salles",
    "Campos, Luis M. Av.": "Avenida Luis M. Campos",
    "Cantilo, Int.": "Intendente Cantilo",
    "Carbajal": "Carbajal",
    "Castañeda": "Castañeda",
    "Cazadores": "Cazadores",
    "Cerrillos": "Cerrillos",
    "Cespedes": "Céspedes",
    "Ciudad De La Paz": "Ciudad de la Paz",
    "Conde": "Conde",
    "Conesa": "Conesa",
    "Congreso": "Congreso",
    "Congreso Av.": "Avenida Congreso",
    "Correa": "Correa",
    "Corregidores": "Corregidores",
    "Cramer": "Crámer",
    "Cramer Av.": "Avenida Crámer",
    "Cuba": "Cuba",
    "De Los Incas Av.": "Avenida de los Incas",
    "Deheza": "Deheza",
    "Del Libertador Av.": "Avenida del Libertador",
    "Delgado": "Delgado",
    "Dorrego": "Dorrego",
    "Dragones": "Dragones",
    "Dumont, Santos": "Santos Dumont",
    "Echeverria": "Echeverría",
    "Elcano": "Elcano",
    "Elcano Av.": "Avenida Elcano",
    "Figueroa Alcorta, Pres. Av.": "Avenida Presidente Figueroa Alcorta",
    "Fitte, Marcelo J., Dr.": "Dr. Marcelo J. Fitte",
    "Forest Av.": "Avenida Forest",
    "Freire, Ramon, Cap. Gral.": "Capitán General Ramón Freire",
    "Garcia, Manuel J., Alte.": "Almirante Manuel J. García",
    "Garcia, Teodoro": "Teodoro García",
    "Golfarini, Juan Angel, Dr.": "Dr. Juan Ángel Golfarini",
    "Gorostiaga": "Gorostiaga",
    "Grecia": "Grecia",
    "Guayra": "Guayra",
    "Guiraldes, Int.": "Intendente Güiraldes",
    "Hernandez, Jose": "José Hernández",
    "Hernandez, Rafael": "Rafael Hernández",
    "Hiroshima": "Hiroshima",
    "Husares": "Húsares",
    "Ibera": "Iberá",
    "Irlanda": "Irlanda",
    "Jaramillo": "Jaramillo",
    "Juramento": "Juramento",
    "Juramento Av.": "Avenida Juramento",
    "La Cachila": "La Cachila",
    "La Pampa": "La Pampa",
    "Lacroze, Federico Av.": "Avenida Federico Lacroze",
    "Larralde, Crisologo Av.": "Avenida Crisólogo Larralde",
    "Las Heras": "Las Heras",
    "Loreto, Virrey": "Virrey Loreto",
    "Manzanares": "Manzanares",
    "Martinez, Enrique, Gral.": "General Enrique Martínez",
    "Matienzo, Benjamin, Teniente": "Teniente Benjamín Matienzo",
    "Maure": "Maure",
    "Melian Av.": "Avenida Melián",
    "Mendez, Nicanor": "Nicanor Méndez",
    "Mendoza": "Mendoza",
    "Migueletes": "Migueletes",
    "Miñones": "Miñones",
    "Moldes": "Moldes",
    "Monroe": "Monroe",
    "Monroe Av.": "Avenida Monroe",
    "Montañeses": "Montañeses",
    "Munich": "Munich",
    "Naon, Romulo": "Rómulo Naón",
    "Newbery, Jorge": "Jorge Newbery",
    "Nuñez": "Núñez",
    "O'higgins": "O'Higgins",
    "Obligado Rafael, Av.Costanera": "Avenida Costanera Rafael Obligado",
    "Olaguer Y Feliu, Virrey": "Virrey Olaguer y Feliú",
    "Olazabal": "Olazábal",
    "Olazabal Av.": "Avenida Olazábal",
    "Olleros": "Olleros",
    "Padre Canavery": "Padre Canavery",
    "Padre Neumann Juan B.": "Padre Juan B. Neumann",
    "Palpa": "Palpa",
    "Paroissien": "Paroissien",
    "Pedraza, Manuela": "Manuela Pedraza",
    "Perez, Gregoria": "Gregoria Pérez",
    "Pico": "Pico",
    "Pino, Virrey Del": "Virrey del Pino",
    "Pissarro, Victor": "Víctor Pissarro",
    "Plaza, Victorino De La, Dr.": "Dr. Victorino de la Plaza",
    "Prins, Enrique": "Enrique Prins",
    "Puerto Principe": "Puerto Príncipe",
    "Quesada": "Quesada",
    "Quinteros, Lidoro J. Av.": "Avenida Lidoro J. Quinteros",
    "Quiroga, Horacio": "Horacio Quiroga",
    "Ramallo": "Ramallo",
    "Ramsay": "Ramsay",
    "Ricchieri, Pablo, Tte. Gral.": "Teniente General Pablo Ricchieri",
    "Rio Piedras": "Río Piedras",
    "Rivadavia Martin, Comodoro": "Comodoro Martín Rivadavia",
    "Rivera, Pedro I., Dr.": "Dr. Pedro I. Rivera",
    "Romero, D.": "D. Romero",
    "Romero, Eduardo, Sgto.": "Sargento Eduardo Romero",
    "Roosevelt Franklin D.": "Franklin D. Roosevelt",
    "Ruiz Huidobro": "Ruiz Huidobro",
    "Saenz Valiente, Juan Pablo": "Juan Pablo Sáenz Valiente",
    "Sagasta Isla, Jose Maria": "José María Sagasta Isla",
    "Sanchez, Miguel": "Miguel Sánchez",
    "Soldado De La Independencia": "Soldado de la Independencia",
    "Solier, Daniel De, Alte.": "Almirante Daniel de Solier",
    "Sourigues, Carlos, Cnel.": "Coronel Carlos Sourigues",
    "Sucre, Antonio Jose De, Mcal.": "Mariscal Antonio José de Sucre",
    "Superi": "Superí",
    "Tamborini, Jose Pascual": "José Pascual Tamborini",
    "Tegucigalpa": "Tegucigalpa",
    "Temperley": "Temperley",
    "Tunez": "Túnez",
    "Udaondo, Guillermo Av.": "Avenida Guillermo Udaondo",
    "Ugarte, Manuel": "Manuel Ugarte",
    "Urtubey Clodomiro Cdro.": "Comodoro Clodomiro Urtubey",
    "Ushuaia Pasaje (Pje. Part.)": "Pasaje Particular Ushuaia",
    "Vedia": "Vedia",
    "Velez, Bernardo, Dr.": "Dr. Bernardo Vélez",
    "Vertiz Virrey Av.": "Avenida Virrey Vértiz",
    "Vidal": "Vidal",
    "Vilela": "Vilela",
    "Vuelta De Obligado": "Vuelta de Obligado",
    "Washington": "Washington",
    "Zabala": "Zabala",
    "Zapata": "Zapata",
    "Zapiola": "Zapiola",
    "Zarraga": "Zárraga",
    "Zavalia": "Zavalía",
    "Zuberbuhler, Carlos E.": "Carlos E. Zuberbühler"
};

// ==========================================
// VARIABLES GLOBALES
// ==========================================
let map;
let direccionesProcesadas = [];
let capaRuta = null;
let marcadoresRuta = [];

// ==========================================
// INICIALIZACIÓN DEL MAPA (LEAFLET)
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    // Centramos el mapa inicial en CABA
    map = L.map('map').setView([-34.6037, -58.3816], 12);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
});

// ==========================================
// LECTURA Y EXTRACCIÓN DEL ARCHIVO KML
// ==========================================
document.getElementById("kmlFile").addEventListener("change", function (event) {
    const file = event.target.files[0];
    let archivoListo = false;

    if (file) {
        archivoListo = true;
    }

    if (archivoListo) {
        const reader = new FileReader();

        reader.onload = function (e) {
            const kmlText = e.target.result;
            direccionesProcesadas = extraerDirecciones(kmlText);
            alert("¡KML cargado! Se encontraron " + direccionesProcesadas.length + " direcciones.");
        };

        reader.readAsText(file);
    }
});

function extraerDirecciones(kmlString) {
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(kmlString, "text/xml");
    const addressNodes = xmlDoc.getElementsByTagName("address");

    let puntos = [];
    let i = 0;
    // OSRM soporta muchísimos más puntos que Google, 
    // pero limitamos a 50 por precaución con la API pública
    let limiteAlcanzado = false;

    while (i < addressNodes.length && !limiteAlcanzado) {
        let textoDir = addressNodes[i].textContent.trim();

        if (textoDir !== "") {
            let textoLimpio = corregirDireccionBaseDeDatos(textoDir);
            puntos.push(textoLimpio);
        }
        if (puntos.length >= 50) {
            limiteAlcanzado = true;
            console.warn("Límite de 50 direcciones alcanzado.");
        }
        i++;
    }
    return puntos;
}

// FUNCION QUE CORRIGE LAS DIRECCIONES PARA UNA POSTERIOR MEJOR BUSQUEDA
function corregirDireccionBaseDeDatos(direccionOriginal) {
    let direccionCorregida = direccionOriginal;
    let originalMinuscula = direccionOriginal.toLowerCase();

    // Obtenemos todas las claves (nombres malos) del diccionario
    const callesMalas = Object.keys(diccionarioCalles);
    let i = 0;
    let coincidenciaEncontrada = false; // Nuestra bandera

    while (i < callesMalas.length && !coincidenciaEncontrada) {
        let calleMala = callesMalas[i];
        let malaMinuscula = calleMala.toLowerCase();

        // Si el texto del KML arranca con el nombre feo de la base de datos...
        if (originalMinuscula.startsWith(malaMinuscula)) {
            let calleBuena = diccionarioCalles[calleMala];

            // Recortamos la parte mala original y le concatenamos el resto 
            // (que incluye la altura, ciudad y país)
            let restoDeLaDireccion = direccionOriginal.substring(calleMala.length);
            direccionCorregida = calleBuena + restoDeLaDireccion;

            coincidenciaEncontrada = true;
        }

        i++;
    }

    return direccionCorregida;
}



// ==========================================
// CONTROLADOR DEL BOTÓN PRINCIPAL
// ==========================================
document.getElementById("btnOptimizar").addEventListener("click", async function () {
    let puedeProcesar = false;

    if (direccionesProcesadas.length > 0) {
        puedeProcesar = true;
    }

    if (puedeProcesar) {
        console.log("Iniciando geocodificación con OSM. Esto tomará unos segundos...");

        // Esperamos a que todas las direcciones se conviertan a coordenadas
        const coordenadas = await traducirConNominatim(direccionesProcesadas);

        console.log("¡Coordenadas listas! Calculando la ruta más rápida...");
        calcularRutaOSRM(coordenadas);
    }
    else {
        alert("Por favor, cargá un archivo KML primero.");
    }
});

// Función auxiliar para pausar la ejecución (1 segundo)
function esperarUnSegundo() {
    return new Promise(function (resolve) {
        setTimeout(resolve, 1000);
    });
}

// ==========================================
// GEOCODIFICACIÓN (TEXTO A COORDENADAS)
// ==========================================
async function traducirConNominatim(arregloTextos) {
    let coordenadas = [];
    let i = 0;
    let seguirTraduciendo = true;

    while (i < arregloTextos.length && seguirTraduciendo) {
        let textoBusqueda = arregloTextos[i];
        let calleResuelta = false; // Bandera para el bucle de reintentos
        let cancelarProceso = false;

        // Bucle interno: Se repite hasta que encontremos la calle o la salteemos
        while (!calleResuelta && !cancelarProceso) {
            // Le agregamos ", CABA, Argentina" acá para darle más contexto a OSM
            let url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(textoBusqueda + ", CABA, Argentina")}`;

            try {
                let respuesta = await fetch(url);
                let datos = await respuesta.json();
                let encontroLugar = false;

                if (datos.length > 0) {
                    encontroLugar = true;
                }

                if (encontroLugar) {
                    coordenadas.push(
                        {
                            lat: parseFloat(datos[0].lat),
                            lng: parseFloat(datos[0].lon)
                        });
                    calleResuelta = true; // Encontrada, salimos del bucle de reintento
                }
                else {
                    // AQUÍ INTERVENIMOS MANUALMENTE
                    let textoCorreccion = prompt("OSM no encontró esta dirección:\n\n" + textoBusqueda + "\n\nIngresá la calle corregida (o dejá en blanco para saltarla):", textoBusqueda);

                    let usuarioCancelo = false;

                    if (textoCorreccion === null || textoCorreccion.trim() === "") {
                        usuarioCancelo = true;
                    }

                    if (usuarioCancelo) {
                        calleResuelta = true; // Salimos del bucle pero sin agregar nada
                        console.warn("Se omitió la dirección: " + textoBusqueda);
                    }
                    else {
                        // Si escribiste algo, actualizamos la variable
                        // Como 'calleResuelta' sigue siendo false, el while vuelve a girar
                        textoBusqueda = textoCorreccion;
                        console.log("Reintentando con: " + textoBusqueda);
                    }
                }
            }
            catch (error) {
                console.error("Error consultando Nominatim: ", error);
                cancelarProceso = true;
                seguirTraduciendo = false; // Cortamos todo si se cae internet
            }

            // Mantenemos la pausa de 1 segundo (incluso entre reintentos)
            await esperarUnSegundo();
        }

        i++;
    }

    return coordenadas;
}

// ==========================================
// CÁLCULO DE RUTA ÓPTIMA Y DIBUJO (OSRM)
// ==========================================
function calcularRutaOSRM(listaCoordenadas) {
    let puedeCalcular = false;

    if (listaCoordenadas.length > 1) {
        puedeCalcular = true;
    }

    if (puedeCalcular) {
        let urlOSRM = "https://router.project-osrm.org/trip/v1/driving/";
        let coordenadasString = "";
        let i = 0;
        let armarUrl = true;

        // OSRM requiere el formato: longitud,latitud;longitud,latitud
        while (i < listaCoordenadas.length && armarUrl) {
            coordenadasString += listaCoordenadas[i].lng + "," + listaCoordenadas[i].lat;

            let esUltimoPunto = false;

            if (i === listaCoordenadas.length - 1) {
                esUltimoPunto = true;
            }

            if (!esUltimoPunto) {
                coordenadasString += ";";
            }

            i++;
        }

        // Agregamos parámetros: viaje circular (roundtrip) e incluir geometrías para dibujar
        urlOSRM += coordenadasString + "?roundtrip=true&source=first&geometries=geojson";

        fetch(urlOSRM)
            .then(function (respuesta) {
                return respuesta.json();
            })
            .then(function (datos) {
                let calculoExitoso = false;

                if (datos.code === "Ok") {
                    calculoExitoso = true;
                }

                if (calculoExitoso) {
                    // Limpiamos la ruta anterior
                    let hayRutaPrevia = false;

                    if (capaRuta !== null) {
                        hayRutaPrevia = true;
                    }

                    if (hayRutaPrevia) {
                        map.removeLayer(capaRuta);
                    }

                    // Dibujamos la nueva ruta
                    let geometria = datos.trips[0].geometry;
                    capaRuta = L.geoJSON(geometria,
                        {
                            style: { color: '#0288D1', weight: 5, opacity: 0.8 }
                        }).addTo(map);

                    map.fitBounds(capaRuta.getBounds());

                    // AQUÍ LLAMAMOS A LAS NUEVAS FUNCIONES MODULARES
                    gestionarPines(listaCoordenadas);
                    generarEnlacesCelular(datos);

                    console.log("¡Ruta trazada, pines dibujados y enlaces generados!");
                }
            });
    }
}

function gestionarPines(coordenadas) {
    // Limpiamos los pines viejos si existen
    let k = 0;
    let limpiarPines = true;

    while (k < marcadoresRuta.length && limpiarPines) {
        map.removeLayer(marcadoresRuta[k]);
        k++;
    }

    marcadoresRuta = []; // Vaciamos el arreglo

    // Dibujamos los pines nuevos
    let m = 0;
    let dibujarPines = true;

    while (m < coordenadas.length && dibujarPines) {
        let pin = L.marker([coordenadas[m].lat, coordenadas[m].lng]).addTo(map);
        marcadoresRuta.push(pin);
        m++;
    }
}

function generarEnlacesCelular(datosOSRM) {
    // Recuperamos el orden óptimo devuelto por OSRM
    let rutaOptimizada = new Array(datosOSRM.waypoints.length);
    let idx = 0;
    let ordenar = true;

    while (idx < datosOSRM.waypoints.length && ordenar) {
        let posicionIdeal = datosOSRM.waypoints[idx].waypoint_index;

        let lngOSRM = datosOSRM.waypoints[idx].location[0];
        let latOSRM = datosOSRM.waypoints[idx].location[1];

        rutaOptimizada[posicionIdeal] = { lat: latOSRM, lng: lngOSRM };
        idx++;
    }

    // Generamos los enlaces
    let enlacesHTML = "<h3>Links para el celular:</h3>";
    let loteActual = 0;
    let procesarLotes = true;

    while (loteActual < rutaOptimizada.length && procesarLotes) {
        let urlMaps = "https://www.google.com/maps/dir/";
        let p = 0;
        let procesarPuntos = true;

        while (p < 10 && (loteActual + p) < rutaOptimizada.length && procesarPuntos) {
            let punto = rutaOptimizada[loteActual + p];
            urlMaps += punto.lat + "," + punto.lng + "/";
            p++;
        }

        let numLote = Math.floor(loteActual / 9) + 1;
        let puntoFinalTexto = loteActual + p;

        if (puntoFinalTexto > rutaOptimizada.length) {
            puntoFinalTexto = rutaOptimizada.length;
        }

        enlacesHTML += `
        <a href="${urlMaps}" target="_blank" style="display:inline-block; margin-bottom:10px; padding:8px 12px; background-color:#0288D1; color:white; text-decoration:none; border-radius:5px; margin-right:10px;">
            📍 Abrir Tramo ${numLote} (Paradas ${loteActual + 1} a ${puntoFinalTexto})
        </a>`;

        loteActual += 9;

        if (loteActual >= rutaOptimizada.length) {
            procesarLotes = false;
        }
    }

    document.getElementById("contenedorEnlaces").innerHTML = enlacesHTML;
}