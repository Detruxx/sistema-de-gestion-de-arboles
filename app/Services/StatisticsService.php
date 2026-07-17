<?php

namespace App\Services;

use App\Models\Tree;
use App\Models\Request;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StatisticsService
{
    /**
     * Calcula la distribución de un modelo según una columna.
     * Útil para gráficos de torta (Pie/Doughnut charts).
     */
    public function getDistribution($modelClass, $groupByColumn)
    {
        try {
            if ($modelClass === Tree::class && $groupByColumn === 'estado_salud') {
                // Extraemos la vitalidad directamente de la base de datos
                // Como 'vitality' está casteado a array/json, extraemos los registros y contamos en memoria 
                // para evitar problemas de compatibilidad con funciones JSON en diferentes motores SQL
                $trees = Tree::select('vitality')->get();
                
                $counts = [
                    'Saludable' => 0,
                    'Enfermo/Dañado' => 0,
                    'Muerto' => 0,
                    'Desconocido' => 0
                ];

                foreach ($trees as $tree) {
                    $vit = $tree->vitality;
                    
                    // Aseguramos que sea un array
                    if (is_string($vit)) {
                        $vit = json_decode($vit, true);
                    }
                    
                    if (is_array($vit)) {
                        $follaje = strtolower($vit['follaje'] ?? '');
                        $plagas = strtolower($vit['plagas'] ?? '');

                        if ($follaje === 'seco') {
                            $counts['Muerto']++;
                        } elseif ($follaje === 'completo' && ($plagas === 'ninguna' || $plagas === '')) {
                            $counts['Saludable']++;
                        } elseif ($follaje === 'ralo' || ($plagas !== 'ninguna' && $plagas !== '')) {
                            $counts['Enfermo/Dañado']++;
                        } else {
                            $counts['Desconocido']++;
                        }
                    } else {
                        $counts['Desconocido']++;
                    }
                }

                // Filtramos los que tienen 0 para que el gráfico quede más limpio
                // $counts = array_filter($counts, function($val) { return $val > 0; });

                return [
                    'labels' => array_keys($counts),
                    'data' => array_values($counts)
                ];
            }
            
            return ['labels' => [], 'data' => []];
        } catch (\Exception $e) {
            Log::error("Error en getDistribution: " . $e->getMessage());
            // Retorno seguro en caso de error para no romper el front
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Genera datos reales para gráficos de tendencia (líneas temporales).
     */
    public function getTrend($modelClass, $dateColumn, $period = 'month', $limit = 6)
    {
        try {
            if ($modelClass === Request::class) {
                $months = [];
                $data = [];
                
                // Calculamos los reclamos creados mes a mes
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $months[] = ucfirst($date->locale('es')->monthName);
                    
                    $count = Request::whereYear($dateColumn, $date->year)
                                    ->whereMonth($dateColumn, $date->month)
                                    ->count();
                    $data[] = $count;
                }

                return [
                    'labels' => $months,
                    'data' => $data
                ];
            }

            return ['labels' => [], 'data' => []];
        } catch (\Exception $e) {
            Log::error("Error en getTrend: " . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Analiza alertas inteligentes basadas en cruces de datos (Datos reales).
     * Devuelve una lista de insights (Actionable Intelligence).
     */
    public function getSmartAlerts()
    {
        $alerts = [];
        $process_alerts = [];

        try {
            // 1. Alerta de Riesgo Inminente (Reclamos abiertos sobre árboles altos y enfermos)
            $urgentCount = Request::whereHas('status', function($q) {
                    $q->where('is_terminal', false);
                })
                ->whereHas('tree', function($q) {
                    $q->where('height', '>', 10)
                      ->where(function($query) {
                          $query->where('vitality', 'like', '%seco%')
                                ->orWhere('vitality', 'like', '%ralo%')
                                ->orWhere('vitality', 'like', '%cochinilla%');
                      });
                })->count();

            if ($urgentCount > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Riesgo de Caída Inminente',
                    'description' => "Se detectaron {$urgentCount} reclamos pendientes sobre árboles de gran porte (más de 10 metros) reportados en mal estado. Se recomienda intervención de emergencia."
                ];
            } else {
                $alerts[] = [
                    'type' => 'success',
                    'title' => 'Riesgo Controlado',
                    'description' => "No se detectaron árboles de gran porte enfermos con reclamos pendientes de atención."
                ];
            }

            // 2. Alerta Operativa (Promedio de resolución de reclamos finalizados)
            $avgCurrentMonth = Request::whereHas('status', function($q) { $q->where('is_terminal', true); })
                                      ->whereMonth('updated_at', Carbon::now()->month)
                                      ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                                      ->value('avg_days');
                                      
            $avgLastMonth = Request::whereHas('status', function($q) { $q->where('is_terminal', true); })
                                   ->whereMonth('updated_at', Carbon::now()->subMonth()->month)
                                   ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                                   ->value('avg_days');

            if ($avgCurrentMonth !== null && $avgLastMonth !== null && $avgLastMonth > 0) {
                $diff = $avgLastMonth - $avgCurrentMonth;
                $percent = round(($diff / $avgLastMonth) * 100, 1);
                
                if ($percent > 0) {
                    $alerts[] = [
                        'type' => 'success',
                        'title' => 'Mejora Operativa',
                        'description' => "El tiempo promedio de resolución disminuyó un {$percent}% respecto al mes pasado. (Promedio actual: " . round($avgCurrentMonth, 1) . " días)."
                    ];
                } elseif ($percent < 0) {
                    $alerts[] = [
                        'type' => 'warning',
                        'title' => 'Demora Operativa',
                        'description' => "El tiempo de resolución aumentó un " . abs($percent) . "% respecto al mes pasado. Revisar posibles cuellos de botella."
                    ];
                } else {
                    $alerts[] = [
                        'type' => 'info',
                        'title' => 'Rendimiento Estable',
                        'description' => "El tiempo promedio de resolución se mantiene idéntico al del mes pasado."
                    ];
                }
            } else {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Rendimiento Operativo',
                    'description' => "Se requiere más historial de reclamos finalizados para poder calcular una tendencia de rendimiento."
                ];
            }

            // 3. Hotspot de Especies (Especie más problemática combinada con tipo de reclamo)
            $hotspot = DB::table('requests')
                ->join('trees', 'requests.tree_id', '=', 'trees.id')
                ->join('species', 'trees.species_id', '=', 'species.id')
                ->join('request_types', 'requests.request_type_id', '=', 'request_types.id')
                ->select('species.common_name as species_name', 'request_types.task_description as problem', DB::raw('count(*) as total'))
                ->groupBy('species.id', 'species.common_name', 'request_types.id', 'request_types.task_description')
                ->orderByDesc('total')
                ->first();

            if ($hotspot && $hotspot->total > 5) {
                $totalRequests = Request::count();
                $percentage = $totalRequests > 0 ? round(($hotspot->total / $totalRequests) * 100, 1) : 0;
                
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Especies Problemáticas (Hotspot)',
                    'description' => "El {$percentage}% de los reclamos históricos corresponden a '{$hotspot->problem}' en árboles de la especie '{$hotspot->species_name}'. Considerar reemplazo a largo plazo en el censo."
                ];
            } else {
                $alerts[] = [
                    'type' => 'success',
                    'title' => 'Distribución Equilibrada',
                    'description' => "No se detectan especies particulares que concentren un nivel crítico y anormal de reclamos recurrentes."
                ];
            }

            // 4. Alerta de Saturación de Contratistas
            $totalPending = \App\Models\WorkOrder::whereIn('work_status', ['Asignado', 'En Proceso'])->count();
            if ($totalPending > 0) {
                $companiesLoad = \App\Models\WorkOrder::whereIn('work_status', ['Asignado', 'En Proceso'])
                    ->select('company_id', DB::raw('count(*) as total'))
                    ->groupBy('company_id')
                    ->get();
                foreach ($companiesLoad as $load) {
                    $percentage = ($load->total / $totalPending) * 100;
                    if ($percentage > 50) {
                        $company = \App\Models\Company::find($load->company_id);
                        if ($company) {
                            $process_alerts[] = [
                                'type' => 'danger',
                                'title' => 'Saturación de Contratista',
                                'description' => "La empresa '{$company->name}' concentra el " . round($percentage, 1) . "% de las Órdenes pendientes. Se sugiere derivar a otras contratistas para evitar cuellos de botella operativos."
                            ];
                        }
                    }
                }
            }

            // 5. Alerta de Reincidencia Crítica
            $repeatedTrees = DB::table('requests')
                ->whereNotNull('tree_id')
                ->select('tree_id', DB::raw('count(*) as total'))
                ->groupBy('tree_id')
                ->having('total', '>=', 3)
                ->get();
                
            if ($repeatedTrees->count() > 0) {
                $process_alerts[] = [
                    'type' => 'danger',
                    'title' => 'Reincidencia Crítica Detectada',
                    'description' => "Existen {$repeatedTrees->count()} árboles en la comuna que acumulan 3 o más reclamos históricos. Se recomienda suspender mantenimientos temporales y evaluar su extracción definitiva."
                ];
            } else {
                $process_alerts[] = [
                    'type' => 'success',
                    'title' => 'Reincidencia Bajo Control',
                    'description' => "No se detectaron árboles con reiterados reclamos (3 o más). El historial de intervenciones se mantiene saludable."
                ];
            }

        } catch (\Exception $e) {
            Log::error("Error en getSmartAlerts: " . $e->getMessage());
            // Si hay error de base de datos, mostramos una alerta genérica pero no rompemos el front
            $alerts[] = [
                'type' => 'info',
                'title' => 'Cargando inteligencia operativa...',
                'description' => 'Estamos recolectando datos suficientes para generar alertas precisas.'
            ];
        }

        return [
            'alerts' => $alerts,
            'process_alerts' => $process_alerts
        ];
    }

    /**
     * Motor Dinámico para Consultas Personalizadas
     */
    public function buildCustomQuery($model, $metric, $groupBy, $dateRange, $exportType)
    {
        $allowedModels = ['trees', 'requests', 'work_orders'];
        if (!in_array($model, $allowedModels)) {
            throw new \Exception("Modelo no permitido.");
        }

        // Validación de Incompatibilidad
        if ($model === 'trees' && $metric === 'avg_time') {
            throw new \Exception("No se puede calcular el 'Tiempo Promedio' de un Árbol físico. Esta métrica solo está disponible para Reclamos y Órdenes de Trabajo.");
        }

        // 1. Instanciar la Query base según el modelo
        if ($model === 'trees') $query = Tree::query();
        elseif ($model === 'requests') $query = \App\Models\Request::query();
        elseif ($model === 'work_orders') $query = \App\Models\WorkOrder::query();

        // 2. Aplicar filtro de fechas
        $table = $query->getModel()->getTable();
        $query->when($dateRange === '30days', function ($q) use ($table) {
            return $q->where($table.'.created_at', '>=', Carbon::now()->subDays(30));
        })->when($dateRange === 'this_year', function ($q) use ($table) {
            return $q->where($table.'.created_at', '>=', Carbon::now()->startOfYear());
        });

        // 3. Determinar Métrica a usar (Cantidad o Promedio de Días)
        $table = $query->getModel()->getTable();
        $selectMetric = "COUNT({$table}.id) as valor";
        if ($metric === 'avg_time') {
            // Calcula el promedio de días que estuvo abierto. Usa ABS para evitar negativos con datos de prueba incoherentes.
            $selectMetric = "ROUND(AVG(ABS(DATEDIFF({$table}.updated_at, {$table}.created_at))), 1) as valor";
        }

        // 4. Determinar lógicas específicas de Agrupamiento y Joins
        if ($groupBy === 'species') {
            if ($model === 'trees') {
                $query->join('species', 'trees.species_id', '=', 'species.id')
                      ->selectRaw("species.common_name as grupo, {$selectMetric}")
                      ->groupBy('species.common_name');
            } elseif ($model === 'requests') {
                $query->join('trees', 'requests.tree_id', '=', 'trees.id')
                      ->join('species', 'trees.species_id', '=', 'species.id')
                      ->selectRaw("species.common_name as grupo, {$selectMetric}")
                      ->groupBy('species.common_name');
            } else {
                // Work Orders u otros que no tienen especies de forma directa
                $query->selectRaw("'No Aplica' as grupo, {$selectMetric}");
            }
        } 
        elseif ($groupBy === 'status') {
            if ($model === 'trees') {
                $query->selectRaw("maintenance_status as grupo, {$selectMetric}")
                      ->groupBy('maintenance_status');
            } elseif ($model === 'requests') {
                $query->join('request_statuses', 'requests.request_status_id', '=', 'request_statuses.id')
                      ->selectRaw("request_statuses.name as grupo, {$selectMetric}")
                      ->groupBy('request_statuses.name');
            } elseif ($model === 'work_orders') {
                $query->selectRaw("work_status as grupo, {$selectMetric}")
                      ->groupBy('work_status');
            }
        } 
        elseif ($groupBy === 'month') {
            // Compatible con MySQL
            $query->selectRaw("MONTH({$table}.created_at) as mes_num, {$selectMetric}")
                  ->groupBy(DB::raw("MONTH({$table}.created_at)"));
        }
        else {
            // Fallback genérico
            $query->selectRaw("'Total' as grupo, {$selectMetric}");
        }

        // 5. Ejecutar consulta
        $results = $query->get();

        // 6. Post-procesamiento
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $results->transform(function($item) use ($meses, $groupBy, $metric) {
            // Convertir mes numérico a texto
            if ($groupBy === 'month') {
                $item->grupo = $meses[$item->mes_num] ?? 'Desconocido';
                unset($item->mes_num);
            }
            
            // Añadir sufijo "días" si la métrica es tiempo promedio
            if ($metric === 'avg_time') {
                // Si el valor es null (ej. no hay registros cerrados), poner 0
                $val = $item->valor ? $item->valor : '0';
                $item->valor = $val . " días";
            }
            
            return $item;
        });

        return [
            'metadata' => [
                'model' => $model,
                'metric' => $metric,
                'grouped_by' => $groupBy,
                'export_type' => $exportType
            ],
            'results' => $results
        ];
    }
}
