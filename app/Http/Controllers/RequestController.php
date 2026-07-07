<?php

namespace App\Http\Controllers;

use App\Models\RequestType; 
use App\Models\RequestStatus;
use App\Models\Street;
use App\Models\Priority;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /**
     * Muestra un listado de los reclamos (Para el Dashboard de Admin).
     */
    public function index(Request $request)
    {
        $requests = \App\Models\Request::with(['user', 'street', 'requestType', 'tree', 'histories.status', 'status', 'workOrders.company', 'priority'])->orderBy('created_at', 'desc')->get();

        $mapped = $requests->values()->map(function ($req, $key) {
            
            // --- HACK PARA PREVISUALIZAR ---
            $prioridadSimulada = $req->priority ? $req->priority->priority_name : 'Baja';
            if ($key === 0) $prioridadSimulada = 'auto-alta';
            if ($key === 1) $prioridadSimulada = 'auto-media';
            // -------------------------------

            return [
                'id' => $req->tracking_code,
                'vecino' => $req->user ? $req->user->name : 'Vecino Anónimo',
                'categoria' => $req->requestType ? $req->requestType->task_description : 'General',
                'fecha' => $req->created_at->format('Y-m-d'),
                'estado' => $req->status ? $req->status->slug : 'open',
                'descripcion' => $req->description,
                'direccion' => $req->street ? $req->street->street_name . ' ' . $req->street->street_number : 'Sin dirección',
                'especie' => $req->tree ? $req->tree->species_name : 'No vinculada',
                'email' => $req->user ? $req->user->email : 'sin-email@treeba.gob.ar',
                'linked_to' => $req->linked_to,
                'suggested_duplicate_id' => $req->suggested_duplicate_id,
                'raw_request_id' => $req->id,
                'work_orders' => $req->workOrders->map(function($wo) {
                    return [
                        'id' => $wo->id,
                        'task_description' => $wo->task_description,
                        'execution_order' => $wo->execution_order,
                        'company' => $wo->company ? $wo->company->name : null,
                        'status' => $wo->work_status
                    ];
                }),
                'priority' => $prioridadSimulada,
                'risk_score' => ($key === 0) ? 92 : (($key === 1) ? 58 : null) // Simular score
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $mapped
        ], 200);
    }

    // El formulario de creación de reclamos ahora vive en forms.reclamos y es manejado directamente
    // en las rutas web, por lo que el método create() ya no es necesario aquí.

    /**
     * Guarda un reclamo recién creado en la base de datos.
     */
    public function store(Request $request, \App\Services\StreetService $streetService)
    {
        // 1. Validamos los datos de entrada
        $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'address'         => 'required|string', 
            'description'     => 'required|string|min:10',
            'tree_id'         => 'nullable|exists:trees,id',
            'foto'            => 'nullable|array|max:3', // Valida que vengan como máximo 3 fotos 
            'foto.*'          => 'image|mimes:jpeg,png,jpg,webp,heic|max:10240', 
        ], [
            'request_type_id.required' => 'El campo tipo de reclamo es obligatorio.',
            'request_type_id.exists'   => 'El tipo de reclamo seleccionado es inválido.',
            'address.required'         => 'El campo dirección es obligatorio.',
            'description.required'     => 'El campo descripción es obligatorio.',
            'description.min'          => 'La descripción debe tener al menos 10 caracteres.',
            'tree_id.exists'           => 'El ID de árbol ingresado es erróneo o el árbol no fue encontrado.',
            'foto.max'                 => 'No puedes subir más de 3 fotografías.',
            'foto.*.image'             => 'El archivo debe ser una imagen.',
            'foto.*.mimes'             => 'El formato de imagen no es válido.',
            'foto.*.max'               => 'La imagen no debe pesar más de 10 MB.',
        ]);

        $userId = auth()->id() ?? 1;

        // Delegamos la lógica de la calle 
        $street = $streetService->resolveFromAddress($request->address);

        // Manejo de la subida de múltiples fotos 
        $photoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $photoPaths[] = $file->store('fotos/reclamos', 'public');
            }
        }

        // Algoritmo de Detección de Duplicados 
        $estadosTerminalesIds = \App\Models\RequestStatus::where('is_terminal', true)->pluck('id')->toArray();
        $posibleDuplicado = \App\Models\Request::where('street_id', $street->id)
                                ->where('request_type_id', $request->request_type_id)
                                ->whereNotIn('request_status_id', $estadosTerminalesIds)
                                ->first();

        // =========================================================================
        // INICIO DEL ALGORITMO DE SCORING (Lógica para el equipo de Backend)
        // =========================================================================
        // TODO (BACKEND): Implementar algoritmo de procesamiento de lenguaje natural
        // 1. Definir diccionario de palabras clave. Ej:
        //    $palabrasCriticas = ['cable', 'fuego', 'caer', 'peligro', 'escuela', 'urgente'];
        //    $palabrasMedias   = ['rama', 'apoyado', 'tapando', 'luz'];
        // 2. Procesar $request->description:
        //    - Convertir a minúsculas.
        //    - Contar cuántas palabras críticas/medias aparecen.
        // 3. Asignar puntaje (Ej: +50 por crítica, +20 por media).
        // 4. Determinar ID de prioridad ($priority_id) y puntaje ($risk_score):
        //    - ATENCIÓN BACKEND: Deben agregar 'auto-media' y 'auto-alta' en PrioritySeeder.php y DB.
        //    - Si > 60 pts -> Prioridad auto-alta (id: X)
        //    - Si > 30 pts -> Prioridad auto-media (id: Y)
        //    - Sino        -> Prioridad Baja/Normal (id: 1)
        
        $calculatedPriorityId = 1; // <--- Reemplazar con el resultado del algoritmo
        $riskScore = 0;            // <--- Reemplazar con el cálculo exacto
        // =========================================================================

        // Crear el reclamo en la base de datos
        $incident = \App\Models\Request::create([
            'user_id'           => $userId,
            'tree_id'           => $request->tree_id,
            'request_type_id'   => $request->request_type_id,
            'street_id'         => $street->id,
            'description'       => $request->description,
            'path'              => $photoPaths, // Guardamos la colección de rutas completa
            'request_status_id' => 1,
            'suggested_duplicate_id' => $posibleDuplicado ? $posibleDuplicado->id : null,
            'priority_id'       => $calculatedPriorityId, // Prioridad calculada por el algoritmo
            // 'risk_score'        => $riskScore, // TODO: Descomentar al agregar columna risk_score
        ]);

        // Registrar en la bitácora
        $incident->histories()->create([
            'request_status_id' => 1,
            'user_id'           => $userId,
            'justification'     => 'Registro inicial del reclamo.',
        ]);

        // Responder en JSON 
        if ($request->wantsJson() || $request->is('api/*') || $request->is('requests')) {
            return response()->json([
                'status'    => 'success',
                'message'   => 'Solicitud registrada con éxito',
                'data'      => $incident
            ], 201);
        }

        return redirect()->back()->with('success', '¡Su reclamo ha sido registrado con éxito!');
    }
    /**
     * Muestra el reclamo especificado (Búsqueda de Seguimiento).
     */
    public function show($id)
    {
        // 1. Extraer el ID numérico del código (Ej: de "REC-2026-018" extraer "18")
        // Si el usuario ingresa solo "18", también funcionará.
        $parts = explode('-', $id);
        $numericId = (int) end($parts);

        // 2. Buscar el reclamo en la base de datos
        // Usamos with() para traer los datos relacionados y no hacer múltiples consultas
        $incident = \App\Models\Request::with(['street', 'requestType', 'histories.status', 'status', 'workOrders.company', 'priority'])->find($numericId);

        if (!$incident) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Reclamo no encontrado'
            ], 404);
        }

        $estadoFrontend = $incident->status ? $incident->status->slug : 'open';

        // 4. Obtener la respuesta del administrador (si hay) de la última bitácora
        // Suponiendo que las justificaciones son las respuestas.
        $ultimaBitacora = $incident->histories->last();
        $respuestaAdmin = $ultimaBitacora && $incident->request_status_id > 1 
                            ? $ultimaBitacora->justification 
                            : null;

        // 5. Devolver la estructura JSON exacta que espera reclamos.js
        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'              => $incident->tracking_code,
                'direccion'       => $incident->street ? $incident->street->street_name . ' ' . $incident->street->street_number : 'Ubicación no especificada',
                'categoria'       => $incident->requestType ? $incident->requestType->name : 'General',
                'fecha'           => $incident->created_at->format('d/m/Y'),
                'estado'          => $estadoFrontend,
                'respuesta_admin' => $respuestaAdmin
            ]
        ], 200);
    }

    /**
     * Actualiza el estado del reclamo y/o la justificación en la base de datos real.
     */
    public function updateStatus(Request $request, $id)
    {
        // 1. Extraer el ID numérico del código (Ej: de "REC-2026-018" extraer "18")
        $parts = explode('-', $id);
        $numericId = (int) end($parts);

        $userRequest = \App\Models\Request::findOrFail($numericId);

        $request->validate([
            'estado'    => 'nullable|string',
            'respuesta' => 'nullable|string|max:1000',
            'linked_to' => 'nullable|integer',
            'ignore_suggestion' => 'nullable|boolean',
            'priority_name' => 'nullable|string|max:50'
        ]);

        $statusId = $userRequest->request_status_id;
        $linkedTo = $userRequest->linked_to;
        $suggestedDuplicateId = $userRequest->suggested_duplicate_id;
        
        // Si mandan un estado nuevo desde el JS (el slug real)
        if ($request->has('estado')) {
            $dbSlug = $request->estado;
            
            $statusObj = RequestStatus::where('slug', $dbSlug)->first();
            if ($statusObj) {
                $statusId = $statusObj->id;
            }

            // Lógica de duplicado manual o automático
            if ($dbSlug === 'vinculated' && $request->has('linked_to')) {
                $linkedTo = $request->linked_to;
                $suggestedDuplicateId = null;
                
                // TODO (BACKEND): Enviar notificación por correo al vecino avisando que se unificó su reclamo
                // Ej: Mail::to($userRequest->user->email)->send(new ClaimMergedMail($linkedTo));
            } elseif ($dbSlug !== 'vinculated') {
                // Si cambiamos de estado "vinculado" a cualquier otro (ej. por error),
                // desvinculamos el reclamo limpiando la columna.
                $linkedTo = null;
            }
        }

        // Ignorar sugerencia
        if ($request->has('ignore_suggestion') && $request->ignore_suggestion) {
            $suggestedDuplicateId = null;
        }

        if ($request->has('priority_name') && !empty(trim($request->priority_name))) {
            $pName = trim($request->priority_name);
            $priority = \App\Models\Priority::firstOrCreate(['priority_name' => $pName]);
            $userRequest->priority_id = $priority->id;
        }

        $justification = $request->respuesta;
        if (!$justification && $request->has('estado')) {
            $justification = 'Cambio de estado a: ' . $request->estado;
        }

        // Aca se define el usuario que va a hacer el cambio de status. Por defecto es 1
        
        $userId = auth()->id() ?? 1;

        DB::transaction(function () use ($userRequest, $statusId, $userId, $justification, $linkedTo, $suggestedDuplicateId) {
            $userRequest->update([
                'request_status_id' => $statusId,
                'linked_to' => $linkedTo,
                'suggested_duplicate_id' => $suggestedDuplicateId
            ]);

            // Solo creamos historial si hay un cambio de estado o una respuesta nueva
            if ($justification) {
                $userRequest->histories()->create([
                    'request_status_id' => $statusId,
                    'user_id'           => $userId,
                    'justification'     => $justification,
                ]);
            }
        });

        if ($request->wantsJson() || $request->is('api/*') || $request->is('requests/*')) {
            return response()->json([
                'status'    => 'success',
                'message'   => 'Solicitud actualizada con éxito',
                'data'      => $userRequest,
            ], 200);
        }

        return redirect()->back()->with('status_updated', 'El estado del reclamo y la bitácora se actualizaron correctamente.');
    }

    /**
     * Obtiene los reclamos de un árbol específico.
     */
    public function getRequestsByTree($tree_id)
    {
        $requests = \App\Models\Request::where('tree_id', $tree_id)
            ->with(['user', 'requestType', 'status'])
            ->get();
        
        if ($requests->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se encontraron solicitudes',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count'  => $requests->count(),
            'data'   => $requests,
        ], 200);
    }

    /**
     * Muestra todas las solicitudes por estado.
     */
    public function getRequestByStatus($statusSlug)
    {
        $status = RequestStatus::where('slug', $statusSlug)->first();
        if (!$status) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Estado de solicitud inválido',
            ], 400);
        }

        $requests = \App\Models\Request::where('request_status_id', $status->id)
            ->with(['tree.street', 'user', 'requestType', 'status'])
            ->get();
        
        return response()->json([
            'status' => 'success',
            'count'  => $requests->count(),
            'data'  => $requests,
        ], 200);
    }

    /**
     * Muestra todas las solicitudes por tipo.
     */
    public function getRequestByType($typeId)
    {
        $requests = DB::table('requests')
            ->join('request_types', 'requests.request_type_id', '=', 'request_types.id')
            ->join('request_statuses', 'requests.request_status_id', '=', 'request_statuses.id')
            ->join('streets', 'requests.street_id', '=', 'streets.id')
            ->join('users', 'requests.user_id', '=', 'users.id')
            ->where('requests.request_type_id', $typeId)
            ->select([
                'requests.id as request_id',
                'requests.description',
                'request_statuses.status_name as status',
                'request_statuses.slug as status_slug',
                'requests.path',
                'requests.created_at',
                'request_types.task_description as type_name',
                'streets.street_name',
                'users.name as user_name'
            ])
            ->orderBy('requests.created_at', 'desc')
            ->get(); 

        if ($requests->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se encontraron solicitudes',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count'  => $requests->count(),
            'data'   => $requests,
        ], 200);
    }

    /**
     * Devuelve todos los estados disponibles con su configuración visual.
     */
    public function getStatuses()
    {
        $statuses = RequestStatus::orderBy('sequence', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data'   => $statuses
        ], 200);
    }
}
