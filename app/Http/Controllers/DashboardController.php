<?php

namespace App\Http\Controllers;

use App\Models\Request as TreeRequest; 
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Devuelve el listado completo de reclamos/árboles formateado para el dashboard.
     */
    public function getTreesList()
    {
        try {
            // Traemos todos los reclamos con sus relaciones optimizadas
            $reclamos = TreeRequest::with(['street', 'status', 'requestType', 'tree.specie'])
                ->latest()
                ->get();

            // Mapeamos los datos de manera ultra segura
            $formattedTrees = $reclamos->map(function ($reclamo) {
                
                return [
                    'id'            => $reclamo->id,
                    'codigo'        => $reclamo->tracking_code, 
                    'descripcion'   => $reclamo->description ?? 'Sin descripción',
                    
                    // 📍 CORREGIDO: Concatenamos calle + altura para que el Front muestre la dirección completa
                    'direccion'     => $reclamo->street 
                        ? $reclamo->street->street_name . ' ' . $reclamo->street->street_number 
                        : 'Sin dirección',
                        
                    'estado_slug'   => $reclamo->status?->slug ?? 'open',
                    'estado_nombre' => $reclamo->status?->status_name ?? 'Pendiente', 
                    
                    // Usamos el fallback por si la columna se llama name, type_name o task_description
                    'categoria'     => $reclamo->requestType?->type_name 
                                       ?? $reclamo->requestType?->name 
                                       ?? $reclamo->requestType?->task_description 
                                       ?? 'General', 
                    
                    'especie'       => $reclamo->tree?->specie?->name ?? 'Sin especificar',
                    'fecha'         => $reclamo->created_at ? $reclamo->created_at->format('d/m/Y') : '',
                ];
            });

            return response()->json([
                'success' => true,
                'trees'   => $formattedTrees
            ], 200);

        } catch (\Exception $e) {
            // Si algo falla, esto avisa la línea exacta y el motivo del error
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor.',
                'debug'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }
}