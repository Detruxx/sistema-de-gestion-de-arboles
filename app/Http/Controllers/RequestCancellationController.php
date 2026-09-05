<?php

namespace App\Http\Controllers;

use App\Models\Request as TreeRequest;
use App\Models\RequestStatus; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestCancellationController extends Controller
{
    /**
     * Procesa la solicitud de cancelación de un reclamo por parte del vecino.
     */
    public function __invoke(Request $request, $id)
    {
        // 1. Validar que el motivo de cancelación venga en la petición
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ], [
            'cancellation_reason.required' => 'El motivo de cancelación es obligatorio.'
        ]);

        try {
            // Buscar el reclamo con su estado actual cargado
            $reclamo = TreeRequest::with('status')->findOrFail($id);

            // 2. Control de Seguridad: Verificar que el reclamo pertenezca al usuario logueado
            if ($reclamo->user_id !== Auth::id()) { // O la columna correspondiente a la FK del vecino
                return response()->json([
                    'success' => false,
                    'message' => 'No tenés permisos para realizar esta acción sobre este reclamo.'
                ], 403);
            }

            // Obtener el slug del estado actual
            $estadoActual = $reclamo->status?->slug ?? 'open';

            // 3. Evaluar los estados terminales (No se puede cancelar algo cerrado o resuelto)
            $estadosTerminales = ['resolved', 'certified', 'denied', 'vinculated', 'cancelled'];
            if (in_array($estadoActual, $estadosTerminales)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar un reclamo que ya se encuentra cerrado o finalizado.'
                ], 422);
            }

            // Iniciamos una transacción para asegurarnos de que se guarde el estado y el historial juntos
            DB::beginTransaction();

            $nuevoEstadoSlug = '';
            $mensajeHistorial = '';

            // 4. Lógica de negocio según el estado actual del reclamo
            if ($estadoActual === 'open') {
                // Si está Pendiente (open) -> Se cancela automáticamente
                $nuevoEstadoSlug = 'cancelled';
                $mensajeHistorial = 'Cancelado por el vecino';
            } elseif (in_array($estadoActual, ['relevated', 'scheduled', 'in_progress'])) {
                // Si ya avanzó en el flujo técnico -> Se requiere aprobación de un inspector
                $nuevoEstadoSlug = 'cancel_requested';
                $mensajeHistorial = 'El vecino solicitó la cancelación';
            }

            // Buscar el registro del nuevo estado en la BDD para obtener su ID real
            $nuevoEstado = RequestStatus::where('slug', $nuevoEstadoSlug)->first();

            if (!$nuevoEstado) {
                throw new \Exception("El estado con slug '{$nuevoEstadoSlug}' no existe en la base de datos.");
            }

            // 5. Actualizar el Reclamo
            $reclamo->update([
                'request_status_id'   => $nuevoEstado->id,
                'cancellation_reason' => $request->input('cancellation_reason')
            ]);

            // 6. Registrar en el Historial de Estados
            // Ajustá 'histories' al nombre exacto de la relación en tu modelo Request
            $reclamo->histories()->create([
                'request_status_id' => $nuevoEstado->id,
                'user_id'           => Auth::id(),
                'justification'     => $mensajeHistorial
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $nuevoEstadoSlug === 'cancelled' 
                    ? 'El reclamo ha sido cancelado con éxito.' 
                    : 'Se ha enviado la solicitud de cancelación para revisión del inspector.',
                'status'  => $nuevoEstadoSlug
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la cancelación.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
