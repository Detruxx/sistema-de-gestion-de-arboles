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
            'email'          => 'nullable|email|max:255|unique:companies,email',
            'location'       => 'required|string|max:255' 
        ]);

        try {
            // Forzamos el estado a No Verificada (ID 2) por defecto
            $validatedData['user_status_id'] = 2;

            // Creamos la empresa con los datos ya validados
            $company = Company::create($validatedData);
            
            // Devolvemos una respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'Empresa registrada con éxito. Estado: No verificada.',
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
        // Solo obtener empresas habilitadas (user_status_id = 1)
        $companies = Company::where('user_status_id', 1)->get(['id', 'name']);

        return response()->json($companies, 200);
    }

    public function indexAdmin() :JsonResponse
    {
        // El admin necesita toda la lista de empresas para verlo en el dashboard
        $companies = Company::with(['jobRoles', 'workOrders', 'users', 'users.status', 'status'])->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $companies
        ], 200);
    }

    /**
     * Alterna el estado de una empresa entre Habilitado (1) y Deshabilitado (2)
     */
    public function toggleStatus($id)
    {
        try {
            $company = Company::findOrFail($id);

            // Cambiamos entre 1 (Habilitado) y 2 (Deshabilitado)
            $company->user_status_id = ($company->user_status_id == 1) ? 2 : 1;
            $company->save();

            // Sincronizar el estado de la empresa con todos sus usuarios asociados
            // Si la empresa se desactiva, sus usuarios también. Si se activa, sus usuarios se activan.
            $company->users()->update(['user_status_id' => $company->user_status_id]);

            $newStatus = ($company->user_status_id == 1) ? 'Habilitado/Verificado' : 'Deshabilitado/No verificado';

            return response()->json([
                'status'    => 'success',
                'message'   => "La empresa {$company->name} ahora está {$newStatus}.",
                'user_status_id' => $company->user_status_id
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo alterar el estado de la empresa.',
                'debug'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
