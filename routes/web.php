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
    Route::post('/reclamos/{id}/status', [ProfileController::class, 'updateReclamoStatus'])->name('profile.reclamo.status');
    Route::get('/mensajes', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/mensajes/{id}/read', [ContactController::class, 'markRead'])->name('contact.read');

    // Dashboard de Admin/Inspector (reemplaza ver-reclamos)
    Route::middleware(['role:admin,inspector'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});

// Endpoint para traer los pines livianos
Route::get('/api/arboles/pines', [TreeController::class, 'getMapPins']);

// Endpoint para traer el detalle de un árbol específico
Route::get('/api/arboles/{id}', [TreeController::class, 'getTreeDetails']);

// API de Reclamos (usada por el Dashboard y el formulario de reclamos)
Route::get('/api/reclamos', [App\Http\Controllers\ComplaintController::class, 'index']);
Route::post('/api/reclamos', [App\Http\Controllers\ComplaintController::class, 'store']);
Route::get('/api/reclamos/{id}', [App\Http\Controllers\ComplaintController::class, 'show']);
Route::put('/api/reclamos/{id}/status', [App\Http\Controllers\ComplaintController::class, 'updateStatus']);
