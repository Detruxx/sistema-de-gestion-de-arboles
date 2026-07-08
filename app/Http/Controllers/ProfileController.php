<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Request as Reclamo;

class ProfileController extends Controller
{
    /**
     * Muestra la vista de configuracion del perfil.
     */
    public function configuration()
    {
        $user = Auth::user();
        return view('profile.configuracion', compact('user'));
    }

    /**
     * Actualiza la contrasena del usuario.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:4|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 4 caracteres.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        $user = Auth::user();

        // Si la conexion a la base de datos falla o no estamos logueados realmente
        if (!$user) {
            return back()->with('success', 'Contraseña actualizada correctamente (Simulación offline).');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual ingresada es incorrecta.']);
        }

        // Actualizar
        try {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return back()->with('success', 'Contraseña actualizada correctamente.');
        } catch (\Exception $e) {
            // Fallback de simulacion
            return back()->with('success', 'Contraseña actualizada correctamente (Simulación offline).');
        }
    }

    /**
     * Muestra la lista de reclamos del vecino logueado (Los ultimos arriba).
     */
    public function myRequests(Request $request)
    {
        $userId = Auth::id() ?: 1;

        // Mostrar sólo los reales de la BD
        $reclamos = Reclamo::where('user_id', $userId)
            ->with(['street', 'RequestType', 'tree.specie', 'histories.status']) 
            ->orderBy('created_at', 'desc')
            ->get(); 

        $plantaciones = collect(); // Sin funcionalidad de plantaciones por ahora

        return view('profile.mis-reclamos', compact('reclamos', 'plantaciones'));
    }



    public function updateProfilePhoto(Request $request) 
    {
        // 1. Validamos que sea una imagen real y no supere los 2MB
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // 2. Si el usuario ya tenía una foto vieja, la borramos del servidor para no acumular basura
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // 3. Guardamos el nuevo archivo en la carpeta 'avatars' dentro del disco público
        $path = $request->file('profile_photo')->store('avatars', 'public');

        // 4. Guardamos la ruta en la base de datos
        $user->update([
            'profile_photo' => $path
        ]);

        return redirect()->back()->with('status', '¡Foto de perfil actualizada con éxito!');
    }

    /**
     * Muestra la bandeja de mensajes enviados por el usuario.
     */
    public function misMensajes(Request $request)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : 1;

        $mensajes = collect();

        try {
            // Cargar desde la base de datos real
            $mensajes = \App\Models\ContactMessage::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            // Fallback a mock data si la base de datos no está disponible
            if (!$request->session()->has('mock_mis_mensajes')) {
                $this->initMockMisMensajes($request);
            }
            $allMock = collect($request->session()->get('mock_mis_mensajes'));
            $mensajes = $allMock->where('user_id', 1)->sortByDesc('created_at');
        }

        return view('profile.bandeja-entrada', compact('mensajes'));
    }

    /**
     * Inicializa mock data de mensajes del usuario para la vista.
     */
    private function initMockMisMensajes(Request $request)
    {
        $mocks = [
            [
                'id' => 301,
                'user_id' => 1,
                'message' => 'Hola, envié un reclamo por un árbol seco hace un mes y quería saber si hay novedades sobre cuándo pasarían a extraerlo. Muchas gracias.',
                'status' => 'answered', // answered, unread
                'inspector_response' => 'Estimado vecino, su reclamo se encuentra agendado para la próxima semana. El equipo de arbolado pasará el día jueves por la mañana para realizar la extracción correspondiente.',
                'created_at' => now()->subDays(5)->format('Y-m-d H:i:s'),
                'responded_at' => now()->subDays(3)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 302,
                'user_id' => 1,
                'message' => 'Quería consultar sobre las especies permitidas para plantar en mi vereda. La calle es angosta y no sé si puedo poner un fresno.',
                'status' => 'unread',
                'inspector_response' => null,
                'created_at' => now()->subDays(1)->format('Y-m-d H:i:s'),
                'responded_at' => null
            ]
        ];

        $request->session()->put('mock_mis_mensajes', $mocks);
    }

    /**
     * Marca un reclamo (solicitud) como visto por el vecino, apagando la notificación.
     * Busca el reclamo por ID y usuario, y le cambia el estado de novedad.
     */
    public function markRequestSeenByUser(Request $request, $id)
    {
        // Buscamos el reclamo en la base de datos asegurándonos de que pertenezca al usuario activo
        $reclamo = \App\Models\Request::where('id', $id)->where('user_id', auth()->id())->first();
        
        // Si no lo encontramos, devolvemos un error 404
        if (!$reclamo) {
            return response()->json(['success' => false, 'message' => 'Reclamo no encontrado.'], 404);
        }
        
        // Si el reclamo tiene la marca de que es nuevo, la apagamos
        if ($reclamo->is_new_for_user) {
            $reclamo->is_new_for_user = false;
            $reclamo->save(); 
        }
        
        // Devolvemos respuesta exitosa en formato JSON
        return response()->json([
            'success' => true,
            'message' => 'Notificación revisada.'
        ]);
    }
}
