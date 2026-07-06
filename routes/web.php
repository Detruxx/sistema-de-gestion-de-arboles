<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RequestTypeController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\CompanyPanelController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home');
});

Route::get('/mapa', function () {
    return view('mapa');
});

Route::get('/cuidados', function () {
    return view('cuidados');
});

Route::get('/tramites/reclamos', function () {
    return view('forms.reclamos');
});

Route::get('/tramites/plantacion', function () {
    return view('forms.plantacion');
});

Route::get('/tramites/permisos', function () {
    return view('forms.permisos');
});

Route::get('/postulacion-empresa', function () {
    return view('forms.postulacion');
});

// Rutas de Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas para el Registro Público del Vecino
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// PARTE DE RECLAMOS
Route::resource('requests', RequestController::class)->except(['create', 'edit']);

Route::patch('/requests/{request}/update-status', [RequestController::class, 'updateStatus'])->name('requests.updateStatus');
Route::post('/requests', [RequestController::class, 'store']);
Route::put('/requests/update-status/{id}', [RequestController::class, 'updateStatus']);
Route::get('/requests/tree/{treeId}', [RequestController::class, 'getRequestsByTree']);
Route::get('/requests/type/{typeId}', [RequestController::class, 'getRequestsByType']);

// Rutas de Contacto (de rediseño-home)
Route::post('/contacto', [ContactController::class, 'store'])->name('contacto.store');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {

    // --- VERIFICACIÓN DE EMAIL ---

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
    })->middleware(['throttle:6,1'])->name('verification.send');

    // --- RUTAS QUE REQUIEREN EMAIL VERIFICADO ---
    Route::middleware(['verified'])->group(function () {

        // Perfil del vecino
        Route::get('/configuracion', [ProfileController::class, 'configuracion'])->name('profile.configuracion');
        Route::post('/configuracion/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/configuracion/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
        Route::get('/mis-reclamos', [ProfileController::class, 'myRequests'])->name('profile.mis-reclamos');
        Route::post('/reclamos/{id}/status', [ProfileController::class, 'updateRequestStatus'])->name('profile.reclamo.status');
        Route::get('/bandeja-entrada', [ProfileController::class, 'misMensajes'])->name('profile.bandeja-entrada');
        Route::get('/mensajes', [ContactController::class, 'index'])->name('contact.index');
        Route::post('/mensajes/{id}/read', [ContactController::class, 'markRead'])->name('contact.read');

        //Endpoint para la lista dinámica de árboles/reclamos en el Dashboard
        Route::get('/api/dashboard/trees-list', [DashboardController::class, 'getTreesList'])->name('dashboard.trees-list');

        // Cancelación del Vecino
        // Al ser un controlador Invocable, no hace falta especificar el nombre del método en un string
        Route::patch('/api/reclamos/{id}/cancelar', RequestCancellationController::class)->name('requests.cancelar');

        // Dashboard Exclusivo Admin
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/dashboard/admin', function () {
                return view('dashboards.admin');
            })->name('dashboard.admin');
        });

        // Dashboard Exclusivo Inspector
        Route::middleware(['role:inspector'])->group(function () {
            Route::get('/dashboard/inspector', function () {
                return view('dashboards.inspector');
            })->name('dashboard.inspector');
        });

        // Acciones compartidas Admin/Inspector
        Route::middleware(['role:admin,inspector'])->group(function () {
            // Ruta para crear órdenes de trabajo/tareas de empresas contratistas
            Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
        });

        // Dashboard Exclusivo Empresas Tercerizadas
        Route::middleware(['role:empresa'])->group(function () {
            Route::get('/dashboard/empresa', [CompanyPanelController::class, 'index'])->name('dashboard.empresa');
        });
    });

});

// ENDPOINTS PUBLICOS DE API

// Endpoint para traer los pines livianos
Route::get('/api/arboles/pines', [TreeController::class, 'getMapPins']);

// Endpoint para traer el detalle de un árbol específico
Route::get('/api/arboles/{id}', [TreeController::class, 'getTreeDetails']);

// Endpoint para traer todos los tipos de reclamo
Route::get('/api/request-types',[RequestTypeController::class, 'index']);

// Endpoint para traer todos los estados de reclamo con su metadata UI
Route::get('/api/request-statuses', [RequestController::class, 'getStatuses']);

// Datos para el Panel de la Empresa Contratista 
Route::get('/company/dashboard-data', [CompanyPanelController::class, 'getDashboardData']);

// Postulación de Empresas 
Route::post('/work-orders/{id}/apply', [WorkOrderController::class, 'applyForTender']);

// Rutas de Administración protegidas
Route::prefix('admin')->group(function () {
    // Carga el selector desplegable para asignar empresas
    Route::get('/companies', [CompanyController::class, 'getActiveCompanies']);
});

// Grupo de Administración Protegido
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

// Grupo exclusivo: Solo el Administrador puede entrar
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    // Listado global de usuarios en formato JSON
    Route::get('/api/admin/users', [UserController::class, 'index'])->name('api.admin.users.index');
    
    // Modificar el rol de un usuario específico (PATCH)
    Route::patch('/api/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('api.admin.users.updateRole');
});
