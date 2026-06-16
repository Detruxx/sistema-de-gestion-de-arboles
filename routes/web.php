<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\RequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mapa', function () {
    return view('mapa');
});

Route::get('/cuidados', function () {
    return view('cuidados');
});

Route::get('/tramites/reclamos', function () {
    return view('tramites.reclamos');
});

Route::get('/tramites/plantacion', function () {
    return view('tramites.plantacion');
});

Route::get('/tramites/permisos', function () {
    return view('tramites.permisos');
});

// Rutas de Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



// Endpoint para traer los pines livianos
Route::get('/api/arboles/pines', [TreeController::class, 'getMapPins']);

// Endpoint para traer el detalle de un árbol específico
Route::get('/api/arboles/{id}', [TreeController::class, 'getTreeDetails']);

//Parte de RECLAMOS
Route::resource('requests', RequestController::class);

