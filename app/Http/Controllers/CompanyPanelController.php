<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;

class CompanyPanelController extends Controller
{
    
    public function index()
    {
        // Conseguimos el ID de la empresa del usuario logueado
        $companyId = Auth::user()->company_id;

        if (!$companyId) {
            abort(403, 'Acceso denegado: No tienes una empresa asignada.');
        }

        // Traemos las órdenes de trabajo de esa empresa que NO estén "En espera"
        $orders = WorkOrder::with('request')
            ->where('company_id', $companyId)
            ->where('work_status', '!=', 'En espera')
            ->orderBy('execution_order', 'asc')
            ->get();

        return view('dashboards.empresa', compact('orders'));
    }

    /**
     * Obtiene los datos para el dashboard
     */
    public function getDashboardData()
    {
        // Obtenemos el ID de la empresa
        $companyId = auth()->user()->company_id;    
        
        if (!$companyId) {
            abort(403, 'Acceso denegado: No tienes una empresa asignada.');
        }

        // Obtenemos las órdenes de trabajo de esa empresa
        $workOrders = WorkOrder::with([
            'request.street',
            'request.requestType',
            'request.user',
            'request.tree.specie',
            'request.status'])

            ->where('company_id', $companyId)
            ->get(); 

        // Procesamos los trabajos
        $jobs = $workOrders->map(function($order){
            return [
                'id'               => $order->id,
                'task_description' => $order->task_description,
                'work_status'      => $order->work_status,
                'payment_status'   => $order->payment_status ?? 'Pendiente', 
                'cost'             => (float) ($order->cost ?? 0),          
                'execution_order'  => $order->execution_order,
                'scheduled_date'   => $order->scheduled_date ? $order->scheduled_date->format('Y-m-d') : null,
                'created_at'       => $order->created_at ? $order->created_at->format('Y-m-d') : null,
                'request' => [
                    'id'          => $order->request ? $order->request->tracking_code : 'REC-N/A',
                    'db_id'       => $order->request ? $order->request->id : null,
                    'direccion'   => $order->request && $order->request->street ? $order->request->street->street_name . ' ' . $order->request->street->street_number : 'Sin dirección',
                    'descripcion' => $order->request ? $order->request->description : '', 
                    'estado'      => $order->request && $order->request->status ? $order->request->status->slug : 'open',
                    
                    'categoria'   => $order->request && $order->request->requestType ? $order->request->requestType->task_description : 'Poda/Extracción',
  
                    'fecha'       => $order->request && $order->request->created_at ? $order->request->created_at->format('d/m/Y') : '',
                    'vecino'      => $order->request && $order->request->user ? $order->request->user->name : 'Vecino Anónimo',
                    'email'       => $order->request && $order->request->user ? $order->request->user->email : '',
                    'especie'     => $order->request && $order->request->tree && $order->request->tree->specie ? $order->request->tree->specie->name : 'Sin especificar',
                ]
            ];
        }); 

        // Devolvemos los datos en formato JSON 
        return response()->json([
            'jobs' => $jobs,
        ],200);
    }
} 