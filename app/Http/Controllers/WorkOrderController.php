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
            'request_id'       => 'required|exists:requests,id',
            'company_id'       => 'required|exists:companies,id',
            'task_description' => 'required|string|max:255',
            'scheduled_date'   => 'nullable|date', // Puede quedar vacía hasta que la empresa confirme
        ]);

        // 2. Creamos la orden de trabajo externa
        WorkOrder::create([
            'request_id'       => $request->request_id,
            'company_id'       => $request->company_id,
            'task_description' => $request->task_description,
            'scheduled_date'   => $request->scheduled_date,
            'work_status'      => 'Asignado',
        ]);

        // 3. Opcional: Podrías actualizar el estado del reclamo a "En proceso de reparación" automáticamente aquí

        return redirect()->back()->with('work_assigned', 'La empresa tercerizada ha sido asignada correctamente a la tarea.');
    }
}
