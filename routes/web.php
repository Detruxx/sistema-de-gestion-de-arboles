<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TreeController;

Route::get('/', function () {
    return view('welcome');
    // --- RUTAS DEL MÓDULO DE ÁRBOLES ---

    // 1. Guardar un nuevo árbol (Formulario de registro)
    Route::post('/trees', [TreeController::class, 'store']);

    // 2. Marcadores ligeros para renderizar los pines en el mapa rápido
    Route::get('/trees/map-pins', [TreeController::class, 'getMapPins']);

    // 3. Buscar árboles en estado crítico (Filtro especial del mapa)
    Route::get('/trees/critical', [TreeController::class, 'getCriticalTrees']);

    // 4. Obtener la ficha con todo el detalle de un árbol específico por su ID
    Route::get('/trees/details/{id}', [TreeController::class, 'getTreeDetails']);

    // 5. Filtrar árboles por el ID de la Especie (Select de especies)
    Route::get('/trees/by-species/{speciesId}', [TreeController::class, 'getTreesBySpecies']);

});
