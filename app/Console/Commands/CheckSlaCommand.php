<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckSlaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica reclamos con más de 15 días de inactividad (estancados) y marca urgente_sla = true';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limitDate = Carbon::now()->subDays(15);
        $count = 0;

        // Buscar reclamos no terminales
        $requests = Request::whereHas('status', function($q) {
            $q->where('is_terminal', false);
        })->get();

        foreach ($requests as $req) {
            // Buscar el último movimiento en el historial
            $lastHistory = $req->histories()->orderBy('created_at', 'desc')->first();
            
            // Si el último movimiento fue hace más de 15 días, o si no tiene historial y su fecha de creación es de hace más de 15 días
            $compareDate = $lastHistory ? $lastHistory->created_at : $req->created_at;

            if ($compareDate < $limitDate) {
                if (!$req->urgente_sla) {
                    $req->urgente_sla = true;
                    $req->save();
                    $count++;
                }
            } else {
                // Si el reclamo volvió a tener movimiento, quitarle la urgencia
                if ($req->urgente_sla) {
                    $req->urgente_sla = false;
                    $req->save();
                }
            }
        }

        $this->info("Verificación SLA completada. $count reclamos marcados como urgentes.");
    }
}
