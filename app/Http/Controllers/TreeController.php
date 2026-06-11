<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TreeController extends Controller
{
     public function store(Request $request)
    {
        // 1. Validamos los datos que vienen del formulario
        $validatedData = $request->validate([
            'species_id' => 'required|exists:species,id',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'height'     => 'required|numeric',
            'dap'        => 'required|numeric',
            
            // Validamos que 'vitality' sea un array (opcional)
            'vitality'   => 'nullable|array', 
        ]);

        // 2. Creamos la instancia del Árbol
        $tree = new Tree();
        $tree->species_id = $validatedData['species_id'];
        $tree->latitude   = $validatedData['latitude'];
        $tree->longitude  = $validatedData['longitude'];
        $tree->height     = $validatedData['height'];
        $tree->dap        = $validatedData['dap'];

        // 3. Guardamos el array de vitalidad directamente
        // Laravel, gracias al cast que pusimos en el Modelo, se encarga de transformarlo a JSON para MySQL.
        $tree->vitality = $request->input('vitality'); // Ej: ['semiseco', 'escasa foliacion']

        $tree->save();

        return response()->json(['message' => 'Árbol registrado con éxito', 'tree' => $tree], 201);
    }

    public function getTreesBySpecies($speciesId)
    {
        $trees = Tree::where('species', $speciesId)
        ->with(['street','species','planter'])
        ->get();

        if ($trees->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron arboles con esa especie'
            ], 404);
        }

        return response()->json([
            'status' => 'success', 
            'count' => $trees->count(),
            'data' => $trees
        ], 200);
    }

    //Busca arboles por el NOMBRE de la calle 
    public function getTreesByStreet(Request $request)
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
            'count' => $trees->count(),
            'data'=>  $trees
        ],200);
    }

    public function getTreeDetails($id)
    {
        // Validacion de datos
        $tree = Tree::with(['street', 'species', 'planter'])->find($id);

        // Si no se encuentra el arbol
        if (!$tree) {
            return response()->json([
                'status'=> 'error',
                'message' => 'Arbol no encontrado'
            ], 404);
        }
        // Se devuelve el arbol
        return response()->json([
            'status' => 'success',
            'data' => $tree
        ],200);
    }

    /* Busca arboles que estan en estado critico,
     Por ahora van a estar en espaniol hasta que me digan como se pone*/
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
            'count' => $criticalTrees->count(),
            'data'=>  $criticalTrees
        ],200);
    }

    /* Retorna solo coordenadas y estados para que el mapa cargue rápido 
    y renderice los pines sin ponerse pesado*/

    public function getMapPins()
    {
        // Se selecciona solo las coordenadas y estados de los arboles
        $pins = Tree::select('id','latitude','longitude','vitality','maintenance_status')->get();
        
        // Si no se encuentran pines
        if ($pins->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron pines'
            ], 404);
        }

        // Si se encuentran pines
        return response()->json([
            'status' => 'success',
            'count' => $pins->count(),
            'data'=>  $pins
        ],200);
    }

}
