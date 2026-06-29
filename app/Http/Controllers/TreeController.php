<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tree;
use Illuminate\Support\Facades\DB;

class TreeController extends Controller
{
    // =========================
    // FUNCIONES
    // =========================

    // Guarda un arbol nuevo en la base de datos
    public function store(Request $request)
    {
        // Validamos los datos que vienen del formulario
        $validatedData = $request->validate([
            'species_id' => 'required|exists:species,id',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'height'     => 'required|numeric',
            'dap'        => 'required|numeric',
            
            // Validamos que 'vitality' sea un array (opcional)
            'vitality'   => 'nullable|array', 
        ]);

        // Creamos la instancia del Árbol
        $tree = new Tree();
        $tree->species_id = $validatedData['species_id'];
        $tree->latitude   = $validatedData['latitude'];
        $tree->longitude  = $validatedData['longitude'];
        $tree->height     = $validatedData['height'];
        $tree->dap        = $validatedData['dap'];

        // Guardamos el array de vitalidad directamente
        // Laravel, gracias al cast que pusimos en el Modelo, se encarga de transformarlo a JSON para MySQL.
        $tree->vitality = $request->input('vitality'); // Ej: ['semiseco', 'escasa foliacion']

        $tree->save();

        return response()->json(['message' => 'Árbol registrado con éxito', 'tree' => $tree], 201);
    }

    // Actualiza el estado del arbol
    public function updateStatus(Request $request, $id)
    {
        // Validacion de datos
        $request->validate([
            'vitality'           => 'nullable|array',
            'maintenance_status' => 'nullable|string',
            'structure'          => 'nullable|string',
            'degree'             => 'nullable|integer',
            'observations'       => 'nullable|string',
        ]);

        // Se busca el arbol
        $tree = Tree::find($id);

        // Si no se encuentra el arbol
        if (!$tree) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Arbol no encontrado'
            ], 404);
        }

        // Actualizamos los datos
        $tree->vitality = $request->input('vitality');
        $tree->maintenance_status = $request->input('maintenance_status');
        $tree->structure = $request->input('structure');
        $tree->degree = $request->input('degree');
        $tree->observations = $request->input('observations');

        // Guardamos el arbol
        $tree->save();

        // Devolvemos el arbol
        return response()->json([
            'status'  => 'success',
            'message' => 'Estado del arbol actualizado con exito',
            'data'    => $tree
        ], 200);
    }


    // =================================
    // FUNCIONES DE CONSULTA (GET)
    // =================================

    // Busca arboles por la Especie
    public function getTreesBySpecies($speciesId)
    {
        // Busca arboles por la Especie
        $trees = Tree::where('species_id', $speciesId)
        ->with(['street','species','planter'])
        ->get();

        // Si no se encuentran arboles con esa especie
        if ($trees->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron arboles con esa especie'
            ], 404);
        }
        
        // Si se encuentran arboles con esa especie
        return response()->json([
            'status' => 'success', 
            'count'  => $trees->count(),
            'data'   => $trees
        ], 200);
    }

    
    //Busca arboles por el NOMBRE de la calle 
    public function getTreesByStreetName(Request $request)
    {

        // Validacion de datos
        $request->validate([
            'street' => 'required|string|min:3'
        ]);
        
        $streetName = $request->input('street');

        // Se busca el arbol por el nombre de la calle
        $trees = Tree::whereHas('street', function($query) use ($streetName){
            $query->where('street_name', 'like', '%' . $streetName . '%');
        })->with(['street', 'species', 'planter'])->get();
        
        // Si no se encuentran arboles en la calle
        if ($trees->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron arboles en la calle' . $streetName
            ], 404);
        }
        // Si se encuentran arboles en la calle
        return response()->json([
            'status' => 'success',
            'count'  => $trees->count(),
            'data'   => $trees
        ],200);
    }

    // Busca arboles por la cuadra
    public function getTreesByBlock(Request $request)
    {
        // Validacion de datos
        $request->validate([
            'street' => 'required|string|min:3',
            'street_number' => 'required|integer|min: 0'
        ]);

        // Se busca el arbol por el numero de la calle
        $streetName = $request->input('street'); 
        $baseStreetNumber = intval($request->input('street_number'));

        // Se calcula el rango de numeros de la cuadra
        $baseStreetNumber= intval($request->input('street_number'));
        $start = floor( $baseStreetNumber / 100) * 100;
        $end = $start + 99;

        // Se busca el arbol por el numero de la calle
        $trees = Tree::whereHas('street', function($query) use ($streetName) { 
            $query->where('street_name', 'like', '%' . $streetName . '%');
        })
        ->whereBetween('street_number', [ $start, $end ])
        ->with(['street', 'species', 'planter'])
        ->get();

        // Si no se encuentran arboles en la cuadra
        if ($trees->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron arboles en la cuadra'
            ], 404);
        }
        // Si se encuentran arboles en la cuadra
        return response()->json([
            'status' => 'success',
            'count'  => $trees->count(),
            'data'   => $trees
        ],200);
    }

    // Busqueda por frente exacto de la calle 

    public function getTreesByExactAddress(Request $request)
    {
        // Validacion de datos
        $request->validate([
            'street_id'     => 'required|exists:streets,id',
            'street_number' => 'required|integer|min: 1'
        ]);

       $streetId = $request->input('street_id'); 
       $exactNumber = $request->input('street_number');

       $trees = Tree::where('street_id', $streetId)
       ->whereHas('street_number', $exactNumber)
       ->with(['street','species','planter'])
       ->get(); 
       
       // Si no se encuentra el arbol
       if ($trees->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron arboles con esa calle y numero'
            ], 404);
       }
       // Si se encuentra el arbol
       return response()->json([
            'status' => 'success',
            'count'  => $trees->count(),
            'data'   => $trees
       ],200);
        
    }
    

    // Busca un arbol y devuelve todos sus detalles
    public function getTreeDetails($id)
    {
        // Validacion de datos
        $tree = Tree::with(['street', 'specie', 'planter'])->find($id);

        // Si no se encuentra el arbol
        if (!$tree) {
            return response()->json([
                'status' => 'error',
                'message'=> 'Arbol no encontrado'
            ], 404);
        }
        // Se devuelve el arbol
        return response()->json([
            'status' => 'success',
            'data'   => $tree
        ],200);
    }

    // Busca arboles que estan en estado critico
    public function getCriticalTrees() 
    {

        // Se busca los arboles en estado critico
        $criticalTrees = Tree::whereJsonContains('vitality','Muerto')
        ->orWhere('structure','Inclinado')
        ->orWhere('maintenance_status', 'Urgente')
        ->get();
    
        // Si no se encuentra arboles en estado critico
        if ($criticalTrees->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron arboles en estado critico'
            ], 404);
        }
        // Si se encuentra arboles en estado critico
        return response()->json([
            'status' => 'success',
            'count'  => $criticalTrees->count(),
            'data'   => $criticalTrees
        ],200);
    }

    // Retorna solo coordenadas y estados para que el mapa cargue rápido 
    // y renderice los pines sin ponerse pesado

    public function getMapPins()
    {
        // Usamos Query Builder puro en lugar de Eloquent para evitar construir 200.000 modelos en memoria RAM
        $pins = DB::table('trees')
            ->leftJoin('streets', 'trees.street_id', '=', 'streets.id')
            ->leftJoin('parks', 'trees.park_id', '=', 'parks.id')
            ->leftJoin('species', 'trees.species_id', '=', 'species.id')
            ->select([
                'trees.id',
                'trees.latitude',
                'trees.longitude',
                'trees.height',
                'trees.degree',
                'streets.street_name',
                'streets.street_number',
                'streets.door_plate',
                'parks.park_name',
                'species.common_name as specie_common_name'
            ])
            ->get();

        // Si no se encuentran pines
        if ($pins->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron pines'
            ], 404);
        }

        // Mapeamos los resultados planos a la estructura que espera el frontend (arbol.street, arbol.specie, etc.)
        // Esto sigue siendo infinitamente más liviano que instanciar objetos Eloquent completos
        $formattedPins = $pins->map(function($pin) {
            return [
                'id' => $pin->id,
                'latitude'  => (float)$pin->latitude,
                'longitude' => (float)$pin->longitude,
                'height' => $pin->height,
                'degree' => $pin->degree,
                'street' => $pin->street_name ? [
                    'street_name' => $pin->street_name,
                    'street_number' => $pin->street_number,
                    'door_plate' => $pin->door_plate
                ] : null,
                'park' => $pin->park_name ? [
                    'park_name' => $pin->park_name
                ] : null,
                'specie' => $pin->specie_common_name ? [
                    'common_name' => $pin->specie_common_name
                ] : null
            ];
        });

        return response()->json([
            'status' => 'success',
            'count'  => $formattedPins->count(),
            'data'   => $formattedPins
        ], 200);
    }

    public function getTopSpecies()
    {
        $topTrees = DB::table('trees')
        ->select('species', DB::raw('COUNT(*) as total'))
        ->groupBy('species')
        ->orderBy('total', 'desc')
        ->limit(3)
        ->get();
        
        if($topTrees->isEmpty()){
            return response()->json([
                'status' => 'error',
                'message'=> 'No se encontraron especies'  
            ],404);
        }

        return response()->json([
            'status' => 'success',
            'data'   =>  $topTrees
        ], 200);
    }
}
