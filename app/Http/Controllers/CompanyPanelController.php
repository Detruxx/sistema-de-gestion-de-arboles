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
        
        // Obtenemos las órdenes de trabajo de esa empresa
        $workOrders = WorkOrder::with(['request.street'])
                    ->where('company_id', $companyId)
                    ->get(); 

        // Procesamos los trabajos
        $jobs = $workOrders->map(function($order){
            return [
                'id'               => $order->id,
                'task_description' => $order->task_description,
                'work_status'      => $order->work_status,
                'payment_status'   => $order->payment_status ?? 'Pendiente', // Columna de pago (avisale a Orne si no la creó)
                'cost'             => (float) ($order->cost ?? 0),          // Columna de costo
                'execution_order'  => $order->execution_order,
                'scheduled_date'   => $order->scheduled_date ? $order->scheduled_date->format('Y-m-d') : null,
                'created_at'       => $order->created_at ? $order->created_at->format('Y-m-d') : null,
                'request' => [
                    'direccion'   => $order->request && $order->request->street ? $order->request->street->name : 'Sin dirección',
                    'descripcion' => $order->request ? $order->request->description : '', // Traducimos description -> descripcion
                ]
            ];
        }); 

        // Devolvemos los datos en formato JSON 
        return response()->json([
            'jobs' => $jobs,
            // En caso de necesitar las licitaciones, descomentar la siguiente línea:
            // 'tenders' => WorkOrder::whereNull('company_id')->get()
        ],200);
    }
} 
