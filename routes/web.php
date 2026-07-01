<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RequestTypeController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\PriorityController;

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

// Ruta específica para que el inspector actualice el estado y la justificación de un RECLAMO
Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');

// Crear un nuevo reclamo
Route::post('/requests', [RequestController::class, 'store']);

// Cambiar el estado de un reclamo
Route::put('/requests/update-status/{id}', [RequestController::class, 'updateStatus']);

// Obtener el historial de reclamos de un árbol específico
Route::get('/requests/tree/{treeId}', [RequestController::class, 'getRequestsByTree']);

// Filtrar reclamos por tipo de opción elegida
Route::get('/requests/type/{typeId}', [RequestController::class, 'getRequestsByType']);

// Rutas de Contacto (de rediseño-home)
Route::post('/contacto', [ContactController::class, 'store'])->name('contacto.store');

// Rutas protegidas por autenticación (de rediseño-home)
Route::middleware(['auth'])->group(function () {
    Route::get('/configuracion', [ProfileController::class, 'configuracion'])->name('profile.configuracion');
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
    });
});

// Endpoint para traer los pines livianos
Route::get('/api/arboles/pines', [TreeController::class, 'getMapPins']);

// Endpoint para traer el detalle de un árbol específico
Route::get('/api/arboles/{id}', [TreeController::class, 'getTreeDetails']);

// Endpoint para traer todos los tipos de reclamo
Route::get('/api/request-types',[RequestTypeController::class, 'index']);
// Endpoint para traer todos los estados de reclamo con su metadata UI
Route::get('/api/request-statuses', [\App\Http\Controllers\RequestController::class, 'getStatuses']);

// Ruta para crear órdenes de trabajo/tareas de empresas contratistas
Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');

// Rutas para el Registro 
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// 🛡️ Grupo de Administración Protegido (Rutas post-Register)
Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // 1. CONTROLADOR DE USUARIOS (UserController)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

   
    // 2. CONTROLADOR DE TRABAJOS (WorkOrderController)
    Route::get('/work-orders', [WorkOrderController::class, 'index'])->name('work_orders.index');
    Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work_orders.store');


    // 3. CONTROLADOR DE PRIORIDADES (PriorityController)
    Route::get('/priorities', [PriorityController::class, 'index'])->name('priorities.index');
    Route::post('/priorities', [PriorityController::class, 'store'])->name('priorities.store');
    Route::put('/priorities/{id}', [PriorityController::class, 'update'])->name('priorities.update');
    Route::delete('/priorities/{id}', [PriorityController::class, 'destroy'])->name('priorities.destroy');    
});

// Grupo compartido: Accesible por Admin e Inspector
    Route::middleware(['auth', 'check.role:admin,inspector'])->group(function () {
    
    // Traer todos los árboles formateados para el AJAX del front
    Route::get('/api/admin/arboles', [TreeController::class, 'getAdminTrees'])->name('api.admin.trees');

});

// Grupo nuevo exclusivo: Solo el Administrador puede entrar
    Route::middleware(['auth', 'check.role:admin'])->group(function () {
    
    // Listado global de usuarios en formato JSON
    Route::get('/api/admin/users', [UserController::class, 'index'])->name('api.admin.users.index');
    
    // Modificar el rol de un usuario específico (PATCH)
    Route::patch('/api/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('api.admin.users.updateRole');

});
