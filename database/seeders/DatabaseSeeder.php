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
        // 1. El Vecino (Usuario Público)
        User::factory()->create([
            'name' => 'Vecino Juan',
            'email' => 'vecino@example.com',
            'role' => 'vecino',
            'password' => bcrypt('vecino123'),
        ]);

        // 2. El Inspector (Trabajador de Arbolado)
        User::factory()->create([
            'name' => 'Inspector Carlos',
            'email' => 'inspector@example.com',
            'role' => 'inspector',
            'password' => bcrypt('inspector123'),
        ]);

        // 3. El Administrador (Personal de IT / Soporte)
        User::factory()->create([
            'name' => 'Admin Soporte IT',
            'email' => 'admin@example.com',
            'role' => 'admin', 
            'password' => bcrypt('admin123'),
        ]);

        $this->call([
            SpecieSeeder::class,
            StreetSeeder::class,
            ParkSeeder::class,
            PlanterSeeder::class,
            TreeSeeder::class,
        ]);
    }
}
