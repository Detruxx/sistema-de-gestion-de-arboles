<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // // 1. Creamos el usuario de prueba que ya venía por defecto

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 2. Ejecutamos los seeders del proyecto en orden de jerarquía
        $this->call([
            // NIVEL 1: Tablas maestras (No dependen de nadie)
            StreetSeeder::class,   // Crea 10 calles (IDs: 1-10)
            ParkSeeder::class,     // Crea 3 plazas (IDs: 1-3)
            SpecieSeeder::class,  // Crea 5 especies reales (IDs: 1-5)

            // NIVEL 2: Tablas intermedias (Dependen de las maestras)
            PlanterSeeder::class,  // Crea 30 planteras asociadas a las calles

            // NIVEL 3: El corazón de la app (Depende de TODAS las anteriores)
            TreeSeeder::class,     // Crea 50 árboles distribuidos en calles, plazas y planteras
        ]);
    }
}
