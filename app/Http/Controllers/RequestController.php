<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as IncidentRequest;
use App\Models\Request_Type;
use App\Models\Street;

class RequestController extends Controller
{

    // =========================
    // FUNCIONES
    // =========================

/*
    Muestra el formulario para crear un reclamo
*/
   public function create()
    {
        // 1. Traemos los 7 tipos de reclamo de la base de datos
        $tiposDeReclamo = Request_Type::all(); 

        // 2. Traemos las calles para que el vecino también elija dónde es el problema
        $calles = Street::all();

        // 3. Cargamos la vista "create" y le enviamos las dos variables
        return view('requests.create', compact('tiposDeReclamo', 'calles'));
    }

/*
    Guarda una solicitud nueva en la base de datos
*/
    public function store(Request $request)
    {
        // Validamos los datos que vienen del formulario
       $validatedData = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'tree_id'         => 'required|exists:trees,id',
            'request_type_id' => 'required|exists:request_types,id',
            'street_id'       => 'required|exists:streets,id',
            'description'     => 'required|string',
            'path'            => 'required|string',
        ]);

        // Creamos la instancia de la Solicitud
        $incident = new IncidentRequest();
        $incident->user_id          = $validatedData['user_id'];
        $incident->tree_id          = $validatedData['tree_id'];
        $incident->request_type_id  = $validatedData['request_type_id'];
        $incident->street_id        = $validatedData['street_id'];
        $incident->description      = $validatedData['description'];
        $incident->path             = $validatedData['path'];
        
        // Estado inicial 'pending'
        $incident->status           = 'open'; 

        // Guardamos la solicitud
        $incident->save();
 
        return response()->json([
            'status'    => 'success', 
            'message'   => 'Solicitud registrada con éxito', 
            'data'      => $incident
        ], 201);
    }

    
/*
    Cambia el estado de una solicitud existente
*/
    public function UpdateStatus(Request $request, $id)
    {
        // Validamos los datos que vienen del formulario
        $request->validate([
            'status' => 'required|string|in:open,in_progress,resolved',
        ]);

        // Buscamos la solicitud por su ID
        $incident = IncidentRequest::find($id);
        
        if (!$incident) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Solicitud no encontrada',
            ], 404);
        }

        // Actualizamos el estado de la solicitud
        $incident->status = $request['status'];
        $incident->save();

        // Respondemos con el estado actualizado
        return response()->json([
            'status'    => 'success',
            'message'   => 'Solicitud actualizada con éxito',
            'data'      => $incident,
        ], 200);
    }

/*
    Obtiene los reclamos de un Arbol en especifico
*/
    public function getRequestsByTree($tree_id)
    {
        // Buscamos la solicitud por su ID
        $request = IncidentRequest::where('tree_id', $tree_id)
        ->with(['user','requestType'])
        ->get();
        
        if($request->isEmpty){
            return response()->json([
                'status'  => 'error',
                'message' => 'No se encontraron solicitudes',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count'  => $request->count(),
            'data'   => $request,
        ], 200);
    }
    
/*
    Muestra todas las solicitudes por estado
*/
    public function getRequestByStatus($status)
    {
        // Valida que el filtro que usan coincida
        if(!in_array($status, ['open', 'in_progress', 'resolved'])){
            return response()->json([
                'status'  => 'error',
                'message' => 'Estado de solicitud inválido',
            ], 400);
        }

        // Buscamos las solicitudes por su estado
        $request = IncidentRequest::where('status', $status)
        ->with(['tree.street','user','requestType'])
        ->get();
        
        // Muestra todas las solicitudes por estado
        return response()->json([
            'status' => 'success',
            'count'  => $request->count(),
            'data'   => $request,
        ], 200);
    }

/*
    Muestra todas las reclamos por tipo de solicitud
*/
    public function getRequestByType($typeId)
    {
        $request = DB::table('requests')
        ->join('request_types', 'requests.request_type_id', '=', 'request_types.id')
        ->join('streets', 'requests.street_id', '=', 'streets.id')
        ->join('users', 'requests.user_id', '=', 'users.id')
        ->where('requests.request_type_id', $typeId)
        ->select([
            'requests.id as request_id',
            'requests.description',
            'requests.status',
            'requests.path',
            'requests.created_at', // esto es para que laravel sepa que fecha mostrar 
            'request_types.task_description as type_name',
            'streets.street_name',
            'users.name as user_name'
        ])
        ->orderBy('requests.created_at', 'desc') // esto los ordena de mayor a menor (del mas reciente al mas antiguo)
        ->get(); 

        if($request->isEmpty()){
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron solicitudes',
            ],404);
        }

        // Muestra todas las solicitudes por tipo de solicitud
        return response()->json([
            'status' => 'success',
            'count'  => $request->count(),
            'data'   => $request,
        ],200);
    }
    
}
