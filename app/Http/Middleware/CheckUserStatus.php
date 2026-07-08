<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos si hay un usuario logueado en la petición
        if (auth()->check()) {
            $user = auth()->user();

            // 2. Si el usuario está deshabilitado (user_status_id != 1)
            if ($user->user_status_id !== 1) {
                
                // Cerramos la sesión inmediatamente
                auth()->logout();
                
                // Invalidamos la sesión actual por seguridad
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redireccionamos al login con un mensaje de error explícito
                return redirect()->route('login')->withErrors([
                    'email' => 'Tu cuenta ha sido deshabilitada por el administrador del sistema.'
                ]);
            }

            // 3. Control extra: Si es un usuario de empresa, verificamos si su Empresa también fue deshabilitada
            if ($user->role === 'empresa' && $user->company) {
                if ($user->company->user_status_id !== 1) {
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->withErrors([
                        'email' => 'La empresa contratista a la que perteneces ha sido deshabilitada.'
                    ]);
                }
            }
        }

        return $next($request);
    }
}
