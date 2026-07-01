<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController; // Ya lo tenías importado acá impecable
use App\Http\Controllers\TreeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RequestTypeController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\CompanyPanelController;
use Illuminate\Foundation\Auth\EmailVerificationRequest; // 📍 NUEVO: Necesario para el link del mail
use Illuminate\Http\Request;

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

//Rutas para el Registro Público del Vecino
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// PARTE DE RECLAMOS
Route::resource('requests', RequestController::class);

Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');
Route::post('/requests', [RequestController::class, 'store']);
Route::put('/requests/update-status/{id}', [RequestController::class, 'updateStatus']);
Route::get('/requests/tree/{treeId}', [RequestController::class, 'getRequestsByTree']);
Route::get('/requests/type/{typeId}', [RequestController::class, 'getRequestsByType']);

// Rutas de Contacto (de rediseño-home)
Route::post('/contacto', [ContactController::class, 'store'])->name('contacto.store');

//RUTAS DE VERIFICACION 
Route::middleware(['auth'])->group(function () {

    // 1. La pantalla que le avisa al usuario: "Te mandamos un mail, verificalo"
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // 2. La ruta que procesa el LINK del mail (Laravel se encarga de la lógica internamente)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // Marca al usuario como verificado estampando la fecha
        return redirect('/mapa')->with('success', '¡Email verificado con éxito!');
    })->middleware(['signed'])->name('verification.verify');

    // 3. Botón por si el usuario pide reenviar el correo de verificación
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '¡Se ha reenviado el enlace de verificación!');
    })->middleware(['throttle:6,1'])->name('verification.send'); // Máximo 6 reenvíos por minuto

    
    // RUTAS COMPLEMENTARIAS QUE ADEMÁS REQUIEREN VERIFICACIÓN (`verified`)
    Route::middleware(['verified'])->group(function () {
        
        Route::get('/configuracion', [ProfileController::class, 'configuracion'])->name('profile.configuracion');
        // El resto de tus rutas internas...
        Route::post('/configuracion/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::get('/mis-reclamos', [ProfileController::class, 'misReclamos'])->name('profile.mis-reclamos');
        Route::post('/reclamos/{id}/status', [ProfileController::class, 'updateReclamoStatus'])->name('profile.reclamo.status');
        Route::get('/mensajes', [ContactController::class, 'index'])->name('contact.index');
        Route::post('/mensajes/{id}/read', [ContactController::class, 'markRead'])->name('contact.read');

        // Dashboard de Admin/Inspector
        Route::middleware(['role:admin,inspector'])->group(function () {
            Route::get('/admin/dashboard', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');

            Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
        });

        // Panel Exclusivo para Empresas Tercerizadas
        Route::middleware(['role:empresa'])->group(function () {
            Route::get('/company/dashboard', [CompanyPanelController::class, 'index'])->name('company.dashboard');
        });
    });

});


// ENDPOINTS PUBLICOS DE API
Route::get('/api/arboles/pines', [TreeController::class, 'getMapPins']);
Route::get('/api/arboles/{id}', [TreeController::class, 'getTreeDetails']);
Route::get('/api/request-types',[RequestTypeController::class, 'index']);
Route::get('/api/request-statuses', [RequestController::class, 'getStatuses']);

