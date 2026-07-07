<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
 
class CompanyController extends Controller
{

    /**
     * Obtiene todas las empresas activas para usar en dropdowns.
     */
    public function getActiveCompanies() :JsonResponse
    {
        $companies = Company::get(['id', 'name']);

        // NOTA: Si en su base de datos ya tienen la columna 'status', podés usar esta línea en su lugar:
        // $companies = Company::where('status', 'activo')->get(['id', 'name']);

        return response()->json($companies, 200);
    }

    public function indexAdmin() :JsonResponse
    {
        // El admin necesita toda la lista de empresas para verlo en el dashboard
        $companies = Company::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $companies
        ], 200);
    }
}
