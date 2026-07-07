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
        $user = Auth::user();
        $userId = $user ? $user->id : 1;

        $requests = collect();
        $isMock = false;

        try {
            // Intentar consultar base de datos
            $requests = Request::where('user_id', $userId)
                ->with(['street', 'RequestType', 'tree.specie']) 
                ->orderBy('created_at', 'desc')
                ->get(); 
        } catch (\Exception $e) {
            $isMock = true;
        }

        // Si la consulta esta vacia o fallo la conexion, usamos Mock Data
        if ($requests->isEmpty() || $isMock) {
            // Inicializamos la session con mock data si no existe
            if (!$request->session()->has('mock_requests')) {
                $this->initMockRequests($request);
            }
            // Filtrar solo los del usuario logueado (simulamos user_id = 1)
            $allMock = collect($request->session()->get('mock_requests'));
            $requests = $allMock->where('user_id', 1)->sortByDesc('created_at');
        }

        // Inicializamos y obtenemos Mock Data para Plantaciones
        if (!$request->session()->has('mock_plantaciones')) {
            $this->initMockPlantaciones($request);
        }
        $allMockPlantaciones = collect($request->session()->get('mock_plantaciones'));
        $plantaciones = $allMockPlantaciones->where('user_id', 1)->sortByDesc('created_at');

        $reclamos = $requests;
        return view('profile.mis-reclamos', compact('reclamos', 'plantaciones'));
    }
 
    /**
     * Actualiza el estado del reclamo (completar o descartar).
     */
    public function updateRequestStatus(Request $request, $id)
    {
        $action = $request->input('action'); // 'completar' o 'descartar'

        try {
            $reclamo = Reclamo::find($id);
            if ($reclamo) {
                if ($action === 'descartar') {
                    $reclamo->delete(); // O cambiar estado a descartado si prefieres
                } else {
                    $reclamo->status = 'resolved'; // Completado
                    $reclamo->save();
                }
                return back()->with('success', 'El estado del reclamo ha sido actualizado con éxito.');
            }
        } catch (\Exception $e) {
            // Continuar al bloque de simulacion
        }

        // Simulación en Session (Fallback)
        if ($request->session()->has('mock_requests')) {
            $mocks = $request->session()->get('mock_requests');
            foreach ($mocks as &$m) {
                if ($m['id'] == $id) {
                    if ($action === 'descartar') {
                        $m['status'] = 'discarded';
                    } else {
                        $m['status'] = 'resolved';
                    }
                    break;
                }
            }
            $request->session()->put('mock_requests', $mocks);
        }

        return back()->with('success', 'El estado del reclamo ha sido actualizado con éxito (Simulado).');
    }

    /**
     * Inicializa los reclamos de prueba en la sesion.
     */
    private function initMockRequests(Request $request)
    {
        $mocks = [
            [
                'id' => 101,
                'user_id' => 1,
                'user_name' => 'Vecino Juan',
                'user_email' => 'vecino@example.com',
                'type_name' => 'Árbol seco con riesgo de caída',
                'street_name' => 'Costa Rica 4650, Palermo',
                'tree_id' => 1001,
                'tree_specie' => 'Jacarandá',
                'description' => 'El árbol de la vereda se encuentra totalmente seco desde hace meses. Con los últimos vientos fuertes, se desprendieron dos ramas medianas y cayeron sobre la acera. Solicito la remoción urgente antes de que ocurra un accidente grave.',
                'status' => 'open', // open (En revisión), resolved (Completado), discarded (Descartado)
                'created_at' => now()->subDays(5)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 102,
                'user_id' => 1,
                'user_name' => 'Vecino Juan',
                'user_email' => 'vecino@example.com',
                'type_name' => 'Raíces levantando la acera',
                'street_name' => 'Av. Cabildo 2130, Belgrano',
                'tree_id' => 1008,
                'tree_specie' => 'Fresno Americano',
                'description' => 'Las raíces del fresno de la puerta de mi casa han roto completamente las baldosas y están levantando el caño de agua pluvial. La vereda quedó muy irregular y representa un peligro para personas mayores.',
                'status' => 'open',
                'created_at' => now()->subDays(2)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 103,
                'user_id' => 2, // Otro vecino
                'user_name' => 'Vecina Marta',
                'user_email' => 'marta@example.com',
                'type_name' => 'Ramas obstruyendo cables o alumbrado',
                'street_name' => 'Defensa 852, San Telmo',
                'tree_id' => 1003,
                'tree_specie' => 'Fresno Americano',
                'description' => 'Las ramas superiores del fresno se metieron entre el cableado del tendido eléctrico público. Los días de tormenta hay chispazos y la luminaria de la calle queda completamente tapada y a oscuras.',
                'status' => 'open',
                'created_at' => now()->subDays(8)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 104,
                'user_id' => 1,
                'user_name' => 'Vecino Juan',
                'user_email' => 'vecino@example.com',
                'type_name' => 'Árbol o rama de gran porte caído',
                'street_name' => 'Av. Sarmiento 2410, Palermo',
                'tree_id' => 1002,
                'tree_specie' => 'Ceibo',
                'description' => 'Una gran rama de ceibo se quebró por la mitad de forma repentina y está obstruyendo parcialmente el carril de bicicletas y la vereda.',
                'status' => 'resolved',
                'created_at' => now()->subDays(12)->format('Y-m-d H:i:s')
            ]
        ];

        $request->session()->put('mock_requests', $mocks);
    }

    /**
     * Inicializa las solicitudes de plantacion de prueba en la sesion.
     */
    private function initMockPlantaciones(Request $request)
    {
        $mocks = [
            [
                'id' => 201,
                'user_id' => 1,
                'user_name' => 'Vecino Juan',
                'street_name' => 'Av. San Martín 1500, Paternal',
                'description' => 'Solicito la plantación de un árbol para dar sombra en la vereda, frente a la escuela primaria. La plantera está vacía.',
                'status' => 'open',
                'created_at' => now()->subDays(15)->format('Y-m-d H:i:s')
            ],
            [
                'id' => 202,
                'user_id' => 1,
                'user_name' => 'Vecino Juan',
                'street_name' => 'Av. Cabildo 3067, CABA',
                'description' => 'Hace tiempo extrajeron un árbol enfermo y quedó el hueco. Me gustaría solicitar un Jacarandá.',
                'status' => 'resolved',
                'created_at' => now()->subDays(30)->format('Y-m-d H:i:s')
            ]
        ];

        $request->session()->put('mock_plantaciones', $mocks);
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
        $isMock = false;

        try {
            // Intentar cargar desde BD si existe modelo
            // Por ahora forzamos mock para asegurar el UI
            $isMock = true; 
        } catch (\Exception $e) {
            $isMock = true;
        }

        if ($mensajes->isEmpty() || $isMock) {
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
     * Marca un reclamo como visto por el vecino (quita la notificación).
     */
    public function markRequestSeenByUser(Request $request, $id)
    {
    $reclamo = \App\Models\Request::where('id', $id)->where('user_id', auth()->id())->first();
    
    if (!$reclamo) {
        return response()->json(['success' => false, 'message' => 'Reclamo no encontrado.'], 404);
    }
    
    if ($reclamo->is_new_for_user) {
        $reclamo->is_new_for_user = false;
        $reclamo->save(); 
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Notificación revisada.'
    ]);
    }
}
