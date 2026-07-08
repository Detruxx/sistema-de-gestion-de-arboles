<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Aca van los middleware de la aplicacion
    ->withMiddleware(function (Middleware $middleware): void {
        // middleware de autenticacion: protege las rutas privadas
        $middleware->alias([
            // middleware de roles
            'role' => \App\Http\Middleware\CheckRole::class,
            // middleware de captcha cloudflare
            'turnstile' => \App\Http\Middleware\ValidateTurnstile::class,
        ]);
    })
    // Manejo de errores personalizado: lo que hace es atrapar los errores globales y los convierte en respuestas JSON
    // Mas que nada aca son errores de tipo fetch, 404, 500, etc
     ->withExceptions(function (Exceptions $exceptions) {
        
        // Atajamos los errores globalmente si la petición es de tipo AJAX o API
        $exceptions->render(function (Throwable $e, Request $request) {
            // si la peticion es de tipo AJAX o API, convertimos los errores en respuestas JSON
            if ($request->is('api/*') || $request->ajax() || $request->wantsJson()) {
                
                // 1. Detectamos el código de estado HTTP dinámicamente
                $statusCode = 500;
                if (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException || $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $statusCode = 404;
                }

                // 2. Estructuramos la respuesta JSON limpia que va a recibir el front
                return response()->json([
                    'status'  => 'error',
                    // mensaje dinámico que detecta el tipo de error, default: Error interno del servidor.
                    'message' => $statusCode === 500 ? 'Error interno del servidor.' : $e->getMessage(),
                    // Si están en modo local (.env con APP_DEBUG=true), enviamos la data técnica para trackear el error
                    'debug'   => config('app.debug') ? [
                        'exception' => get_class($e),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                        'trace'     => array_slice($e->getTrace(), 0, 5), // Solo los primeros 5 pasos para no saturar
                    ] : null
                ], $statusCode);
            }
        });
    })->create();
