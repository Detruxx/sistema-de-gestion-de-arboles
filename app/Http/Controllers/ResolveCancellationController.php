<?php

namespace App\Http\Controllers;
use App\Models\Request as TreeRequest;
use App\Models\RequestStatus;
use Illuminate\Http\Request;

class ResolveCancellationController extends Controller
{
    /**
     * Resuelve la solicitud de cancelación de un reclamo.
     */
    public function __invoke(Request $request, $id) 
    {
        $request->validate([
            'decision' => 'required|in:rechazar,confirmar'
        ]);

        $claim = TreeRequest::findOrFail($id);

        // Se verifica que el reclamo tenga una solicitud de cancelación activa
        if(!$claim->status || $claim->status->slug !== 'cancel_requested'){

            return reponse()->json([
                'status' => 'error',
                'message' => 'Operación no permitida. El reclamo no tiene una solicitud de cancelación activa.'
            ],422);
        }
        // Si se acepta la cancelación
        if($request->decision === 'aceptar'){ 
            // Buscamos el estado de 'cancelado'
            $cancelledStatus = RequestStatus::where('slug','cancelled')->firstOrFail();

            // Actualizamos el estado del reclamo a 'cancelado'
            $claim->update([
                'request_status_id' => $cancelledStatus->id
            ]);

            // Registramos el cambio en la bitácora
            RequestStatusHistory::create([
                'request_id' => $claim->id,
                'request_status_id' => $cancelledStatus->id,
                'user_id' => auth()->id(), 
                'justification' => 'Solicitud de cancelación confirmada por el inspector. Reclamo cancelado.',
            ]);

            // Se retorna la respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'Reclamo cancelado exitosamente.'
             ],200);
        } 


        // Si se rechaza la cancelación
        if($request->decision === 'rechazar'){
            // Se busca el penúltimo estado (el que tenía antes de que se solicitara la cancelación)
            $penultimateStatus = RequestStatusHistory::where('request_id', $claim->id)
            ->latest()
            ->skip(1)
            ->first();  

            // Si no se encuentra el penúltimo estado, se retorna un error
            if(!$penultimateStatus) {  
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se pudo determinar el estado anterior para restaurar el reclamo.'
                ],422);
            }

            // Actualizamos el estado del reclamo al penúltimo estado
            $claim->update([
                'request_status_id' => $penultimateStatus->request_status_id
            ]);

            // Registramos el cambio en la bitácora
            RequestStatusHistory::create([
                'request_id' => $claim->id,
                'request_status_id' => $penultimateStatus->request_status_id,
                'user_id' => auth()->id(),
                'justification' => 'Solicitud de cancelación rechazada por el inspector'
            ]); 

            // Se retorna la respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'La solicitud de cancelación fue rechazada. El reclamo volvió a su estado anterior.'
            ],200);
        }
    }
}
