<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request;
use App\Models\DataAudit;
use Carbon\Carbon;

class AuditTreeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:audit-tree-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audita la base de datos buscando inconsistencias lógicas en los registros de árboles y trabajos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneYearAgo = Carbon::now()->subYear();
        $count = 0;

        // Inconsistencia 1: Árboles plantados hace menos de 1 año (ID 4 = Plantación)
        $recentPlantations = Request::where('request_type_id', 4)
            ->where('created_at', '>', $oneYearAgo)
            ->whereNotNull('tree_id')
            ->pluck('tree_id');

        // Buscar si esos mismos árboles tienen reclamos de Poda de Altura o similar (ID 2 = Poda) con órdenes de trabajo
        $illogicalPrunings = Request::whereIn('tree_id', $recentPlantations)
            ->where('request_type_id', 2)
            ->with('workOrders')
            ->get();

        foreach ($illogicalPrunings as $pruningReq) {
            if ($pruningReq->workOrders->count() > 0) {
                // Hay una orden de poda para un árbol de menos de 1 año
                // Verificar si ya existe el conflicto
                $exists = DataAudit::where('tree_id', $pruningReq->tree_id)
                    ->where('conflict_type', 'Poda Ilegal (Árbol Joven)')
                    ->where('resolved', false)
                    ->exists();

                if (!$exists) {
                    DataAudit::create([
                        'tree_id' => $pruningReq->tree_id,
                        'conflict_type' => 'Poda Ilegal (Árbol Joven)',
                        'description' => "El árbol ID {$pruningReq->tree_id} tiene una orden de poda de altura asignada, pero fue plantado hace menos de 1 año."
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Auditoría completada. Se detectaron $count nuevas inconsistencias.");
    }
}
