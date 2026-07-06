<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function showRegistrationForm()
    {
        // Si ya esta logueado, lo mandamos directo al dashboard
        if (Auth::check()) {
            return redirect('/');
        }
        
        return view('auth.register');
    } 

    /**
     * Procesa la solicitud de registro de un nuevo vecino.
     */
    public function register(Request $request) 
    {
        // 1. Validamos los datos de entrada con mensajes específicos en español
        $credentials = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'     => 'El nombre es obligatorio.',
            'last_name.required'=> 'El apellido es obligatorio.',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Debes ingresar un correo electrónico válido.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'Las contraseñas ingresadas no coinciden.',
        ]);

        // 2. Creamos el registro del vecino en la base de datos
        $user = User::create([
            'name'       => $credentials['name'],
            'last_name'  => $credentials['last_name'],
            'email'      => $credentials['email'],
            'password'   => Hash::make($credentials['password']),
            'role'       => 'vecino', // Por defecto todos entran con el rol básico de ciudadano
        ]);

        // Ves esto orne?
        // 3. Disparamos el evento de Laravel para enviar el correo de verificación
        event(new Registered($user));

        // 4. Iniciamos la sesión automáticamente
        Auth::login($user);

        // 5. Lo redirigimos a la pantalla de aviso de verificación (verification.notice)
        return redirect()->route('verification.notice');
    }
}
