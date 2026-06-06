<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Optimizador de Rutas</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 600px; width: 100%; margin-top: 15px; border: 1px solid #ccc; }
        body { font-family: Arial, sans-serif; margin: 20px; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h1>Optimizador de Rutas (OpenStreetMap)</h1>
    
    <div>
        <input type="file" id="kmlFile" accept=".kml">
    </div>
    
    <button id="btnOptimizar" style="margin-top: 10px;">Optimizar y Dibujar</button>

    <div id="contenedorEnlaces" style="margin-top: 15px;"></div> 

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/app.js') }}"></script> <!-- esto hace que laravel reconozca el js que esta en public -->
</body>
</html>