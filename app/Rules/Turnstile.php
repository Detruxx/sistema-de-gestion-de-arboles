<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    /**
     *  Corre la regla de validacion del captcha del cloudflare
     * @param string $attribute  Nombre del campo (cf-turnstile-response)
     * @param mixed $value El token generado por el navegador del usuario
     * @param Closure $fail Closure para reportar errores
     */
    public function validate (string $attribute, mixed $value, Closure $fail): void
    {
        // Si no se recibió el toke del captcha
        if (empty($value)){
            $fail('El captcha es obligatorio.');
            return;
        }
        // Consultamos la API de verificacion de cloudflare
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.cloudflare.turnstile.secret_key'),
            'response' => $value,
        ]);

        // Verificamos si la petición falló o si cloudflare nos dice que el token es invalido
        if(!$response->successful() || !$response->json('success')){
            $fail('La verificacion de seguridad de captcha ha fallado. Por favir, intente nuevamente.');
        }
    }
}
