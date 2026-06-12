<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TreeController;

Route::get('/', function () {
    return view('welcome');
});

// =========================================================================
// RUTAS DEL MÓDULO DE ÁRBOLES
// =========================================================================

// 1. Guardar un nuevo árbol (Formulario de registro)
Route::post('/trees', [TreeController::class, 'store']);

// 2. Actualizar el estado de inspección de un árbol específico
Route::put('/trees/update-status/{id}', [TreeController::class, 'updateStatus']);

// 3. Obtener la ficha con todo el detalle de un árbol específico por su ID
Route::get('/trees/details/{id}', [TreeController::class, 'getTreeDetails']);

// 4. Buscar árboles en estado crítico (Filtro especial del mapa)
Route::get('/trees/critical', [TreeController::class, 'getCriticalTrees']);

// 5. Marcadores ligeros para renderizar los pines en el mapa rápido
Route::get('/trees/map-pins', [TreeController::class, 'getMapPins']);

// 6. Filtrar árboles por el ID de la Especie (Select de especies)
Route::get('/trees/by-species/{speciesId}', [TreeController::class, 'getTreesBySpecies']);

// --- LAS 3 VARIANTES DE BÚSQUEDA URBANA POR CALLE ---

// Variante A: Búsqueda general por nombre aproximado de la calle
Route::get('/trees/search/street-general', [TreeController::class, 'getTreesByStreet']);

// Variante B: Búsqueda por cuadra (Nombre de calle + altura aproximada para calcular rango)
Route::get('/trees/search/block', [TreeController::class, 'getTreesByBlock']);

// Variante C: Búsqueda por frente exacto (ID de la calle + chapa de la casa exacta)
Route::get('/trees/search/front-exact', [TreeController::class, 'getTreesByExactAddress']);
