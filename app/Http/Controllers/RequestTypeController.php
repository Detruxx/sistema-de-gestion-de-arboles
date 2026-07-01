<?php

namespace App\Http\Controllers;

use App\Models\RequestType;
use Illuminate\Http\JsonResponse;

class RequestTypeController extends Controller
{
    // Traer todos los tipos de reclamo
    public function index(): JsonResponse
    {
        return response()->json(RequestType::all());
    }
}
