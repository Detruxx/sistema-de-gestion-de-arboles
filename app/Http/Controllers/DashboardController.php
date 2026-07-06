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

            // Mapeamos los datos de manera ultra segura usando operadores null-safe (?->)
            $formattedTrees = $reclamos->map(function ($reclamo) {
                
                return [
                    'id'            => $reclamo->id,
                    'codigo'        => $reclamo->tracking_code, // 📍 Corregido para usar la variable interna
                    'descripcion'   => $reclamo->description ?? 'Sin descripción',
                    'direccion'     => $reclamo->street?->street_name ?? 'Sin dirección',
                    'estado_slug'   => $reclamo->status?->slug ?? 'open',
                    'estado_nombre' => $reclamo->status?->status_name ?? 'Pendiente', 
                    
                    // 📍 Usamos un fallback por si la columna se llama name, type_name o task_description
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
            // Si algo falla, esto te va a decir EXACTAMENTE qué línea y qué pasó en lugar de dar un error genérico
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor.',
                'debug'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }
}