<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    /**
     * Guarda el mensaje enviado por el vecino.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|min:5',
        ], [
            'mensaje.required' => 'El cuerpo del mensaje es obligatorio.',
            'mensaje.min' => 'El mensaje debe contener al menos 5 caracteres.',
        ]);

        $user = Auth::user();
        $userId = $user ? $user->id : 1; // Fallback al vecino de pruebas si no se puede determinar

        try {
            ContactMessage::create([
                'user_id' => $userId,
                'message' => $request->mensaje,
                'status' => 'unread',
                'inspector_response' => null,   // Nace sin respuesta
                'is_new_for_user' => false     // Todavía no hay notificación para el usuario
            ]);
            return back()->with('success', '¡Tu mensaje ha sido enviado con éxito! Nos pondremos en contacto a la brevedad.');
        } catch (\Exception $e) {
            // Continuar al fallback de sesion
        }

        // Fallback de simulación en session
        if (!$request->session()->has('mock_mensajes')) {
            $this->initMockMensajes($request);
        }

        $newMsg = [
            'id' => rand(200, 999),
            'user_name' => $user ? $user->name : 'Vecino Juan',
            'user_email' => $user ? $user->email : 'vecino@example.com',
            'message' => $request->mensaje,
            'status' => 'unread',
            'inspector_response' => null,   // Simulación
            'is_new_for_user' => false,     // Simulación
            'created_at' => now()->format('Y-m-d H:i:s')
        ];

        $mocks = $request->session()->get('mock_mensajes');
        $mocks[] = $newMsg;
        $request->session()->put('mock_mensajes', $mocks);

        return back()->with('success', '¡Tu mensaje ha sido enviado con éxito! (Simulación offline).');
    }

    /**
     * Lista los mensajes de contacto para el inspector/admin (los mas viejos arriba).
     */
    public function index(Request $request)
    {
        $mensajes = collect();
        $isMock = false;

        try {
            $mensajes = ContactMessage::with('user')
                ->orderBy('created_at', 'asc')
                ->get();
        } catch (\Exception $e) {
            $isMock = true;
        }

        if ($mensajes->isEmpty() || $isMock) {
            if (!$request->session()->has('mock_mensajes')) {
                $this->initMockMensajes($request);
            }
            $allMock = collect($request->session()->get('mock_mensajes'));
            // Los mas viejos arriba
            $mensajes = $allMock->sortBy('created_at');
        }

        return view('profile.mensajes', compact('mensajes'));
    }

    /**
     * Guarda la respuesta del inspector y activa la notificación visual para el vecino.
     */
    public function respond(Request $request, $id)
    {
        $request->validate([
            'respuesta_inspector' => 'required|string|min:5',
        ], [
            'respuesta_inspector.required' => 'La respuesta no puede estar vacía.',
            'respuesta_inspector.min' => 'La respuesta debe contener al menos 5 caracteres.',
        ]);

        try {
            $msg = ContactMessage::find($id);
            if ($msg) {
                $msg->update([
                    'inspector_response' => $request->respuesta_inspector,
                    'is_new_for_user' => true,   // 📍 Activamos el puntito rojo de notificación para el vecino
                    'status' => 'read'           // Al responderlo, se marca automáticamente como leído por el admin
                ]);
                return back()->with('success', 'Respuesta enviada con éxito al vecino.');
            }
        } catch (\Exception $e) {
            // Continuar al fallback
        }

        // Fallback en session
        if ($request->session()->has('mock_mensajes')) {
            $mocks = $request->session()->get('mock_mensajes');
            foreach ($mocks as &$m) {
                if ($m['id'] == $id) {
                    $m['inspector_response'] = $request->respuesta_inspector;
                    $m['is_new_for_user'] = true; // Simulación
                    $m['status'] = 'read';
                    break;
                }
            }
            $request->session()->put('mock_mensajes', $mocks);
        }

        return back()->with('success', 'Respuesta guardada con éxito (Simulado).');
    }

    /**
     * Marca un mensaje como leido manualmente por el Inspector.
     */
    public function markRead(Request $request, $id)
    {
        try {
            $msg = ContactMessage::find($id);
            if ($msg) {
                $msg->status = 'read';
                $msg->save();
                return back()->with('success', 'Mensaje marcado como leído.');
            }
        } catch (\Exception $e) {
            // Continuar al fallback
        }

        // Fallback en session
        if ($request->session()->has('mock_mensajes')) {
            $mocks = $request->session()->get('mock_mensajes');
            foreach ($mocks as &$m) {
                if ($m['id'] == $id) {
                    $m['status'] = 'read';
                    break;
                }
            }
            $request->session()->put('mock_mensajes', $mocks);
        }

        return back()->with('success', 'Mensaje marcado como leído (Simulado).');
    }

    /**
     * Inicializa los mensajes de prueba con las nuevas propiedades.
     */
    private function initMockMensajes(Request $request)
    {
        $mocks = [
            [
                'id' => 201,
                'user_name' => 'Vecino Juan',
                'user_email' => 'vecino@example.com',
                'message' => 'Hola, quería consultar si hay planes de reforestación para la Comuna 13 en la calle Amenábar este invierno, ya que se extrajeron varios árboles viejos hace poco.',
                'status' => 'unread',
                'inspector_response' => null,
                'is_new_for_user' => false,
                'created_at' => now()->subDays(6)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 202,
                'user_name' => 'Vecina Marta',
                'user_email' => 'marta@example.com',
                'message' => 'Buenas tardes. Excelente la plataforma TreeBA. Me gustaría saber si hacen talleres de poda o cuidado del arbolado urbano para los vecinos. ¡Saludos!',
                'status' => 'unread',
                'inspector_response' => 'Hola Marta, ¡muchas gracias! Sí, realizamos talleres los primeros sábados de cada mes. Próximamente abriremos la inscripción en la web.',
                'is_new_for_user' => true, // Arranca simulando una respuesta nueva sin abrir
                'status' => 'read',
                'created_at' => now()->subDays(3)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 203,
                'user_name' => 'Vecino Juan',
                'user_email' => 'vecino@example.com',
                'message' => 'Les escribo para avisar que la plantera de Av. Cabildo 2130 está con muchos escombros de una obra cercana. Ya limpié un poco pero el árbol necesita ayuda.',
                'status' => 'read',
                'inspector_response' => null,
                'is_new_for_user' => false,
                'created_at' => now()->subDays(10)->format('Y-m-d H:i:s')
            ]
        ];

        $request->session()->put('mock_mensajes', $mocks);
    }
}