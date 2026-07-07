<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Request as TreeRequest;

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
            'work_status' => 'required|in:Asignado,En espera,En Proceso,Finalizado'
        ]);
        
        // Actualizamos el estado
        $workOrder->update([
            'work_status' => $request->work_status
        ]);
        
        // Sincronizamos con el reclamo
        $claim = $workOrder->request;
        if($claim){
            $newRequestStatusSlug = null;
            $justification = '';

            // Cambiamos según el estado
            if($request->work_status === 'En Proceso'){
                $newRequestStatusSlug = 'in_progress';
                $justification = 'Trabajo en curso iniciado por la contratista.';

            // Si finaliza
            }elseif($request->work_status === 'Finalizado'){
                $newRequestStatusSlug = 'resolved';
                $justification = 'Trabajo finalizado por la contratista.';
            }

            // Verificamos que se haya establecido un nuevo estado
            if($newRequestStatusSlug){
                $statusObj = RequestStatus::where('slug', $newRequestStatusSlug)->first();
                if($statusObj){
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
        }

        return response->json([
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
