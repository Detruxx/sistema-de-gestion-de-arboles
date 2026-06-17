<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;

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

// PARTE DE RECLAMOS
Route::resource('requests', RequestController::class);

// Rutas de Contacto
Route::post('/contacto', [ContactController::class, 'store'])->name('contacto.store');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/configuracion', [ProfileController::class, 'configuracion'])->name('profile.configuracion');
    Route::post('/configuracion/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/mis-reclamos', [ProfileController::class, 'misReclamos'])->name('profile.mis-reclamos');
    Route::get('/ver-reclamos', [ProfileController::class, 'verReclamos'])->name('profile.ver-reclamos');
    Route::post('/reclamos/{id}/status', [ProfileController::class, 'updateReclamoStatus'])->name('profile.reclamo.status');
    Route::get('/mensajes', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/mensajes/{id}/read', [ContactController::class, 'markRead'])->name('contact.read');
});

// Endpoint para traer los pines livianos
Route::get('/api/arboles/pines', [TreeController::class, 'getMapPins']);

// Endpoint para traer el detalle de un árbol específico
Route::get('/api/arboles/{id}', [TreeController::class, 'getTreeDetails']);




