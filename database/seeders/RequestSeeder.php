<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\RequestStatusHistory;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Creamos, por ejemplo, 10 reclamos de prueba usando el Factory
        $reclamos = Request::factory()->count(10)->create();

        // 2. Recorremos cada reclamo recién creado para generarle su "primer paso" en la bitácora
        foreach ($reclamos as $reclamo) {
            RequestStatusHistory::create([
                'request_id'        => $reclamo->id,
                'request_status_id' => $reclamo->request_status_id, // Usamos el mismo estado con el que nació el reclamo
                'user_id'           => 1, // Asumimos que lo inició el vecino (ID 1) o el sistema
                'justification'     => 'Registro inicial del reclamo ingresado por el ciudadano de forma digital.',
            ]);

            // 🔥 OPCIONAL: Si querés que algunos reclamos simulen tener MÁS de un movimiento
            if ($reclamo->request_status_id > 1) {
                RequestStatusHistory::create([
                    'request_id'        => $reclamo->id,
                    'request_status_id' => $reclamo->request_status_id, // Estado actual
                    'user_id'           => 2, // El inspector Carlos (ID 2) hizo el movimiento
                    'justification'     => 'Simulación de actualización realizada por el cuerpo de inspectores de arbolado.',
                ]);
            }
        }
    }
}
