<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Request as TreeRequest;
use App\Models\RequestStatus;
use App\Models\RequestStatusHistory;

class WorkOrderController extends Controller
{

    /**
     * Muestra el listado de ordenes de trabajo
     */
    public function index(Request $request)
    {
        //Traemos todas las ordenes de trabajo ordenadas de manera descendente
        $workOrders = WorkOrder::with(['request', 'company'])->orderBy('id', 'desc')->get();

        //Respuesta JSON para el frontend
        return response()->json([
            'status' => 'success',
            'data' => $workOrders,
        ], 200);
    }

    /**
     * Registrar nueva orden de trabajo
     */
    public function store(Request $request)
    {
        // 1. Validamos que la empresa exista y la tarea tenga texto
        $request->validate([
            'request_id'       => 'required|exists:requests,id',
            'company_id'       => 'required|exists:companies,id',
            'task_description' => 'required|string|max:255',
            'execution_order'  => 'required|integer|min:1', //Validamos el orden
            'scheduled_date'   => 'nullable|date', //Puede quedar vacía hasta que la empresa confirme
        ]);

        $currentOrder = $request->execution_order;
        $initialStatus = 'Asignado';

        // Si es un trabajo secuencial posterior (ej: Trabajo 2)
        if ($currentOrder > 1) {
            // Buscamos si la tarea inmediatamente anterior (ej: Trabajo 1) ya fue finalizada
            $previousTaskCompleted = WorkOrder::where('request_id', $request->request_id)
                ->where('execution_order', $currentOrder - 1)
                ->where('work_status', 'Finalizado')
                ->exists();

            // Si la anterior no está lista, esta indefectiblemente va "En espera"
            if (!$previousTaskCompleted) {
                $initialStatus = 'En espera';
            }
        }

        // 2. Creamos la orden de trabajo externa
        $workOrder = WorkOrder::create([
            'request_id'       => $request->request_id,
            'company_id'       => $request->company_id,
            'task_description' => $request->task_description,
            'execution_order'  => $currentOrder,
            'scheduled_date'   => $request->scheduled_date,
            'work_status'      => $initialStatus,
        ]);

        // 3. Opcional: Podrías actualizar el estado del reclamo a "En proceso de reparación" automáticamente aquí
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Orden de trabajo registrada con éxito.',
                'work_order' => $workOrder
            ]);
        }
        
        return redirect()->back()->with('work_assigned', 'Orden de trabajo registrada con éxito bajo el flujo secuencial establecido.');
    }

    /**
     * Actualizar estado de orden de trabajo
     */
    public function updateWorkOrderStatus(Request $request, $id)
    {
        $workOrder = WorkOrder::findOrFail($id);

        // Validamos el estado
        $request->validate([
            'work_status' => 'nullable|in:Asignado,En espera,En Proceso,Finalizado',
            'payment_status' => 'nullable|in:Pendiente,Apto para Cobro,Pagado',
            'scheduled_date' => 'nullable|date'
        ]);
        
        if ($request->has('payment_status')) {
            $workOrder->update(['payment_status' => $request->payment_status]);
            // Si solo enviaron el estado de pago, retornamos éxito
            if (!$request->has('work_status')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Estado de pago actualizado exitosamente.'
                ]);
            }
        }

        if (!$request->has('work_status')) {
            return response()->json(['status' => 'success']);
        }

        $newStatus = $request->work_status;

        // 1. Validar bloqueo secuencial al avanzar
        if (in_array($newStatus, ['Asignado', 'En Proceso', 'Finalizado'])) {
            $previousPending = WorkOrder::where('request_id', $workOrder->request_id)
                ->where('execution_order', '<', $workOrder->execution_order)
                ->where('work_status', '!=', 'Finalizado')
                ->exists();

            if ($previousPending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No puedes avanzar esta tarea porque hay trabajos previos (con orden menor) pendientes de finalizar.'
                ], 400);
            }
        }

        // Guardar la fecha anterior para comparar
        $oldScheduledDate = $workOrder->scheduled_date ? \Carbon\Carbon::parse($workOrder->scheduled_date)->format('Y-m-d') : null;

        // 2. Actualizamos el estado y la fecha de programación
        $updateData = ['work_status' => $newStatus];
        
        if ($newStatus === 'En Proceso') {
            // Si inicia la tarea, la fecha de programación se fija al día de hoy automáticamente
            $updateData['scheduled_date'] = now()->format('Y-m-d');
        } elseif ($request->has('scheduled_date')) {
            // Si solo está guardando la fecha estando Asignado
            $updateData['scheduled_date'] = $request->scheduled_date;
        }

        $workOrder->update($updateData);
        
        // 3. Sincronizamos con el reclamo y la siguiente tarea
        $claim = $workOrder->request;
        if($claim){
            $newRequestStatusSlug = null;
            $justification = '';

            if($newStatus === 'En Proceso'){
                $newRequestStatusSlug = 'in_progress';
                $justification = 'Trabajo en curso iniciado por la contratista.';

            }elseif($newStatus === 'Finalizado'){
                // a. Propagar a la siguiente tarea
                $nextWorkOrder = WorkOrder::where('request_id', $claim->id)
                    ->where('execution_order', '>', $workOrder->execution_order)
                    ->orderBy('execution_order', 'asc')
                    ->first();

                if ($nextWorkOrder && $nextWorkOrder->work_status === 'En espera') {
                    $nextWorkOrder->update(['work_status' => 'Asignado']);
                }

                // b. Verificar si TODAS las tareas están finalizadas
                $allFinished = WorkOrder::where('request_id', $claim->id)
                    ->where('work_status', '!=', 'Finalizado')
                    ->doesntExist();

                if ($allFinished) {
                    $newRequestStatusSlug = 'resolved';
                    $justification = 'Todas las tareas asociadas han sido finalizadas por la(s) contratista(s).';
                }
            }

            // Actualizar el estado global del reclamo si hubo cambio
            if($newRequestStatusSlug){
                $statusObj = RequestStatus::where('slug', $newRequestStatusSlug)->first();
                if($statusObj && $claim->request_status_id !== $statusObj->id){
                    $claim->update([
                        'request_status_id' => $statusObj->id,
                    ]);

                    RequestStatusHistory::create([
                        'request_id' => $claim->id,
                        'request_status_id' => $statusObj->id,
                        'user_id' => auth()->id() ?? 1,
                        'justification' => $justification,
                    ]);
                }
            }

            // Registrar en el historial si la empresa actualizó la fecha programada (sin cambiar de estado general)
            if ($request->has('scheduled_date') && $newStatus === 'Asignado') {
                $newDateStr = \Carbon\Carbon::parse($request->scheduled_date)->format('Y-m-d');
                if ($newDateStr !== $oldScheduledDate) {
                    RequestStatusHistory::create([
                        'request_id' => $claim->id,
                        'request_status_id' => $claim->request_status_id, // Mantiene el estado actual del reclamo
                        'user_id' => auth()->id() ?? 1,
                        'justification' => 'La empresa contratista ha programado la ejecución de esta orden de trabajo para la fecha: ' . \Carbon\Carbon::parse($request->scheduled_date)->format('d/m/Y') . '.',
                    ]);
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Estado de la orden de trabajo actualizado y reclamo sincronizado.'
        ], 200);
    }

    /**
     * Un trabajador de empresa se postula para realizar un trabajo.
     */
    public function applyForTender($id)
    {
        $workOrder = WorkOrder::findOrFail($id);

        if ($workOrder->company_id !== null) {
            return response()->json(['status' => 'error', 'message' => 'Este trabajo ya fue asignado a otra empresa.'], 403);
        }

        $workOrder->update([
            'company_id' => auth()->user()->company_id
        ]);

        return response()->json(['status' => 'success'], 200);
    }
} 
