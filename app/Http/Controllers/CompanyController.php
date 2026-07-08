<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
 
class CompanyController extends Controller
{

    /**
     * Registra una nueva empresa en la base de datos.
     * Recibe los datos, los valida y si está todo bien, crea la empresa.
     */
    public function store(Request $request)
    {
        // Validamos que los datos recibidos tengan el formato correcto
        $validatedData = $request->validate([
            'name'           => 'required|string|max:255|unique:companies,name',
            'business_name'  => 'required|string|max:255', 
            'cuit'           => 'required|string|max:20|unique:companies,cuit', 
            'email'          => 'nullable|email|max:255',
            'location'       => 'required|string|max:255' 
        ]);

        try {
            // Creamos la empresa con los datos ya validados
            $company = Company::create($validatedData);
            
            // Devolvemos una respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'Empresa registrada con éxito.',
                'company' => $company 
            ], 201);

        } catch (\Exception $e) { 
            // Si ocurre algún error, atrapamos la excepción y avisamos
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo registrar la empresa.',
                'debug'   => $e->getMessage()
            ], 500);
        }
    }

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
