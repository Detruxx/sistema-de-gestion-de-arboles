<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Request;
<<<<<<< HEAD
=======
use App\Models\RequestStatusHistory;
>>>>>>> 7df9417fa65fa2849e01939f57c5d7913c14c79a

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
<<<<<<< HEAD
        Request::factory()->count(20)->create();
=======
        // 1. Creamos 10 reclamos aleatorios
        $reclamos = Request::factory()->count(10)->create();

        // 1.5 Creamos un caso EXPLICITO de duplicado para probar el algoritmo
        $reclamoMaestro = Request::factory()->create([
            'street_id' => 1,
            'request_type_id' => 1,
            'request_status_id' => 2, // Relevado
            'description' => 'Reclamo Original: Rama gigante a punto de caer'
        ]);

        $reclamoDuplicado = Request::factory()->create([
            'street_id' => 1,
            'request_type_id' => 1, // Misma calle y mismo tipo de reclamo
            'request_status_id' => 1, // Nuevo reclamo pendiente
            'description' => 'Reclamo Duplicado: Vecino reporta la misma rama gigante',
            'suggested_duplicate_id' => $reclamoMaestro->id
        ]);

        $reclamos->push($reclamoMaestro);
        $reclamos->push($reclamoDuplicado);

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
>>>>>>> 7df9417fa65fa2849e01939f57c5d7913c14c79a
    }
}
