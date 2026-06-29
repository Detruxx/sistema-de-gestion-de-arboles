<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Request as TreeRequest;

class WorkOrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validamos que la empresa exista y la tarea tenga texto
        $request->validate([
            'request_id'      => 'required|exists:requests,id',
            'company_id'      => 'required|exists:companies,id',
            'task_description'=> 'required|string|max:255',
            'execution_order' => 'required|integer|min:1', //Validamos el orden
            'scheduled_date'  => 'nullable|date', //Puede quedar vacía hasta que la empresa confirme
        ]);

        $ordenActual = $request->execution_order;
        $statusInicial = 'Asignado';

        // 💡 Si es un trabajo secuencial posterior (ej: Trabajo 2)
        if ($ordenActual > 1) {
            // Buscamos si la tarea inmediatamente anterior (ej: Trabajo 1) ya fue finalizada
            $tareaAnteriorCompletada = WorkOrder::where('request_id', $request->request_id)
                ->where('execution_order', $ordenActual - 1)
                ->where('work_status', 'Finalizado')
                ->exists();

            // Si la anterior no está lista, esta indefectiblemente va "En espera"
            if (!$tareaAnteriorCompletada) {
                $statusInicial = 'En espera';
            }
        }

        // 2. Creamos la orden de trabajo externa
        WorkOrder::create([
            'request_id'       => $request->request_id,
            'company_id'       => $request->company_id,
            'task_description' => $request->task_description,
            'execution_order'  => $ordenActual,
            'scheduled_date'   => $request->scheduled_date,
            'work_status'      => $statusInicial,
        ]);

        // 3. Opcional: Podrías actualizar el estado del reclamo a "En proceso de reparación" automáticamente aquí
        return redirect()->back()->with('work_assigned', 'Orden de trabajo registrada con éxito bajo el flujo secuencial establecido.');
    }
}
