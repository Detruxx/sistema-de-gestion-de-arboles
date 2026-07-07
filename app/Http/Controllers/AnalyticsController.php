<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use App\Models\Tree;
use App\Models\Request as Reclamo;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $statsService;

    public function __construct(StatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Endpoint unificado para obtener todas las estadísticas del panel Admin.
     * Retorna toda la información procesada en un solo JSON.
     */
    public function getDashboardAnalytics()
    {
        // 1. Distribución de salud de los árboles (Para Pie Chart)
        $treeHealthDistribution = $this->statsService->getDistribution(Tree::class, 'estado_salud');

        // 2. Tendencia de reclamos en los últimos 6 meses (Para Line Chart)
        $requestsTrend = $this->statsService->getTrend(Reclamo::class, 'created_at', 'month', 6);

        // 3. Alertas Inteligentes (Actionable Intelligence)
        $smartAlerts = $this->statsService->getSmartAlerts();

        return response()->json([
            'status' => 'success',
            'data' => [
                'charts' => [
                    'tree_health' => $treeHealthDistribution,
                    'requests_trend' => $requestsTrend,
                ],
                'alerts' => $smartAlerts
            ]
        ], 200);
    }

    /**
     * Endpoint para generar reportes dinámicos (Consultas Manuales)
     */
    public function generateCustomReport(Request $request)
    {
        try {
            $data = $request->validate([
                'model' => 'required|string',
                'metric' => 'required|string',
                'groupBy' => 'required|string',
                'dateRange' => 'required|string',
                'exportType' => 'required|string'
            ]);

            $results = $this->statsService->buildCustomQuery(
                $data['model'], 
                $data['metric'], 
                $data['groupBy'], 
                $data['dateRange'],
                $data['exportType']
            );

            return response()->json([
                'status' => 'success',
                'data' => $results
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al generar la consulta: ' . $e->getMessage()
            ], 500);
        }
    }
}
