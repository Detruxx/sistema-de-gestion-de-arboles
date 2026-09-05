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
use Illuminate\Support\Facades\Mail; 
use App\Mail\ClaimMergedMail;       

class RequestController extends Controller
{
    /**
     * Muestra un listado de los reclamos (Para el Dashboard de Admin).
     */
    public function index(Request $request)
    {
        $requests = \App\Models\Request::with(['user', 'street', 'requestType', 'tree.specie', 'histories.status', 'status', 'workOrders.company', 'priority'])->orderBy('created_at', 'desc')->get();

        $mapped = $requests->values()->map(function ($req) {
            return [
                'id' => $req->tracking_code,
                'vecino' => $req->user ? $req->user->name : 'Vecino Anónimo',
                'categoria' => $req->requestType ? $req->requestType->task_description : 'General',
                'fecha' => $req->created_at->format('Y-m-d'),
                'estado' => $req->status ? $req->status->slug : 'open',
                'descripcion' => $req->description,
                'direccion' => $req->street ? $req->street->street_name . ' ' . $req->street->street_number : 'Sin dirección',
                'especie' => $req->tree && $req->tree->specie ? $req->tree->specie->common_name : 'No vinculada',
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
                'priority' => $req->priority ? $req->priority->slug : 'low', 
                'risk_score' => $req->risk_score,
                'tree_id' => $req->tree_id,
                'latitude' => $req->tree ? (float) $req->tree->latitude : null,
                'longitude' => $req->tree ? (float) $req->tree->longitude : null,
                'photo_count' => is_array($req->path) ? count($req->path) : 0
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $mapped
        ], 200);
    }

    /**
     * Guarda un reclamo recién creado en la base de datos con algoritmo de scoring inteligente
     */
    public function store(Request $request, \App\Services\StreetService $streetService)
    {
        // 1. Validamos los datos de entrada
        $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'address'         => 'required|string', 
            'description'     => 'required|string|min:10',
            'tree_id'         => 'nullable|exists:trees,id',
            'foto'            => 'nullable|array|max:3', 
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

        $street = $streetService->resolveFromAddress($request->address);

        $photoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $photoPaths[] = $file->store('fotos/reclamos', 'public');
            }
        }

        $estadosTerminalesIds = \App\Models\RequestStatus::where('is_terminal', true)->pluck('id')->toArray();
        $posibleDuplicado = \App\Models\Request::where('street_id', $street->id)
                                ->where('request_type_id', $request->request_type_id)
                                ->whereNotIn('request_status_id', $estadosTerminalesIds)
                                ->first();

        // ALGORITMO DE SCORING 
        $descripcion = mb_strtolower($request->input('description', ''));
        $riskScore = 0;

        $palabrasCriticas = ['cable', 'fuego', 'caer', 'peligro', 'escuela', 'urgente', 'derrumba', 'electrocu'];
        $palabrasMedias   = ['rama', 'apoyado', 'tapando', 'luz', 'vereda', 'raiz', 'viento'];

        foreach ($palabrasCriticas as $critica) {
            if (str_contains($descripcion, $critica)) {
                $apariciones = substr_count($descripcion, $critica);
                $riskScore += (50 * $apariciones);
            }
        }

        foreach ($palabrasMedias as $media) {
            if (str_contains($descripcion, $media)) {
                $apariciones = substr_count($descripcion, $media);
                $riskScore += (20 * $apariciones);
            }
        }

        if ($riskScore > 100) {
            $riskScore = 100;
        }

        $defaultPriority = Priority::where('slug', 'low')->first();
        $calculatedPriorityId = $defaultPriority ? $defaultPriority->id : 1;

        if ($riskScore > 60) {
            $prioridadAuto = Priority::where('slug', 'auto-alta')->first();
            if ($prioridadAuto) { $calculatedPriorityId = $prioridadAuto->id; }
        } elseif ($riskScore > 30) {
            $prioridadAuto = Priority::where('slug', 'auto-media')->first();
            if ($prioridadAuto) { $calculatedPriorityId = $prioridadAuto->id; }
        }

        $incident = \App\Models\Request::create([
            'user_id'                => $userId,
            'tree_id'                => $request->tree_id,
            'request_type_id'        => $request->request_type_id,
            'street_id'              => $street->id,
            'description'            => $request->description,
            'path'                   => $photoPaths,
            'request_status_id'      => 1,
            'suggested_duplicate_id' => $posibleDuplicado ? $posibleDuplicado->id : null,
            'priority_id'            => $calculatedPriorityId, 
            'risk_score'             => $riskScore,             
        ]);

        $incident->histories()->create([
            'request_status_id' => 1,
            'user_id'           => $userId,
            'justification'     => 'Registro inicial del reclamo.',
        ]);

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
        $parts = explode('-', $id);
        $numericId = (int) end($parts);

        $incident = \App\Models\Request::with(['street', 'requestType', 'histories.status', 'status', 'workOrders.company', 'priority'])->find($numericId);

        if (!$incident) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Reclamo no encontrado'
            ], 404);
        }

        // Apagamos el puntito rojo si el vecino logueado está viendo su propia solicitud modificada
        if (auth()->check() && $incident->user_id === auth()->id() && $incident->is_new_for_user) {
            $incident->update(['is_new_for_user' => false]);
        }

        $estadoFrontend = $incident->status ? $incident->status->slug : 'open';

        $ultimaBitacora = $incident->histories->last();
        $respuestaAdmin = $ultimaBitacora && $incident->request_status_id > 1 
                            ? $ultimaBitacora->justification 
                            : null;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'              => $incident->tracking_code,
                'direccion'       => $incident->street ? $incident->street->street_name . ' ' . $incident->street->street_number : 'Ubicación no especificada',
                'categoria'       => $incident->requestType ? $incident->requestType->task_description : 'General',
                'fecha'           => $incident->created_at->format('d/m/Y'),
                'estado'          => $estadoFrontend,
                'respuesta_admin' => $respuestaAdmin
            ]
        ], 200);
    }

    /**
     * Actualiza el estado del reclamo y envía correos automáticos
     */
    public function updateStatus(Request $request, $id)
    {
        $parts = explode('-', $id);
        $numericId = (int) end($parts);

        // Cargamos la relación del usuario para tener su email listo
        $userRequest = \App\Models\Request::with('user')->findOrFail($numericId);

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
        
        if ($request->has('estado')) {
            $dbSlug = $request->estado;
            
            $statusObj = RequestStatus::where('slug', $dbSlug)->first();
            if ($statusObj) {
                $statusId = $statusObj->id;
            }

            //LÓGICA DE ENVÍO DE MAIL 
            if ($dbSlug === 'vinculated' && $request->has('linked_to')) {
                $linkedTo = $request->linked_to;
                $suggestedDuplicateId = null;
                
                // Si el reclamo tiene un usuario vinculado con un email válido, le disparamos el correo
                if ($userRequest->user && !empty($userRequest->user->email)) {
                    Mail::to($userRequest->user->email)->send(new \App\Mail\ClaimMergedMail($linkedTo));
                }
            } elseif ($dbSlug !== 'vinculated') {
                $linkedTo = null;
            }

            // Enviar correo de cierre si el nuevo estado es terminal y diferente al anterior
            if ($statusObj && $statusObj->is_terminal && $userRequest->request_status_id != $statusId) {
                if ($userRequest->user && !empty($userRequest->user->email)) {
                    // ID 4 = Plantación
                    if ($userRequest->request_type_id == 4) {
                        Mail::to($userRequest->user->email)->send(new \App\Mail\TreePlantedMail($userRequest));
                    } else {
                        Mail::to($userRequest->user->email)->send(new \App\Mail\RequestCompletedMail($userRequest));
                    }
                }
            }
        }

        if ($request->has('ignore_suggestion') && $request->ignore_suggestion == true) {
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
        
        $userId = auth()->id() ?? 1;

        DB::transaction(function () use ($userRequest, $statusId, $userId, $justification, $linkedTo, $suggestedDuplicateId, $request) {
            $userRequest->update([
                'request_status_id'      => $statusId,
                'linked_to'              => $linkedTo,
                'suggested_duplicate_id' => $suggestedDuplicateId,
                'is_new_for_user'        => true // Activamos el puntito de notificación para el vecino
            ]);

            if ($justification) {
                $userRequest->histories()->create([
                    'request_status_id' => $statusId,
                    'user_id'           => $userId,
                    'justification'     => $justification,
                ]);
            }

            // Si el reclamo pasa a estar certificado, marcamos las tareas finalizadas como aptas para cobro
            if ($request->estado === 'certified') {
                \App\Models\WorkOrder::where('request_id', $userRequest->id)
                    ->where('work_status', 'Finalizado')
                    ->where('payment_status', 'Pendiente')
                    ->update(['payment_status' => 'Apto para Cobro']);
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
            'data'   => $requests,
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

    /**
     * Devuelve los reclamos que tienen un árbol vinculado, con las coordenadas del árbol.
     * Pensado para mostrar pines de reclamos en el mapa público.
     */
    public function getClaimPins()
    {
        $claims = DB::table('requests')
            ->join('trees', 'requests.tree_id', '=', 'trees.id')
            ->join('request_statuses', 'requests.request_status_id', '=', 'request_statuses.id')
            ->join('request_types', 'requests.request_type_id', '=', 'request_types.id')
            ->leftJoin('streets', 'requests.street_id', '=', 'streets.id')
            ->whereNotNull('requests.tree_id')
            ->where('request_statuses.is_terminal', false)
            ->select([
                'requests.id',
                'requests.tree_id',
                'requests.description',
                'requests.created_at',
                'trees.latitude',
                'trees.longitude',
                'request_types.task_description as categoria',
                'request_statuses.status_name as estado',
                'request_statuses.color as estado_color',
                'streets.street_name',
                'streets.street_number',
            ])
            ->orderBy('requests.created_at', 'desc')
            ->get();

        // Armamos el tracking_code igual que en el modelo
        $formatted_claims = $claims->map(function ($claim) {
            $year = date('Y', strtotime($claim->created_at));
            $tracking_code = 'REC-' . $year . '-' . str_pad($claim->id, 3, '0', STR_PAD_LEFT);

            return [
                'id'             => $claim->id,
                'tracking_code'  => $tracking_code,
                'tree_id'        => $claim->tree_id,
                'latitude'       => (float) $claim->latitude,
                'longitude'      => (float) $claim->longitude,
                'categoria'      => $claim->categoria,
                'estado'         => $claim->estado,
                'estado_color'   => $claim->estado_color,
                'direccion'      => $claim->street_name
                    ? $claim->street_name . ' ' . ($claim->street_number ?? '')
                    : 'Sin dirección',
            ];
        });

        return response()->json([
            'status' => 'success',
            'count'  => $formatted_claims->count(),
            'data'   => $formatted_claims
        ], 200);
    }

    /**
     * Devuelve las fotos de un reclamo en formato JSON.
     */
    public function getPhotos($id)
    {
        $request = \App\Models\Request::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $request->path ?: []
        ], 200);
    }
}
