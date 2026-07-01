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

        return view('company.dashboard', compact('orders'));
    }
}
