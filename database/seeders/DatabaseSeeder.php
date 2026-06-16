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
        // Administrador/Inspector de pruebas
        User::updateOrCreate(
            ['email' => 'administrador@hotmail.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('123'),
            ]
        );

        // Vecino de pruebas
        User::updateOrCreate(
            ['email' => 'vecino@hotmail.com'],
            [
                'name' => 'Vecino Juan',
                'password' => bcrypt('123'),
            ]
        );

        $this->call([
            StreetSeeder::class,      
            ParkSeeder::class,        
            SpeciesSeeder::class,     
            RequestTypeSeeder::class,
            PlanterSeeder::class,
            TreeSeeder::class,
            RequestSeeder::class,
        ]);
    }
}
