<?php

namespace App\Http\Middleware;

use Closure;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ValidateTurnstile
{
    /**
     * Valida el captcha antes de que la petición llegue al controlador.
     */
    public function handle(Request $request, Closure $next)
    {
        // Validamos el captcha usando la regla que ya creamos en la regla del proyecto
        $validator = validator($request->all(), [
            'cf-turnstile-response' => ['required', new Turnstile()],
        ], [
            'cf-turnstile-response.required' => 'Debes completar el captcha de seguridad.',
        ]);

        if ($validator->fails()) {
            // Esto redirige automáticamente hacia atrás con los errores
            throw new ValidationException($validator);
        }

        return $next($request);
    }
}
