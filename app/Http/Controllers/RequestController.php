<?php

namespace App\Http\Controllers;

use App\Models\RequestType; 
use App\Models\RequestStatus;
use App\Models\Street;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /**
     * Muestra un listado de los reclamos.
     */
    public function index()
    {
        //
    }

    /**
     * Muestra el formulario para crear un nuevo reclamo.
     */
    public function create()
    {
        // 1. Traemos los tipos de reclamo de la base de datos
        $tiposDeReclamo = RequestType::all();

        // 2. Traemos las calles para que el vecino también elija dónde es el problema
        $calles = Street::all();

        // 3. Cargamos la vista "create" y le enviamos las dos variables
        return view('requests.create', compact('tiposDeReclamo', 'calles'));
    }

    /**
     * Guarda un reclamo recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        // Aquí llegará el reclamo cuando el usuario haga clic en "Enviar"
        $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'street_id'       => 'required|exists:streets,id',
            'description'     => 'required|string|min:10',
            'tree_id'         => 'nullable|exists:trees,id',
            'path'            => 'nullable|string',
        ]);

        $userId = auth()->id() ?? $request->input('user_id', 1);

        // Guardar en la base de datos
        $incident = \App\Models\Request::create([
            'user_id'           => $userId,
            'tree_id'           => $request->tree_id,
            'request_type_id'   => $request->request_type_id,
            'street_id'         => $request->street_id,
            'description'       => $request->description,
            'path'              => $request->input('path', 'fotos/default.jpg'),
            'request_status_id' => 1, // El ID 1 corresponde a 'open' (Pendiente)
        ]);

        // Registrar en la bitácora
        $incident->histories()->create([
            'request_status_id' => 1,
            'user_id'           => $userId,
            'justification'     => 'Registro inicial del reclamo.',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'    => 'success',
                'message'   => 'Solicitud registrada con éxito',
                'data'      => $incident
            ], 201);
        }

        return redirect()->back()->with('success', '¡Su reclamo ha sido registrado con éxito!');
    }

    /**
     * Muestra el reclamo especificado.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario para editar el reclamo especificado.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza el reclamo especificado en la base de datos.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Elimina el reclamo especificado de la base de datos.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Actualiza el estado del reclamo y registra el motivo en el historial.
     */
    public function updateStatus(Request $request, $id)
    {
        $treeRequest = \App\Models\Request::findOrFail($id);

        if ($request->has('request_status_id')) {
            $request->validate([
                'request_status_id' => 'required|exists:request_statuses,id',
                'justification'     => 'required|string|min:5|max:1000', 
            ]);
            $statusId = $request->request_status_id;
        } else {
            $request->validate([
                'status'        => 'required|string|exists:request_statuses,slug',
                'justification' => 'nullable|string|min:5|max:1000',
            ]);
            $status = RequestStatus::where('slug', $request->status)->firstOrFail();
            $statusId = $status->id;
        }

        $justification = $request->input('justification', 'Actualización de estado realizada por el inspector.');
        $userId = auth()->id() ?? $request->input('user_id', 2);

        DB::transaction(function () use ($treeRequest, $statusId, $userId, $justification) {
            $treeRequest->update([
                'request_status_id' => $statusId
            ]);

            $treeRequest->histories()->create([
                'request_status_id' => $statusId,
                'user_id'           => $userId,
                'justification'     => $justification,
            ]);
        });

        if ($request->wantsJson()) {
            $treeRequest->load(['status', 'user']);
            return response()->json([
                'status'    => 'success',
                'message'   => 'Solicitud actualizada con éxito',
                'data'      => $treeRequest,
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
                'status' => 'error',
                'message' => 'No se encontraron solicitudes',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count'  => $requests->count(),
            'data'   => $requests,
        ], 200);
    }
}
