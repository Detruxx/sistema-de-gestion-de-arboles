<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Request as TreeRequest;
use App\Models\RequestStatusHistory;
use App\Models\RequestStatus;
use App\Models\User;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Traemos los IDs de los estados usando sus slugs para no usar números fijos
        $openStatusId = RequestStatus::where('slug', 'open')->first()->id ?? 1;
        $relevatedStatusId = RequestStatus::where('slug', 'relevated')->first()->id ?? 2;

        // Creamos un usuario vecino temporal para asociar al historial inicial
        $vecinoTemporal = User::factory()->create([
            'name' => 'Vecino',
            'last_name' => 'Digital',
            'role' => 'vecino',
            'email' => 'vecino.temporal@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Creamos un inspector temporal para asociar a los movimientos avanzados
        $inspectorTemporal = User::factory()->create([
            'name' => 'Inspector',
            'last_name' => 'Turno',
            'role' => 'inspector',
            'email' => 'inspector.temporal@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // 1. Creamos 10 reclamos aleatorios utilizando el Factory (el cual ya genera el path JSON vacío o con fotos)
        $reclamos = TreeRequest::factory()->count(10)->create();

        // 1.5 Creamos un caso EXPLICITO de duplicado para probar el algoritmo
        $reclamoMaestro = TreeRequest::factory()->create([
            'street_id' => 1,
            'request_type_id' => 1,
            'request_status_id' => $relevatedStatusId, //Dinámico
            'description' => 'Reclamo Original: Rama gigante a punto de caer',
        ]);

        $reclamoDuplicado = TreeRequest::factory()->create([
            'street_id' => 1,
            'request_type_id' => 1, // Misma calle y mismo tipo de reclamo
            'request_status_id' => $openStatusId, // Dinámico
            'description' => 'Reclamo Duplicado: Vecino reporta la misma rama gigante',
            'suggested_duplicate_id' => $reclamoMaestro->id,
        ]);

        $reclamos->push($reclamoMaestro);
        $reclamos->push($reclamoDuplicado);

        // 2. Recorremos cada reclamo recién creado para generarle su "primer paso" en la bitácora
        foreach ($reclamos as $reclamo) {
            RequestStatusHistory::create([
                'request_id'        => $reclamo->id,
                'request_status_id' => $reclamo->request_status_id,
                'user_id'           => $vecinoTemporal->id,
                'justification'     => 'Registro inicial del reclamo ingresado por el ciudadano de forma digital.',
            ]);

            // Si el reclamo ya avanzó más allá de "open"
            if ($reclamo->request_status_id != $openStatusId) {
                RequestStatusHistory::create([
                    'request_id'        => $reclamo->id,
                    'request_status_id' => $reclamo->request_status_id,
                    'user_id'           => $inspectorTemporal->id,
                    'justification'     => 'Simulación de actualización realizada por el cuerpo de inspectores de arbolado y derivación a contratista.',
                ]);
            }
        }
    }
}