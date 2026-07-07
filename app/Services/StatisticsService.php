<?php

namespace App\Services;

use App\Models\Tree;
use App\Models\Request;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsService
{
    /**
     * Calcula la distribución de un modelo según una columna.
     * Útil para gráficos de torta (Pie/Doughnut charts).
     * Ejemplo: getDistribution(Tree::class, 'estado_salud')
     * 
     * @param string $modelClass
     * @param string $groupByColumn
     * @return array
     */
    public function getDistribution($modelClass, $groupByColumn)
    {
        // ESQUELETO: Retorna datos mockeados simulando la consulta real
        // Consulta real sería: $modelClass::select($groupByColumn, DB::raw('count(*) as total'))->groupBy($groupByColumn)->get();
        if ($modelClass === Tree::class && $groupByColumn === 'estado_salud') {
            return [
                'labels' => ['Saludable', 'Enfermo', 'Dañado'],
                'data' => [1250, 320, 150]
            ];
        }

        return ['labels' => [], 'data' => []];
    }

    /**
     * Genera datos para gráficos de tendencia (líneas temporales).
     * Ejemplo: getTrend(Request::class, 'created_at', 'month', 6)
     * 
     * @param string $modelClass
     * @param string $dateColumn
     * @param string $period (month, week, day)
     * @param int $limit
     * @return array
     */
    public function getTrend($modelClass, $dateColumn, $period = 'month', $limit = 6)
    {
        // ESQUELETO: Retorna datos mockeados para los últimos 6 meses
        // Consulta real agruparía por YEAR() y MONTH()
        if ($modelClass === Request::class) {
            $months = [];
            for ($i = $limit - 1; $i >= 0; $i--) {
                $months[] = ucfirst(Carbon::now()->subMonths($i)->locale('es')->monthName);
            }

            return [
                'labels' => $months,
                'data' => [45, 60, 55, 80, 120, 95] // Mock de cantidad de reclamos
            ];
        }

        return ['labels' => [], 'data' => []];
    }

    /**
     * Analiza alertas inteligentes basadas en cruces de datos.
     * Devuelve una lista de insights (Actionable Intelligence).
     * 
     * @return array
     */
    public function getSmartAlerts()
    {
        // ESQUELETO: Funciones reales cruzarían distintas tablas
        $alerts = [];

        // 1. Alerta de Riesgo Inminente (Mock: Árboles enfermos > 10m altura)
        $alerts[] = [
            'type' => 'danger',
            'title' => 'Riesgo de Caída Inminente',
            'description' => 'Hay 15 árboles de gran porte (más de 10 metros) reportados como "Enfermos" o "Dañados". Se recomienda intervención urgente.'
        ];

        // 2. Alerta Operativa (Mock: Tiempo de resolución)
        $alerts[] = [
            'type' => 'success',
            'title' => 'Mejora Operativa',
            'description' => 'El tiempo promedio de resolución de reclamos disminuyó un 12% respecto al mes pasado.'
        ];

        // 3. Hotspot de Especies (Mock: Especies problemáticas)
        $alerts[] = [
            'type' => 'warning',
            'title' => 'Especies Problemáticas',
            'description' => 'El 42% de los reclamos por rotura de veredas están asociados a la especie "Ficus". Considerar reemplazo a largo plazo.'
        ];

        return $alerts;
    }

    /**
     * Motor Dinámico para Consultas Personalizadas (Esqueleto)
     * Interpreta los parámetros enviados desde el frontend y construye la query.
     */
    public function buildCustomQuery($model, $metric, $groupBy, $dateRange, $exportType)
    {
        // 1. Validar inputs con un Whitelist de seguridad (para prevenir SQL injection o errores lógicos)
        $allowedModels = ['trees', 'requests', 'work_orders'];
        if (!in_array($model, $allowedModels)) {
            throw new \Exception("Modelo no permitido.");
        }

        // ESQUELETO: Aquí iría la lógica Eloquent real.
        // Ejemplo de lo que se ejecutaría:
        // $query = Request::query();
        // if ($dateRange === '30days') $query->where('created_at', '>=', now()->subDays(30));
        // $query->select($groupBy, DB::raw("COUNT(*) as total"))->groupBy($groupBy);
        // return $query->get();

        // Datos mockeados de respuesta exitosa
        return [
            'metadata' => [
                'model' => $model,
                'metric' => $metric,
                'grouped_by' => $groupBy,
                'export_type' => $exportType
            ],
            'results' => [
                ['grupo' => 'Activo', 'valor' => 150],
                ['grupo' => 'Pendiente', 'valor' => 45],
                ['grupo' => 'Finalizado', 'valor' => 300]
            ]
        ];
    }
}
