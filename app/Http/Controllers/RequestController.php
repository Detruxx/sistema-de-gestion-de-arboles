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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Traemos los 7 tipos de reclamo de la base de datos
        $tiposDeReclamo = RequestType::all();

        // 2. Traemos las calles para que el vecino también elija dónde es el problema
        $calles = Street::all();

        // 3. Cargamos la vista "create" y le enviamos las dos variables
        return view('requests.create', compact('tiposDeReclamo', 'calles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Aquí llegará el reclamo cuando el usuario haga clic en "Enviar"
        // Por ahora, solo validamos y guardamos (luego lo expandiremos)
        $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'street_id' => 'required|exists:streets,id',
            'description' => 'required|string|min:10',
        ]);

        // Guardar en la base de datos (Ejemplo rápido)
        \App\Models\Request::create([
            'user_id' => 1, // Simulamos el usuario logueado por ahora
            'request_type_id' => $request->request_type_id,
            'street_id' => $request->street_id,
            'description' => $request->description,
            'path' => 'fotos/default.jpg',
            'request_status_id' => 1,

        ]);

        return redirect()->back()->with('success', '¡Su reclamo ha sido registrado con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    //FUNCION PARA ACTUALIZAR LA JUSTIFICACION DE UN ESTADO
    public function updateStatus(Request $request, $id) 
    {
    // Supongamos que recibimos del formulario: 'status_id' y 'justification'
    $newStatusId = $request->input('status_id');
    $justification = $request->input('justification');
    $user = auth()->user(); // El inspector logueado

    $treeRequest = \App\Models\Request::findOrFail($id);

    // DB Transaction asegura que si algo falla, no se guarde nada a medias
    \DB::transaction(function () use ($treeRequest, $newStatusId, $user, $justification) {
        
        // 1. Actualizamos el estado actual en el reclamo principal
        $treeRequest->update([
            'request_status_id' => $newStatusId
        ]);

        // 2. Creamos una nueva página en el libro de historial (sin borrar nada anterior)
        $treeRequest->histories()->create([
            'request_status_id' => $newStatusId,
            'user_id' => $user->id,
            'justification' => $justification
        ]);
    });

    return redirect()->back()->with('success', 'El estado del reclamo ha sido actualizado con éxito.');
}
}
