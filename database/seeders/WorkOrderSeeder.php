<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkOrder;
use App\Models\Request as TreeRequest; // Le ponemos alias para que no se confunda con el Request de HTTP

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Caso explícito asignado a la Empresa de pruebas (ID 1) para poder testear al loguearte
        // Buscamos el primer reclamo que tengamos disponible
        $unReclamo = TreeRequest::first();

        if ($unReclamo) {
            WorkOrder::create([
                'request_id'       => $unReclamo->id,
                'company_id'       => 1, // ID de la empresa de Mariano
                'task_description' => '🔴 Tarea Crítica: Corte de raíces que levantan la vereda en zona escolar.',
                'scheduled_date'   => now()->addDays(2)->format('Y-m-d'),
                'execution_order'  => 1,
                'work_status'      => 'Asignado',
            ]);
        }

        // 2. Creamos 5 órdenes de trabajo más completamente aleatorias
        WorkOrder::factory()->count(5)->create();
    }
}
