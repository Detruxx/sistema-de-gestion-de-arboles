<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
 
class CompanyController extends Controller
{

    /**
     * Obtiene todas las empresas activas para usar en dropdowns.
     */
    public function getActivateCompanies() :JsonResponse
    {
        $companies = Company::get(['id', 'name']);

        // NOTA: Si en su base de datos ya tienen la columna 'status', podés usar esta línea en su lugar:
        // $companies = Company::where('status', 'activo')->get(['id', 'name']);

        return response()->json($companies, 200);
    }
}
