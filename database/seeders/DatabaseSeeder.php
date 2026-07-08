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
        // 1. Ejecutamos primero todos los catálogos y estructuras base
        $this->call([
            UserStatusSeeder::class,  // Siempre primero para que empresas y usuarios puedan nacer vinculados 
            StreetSeeder::class,      
            ParkSeeder::class,        
            SpecieSeeder::class,
            PrioritySeeder::class,
            CompanySeeder::class,     // Usa el estado por defecto 
            RequestTypeSeeder::class,
            RequestStatusSeeder::class,
            PlanterSeeder::class,
            TreeSeeder::class,
            RequestSeeder::class,
            WorkOrderSeeder::class,   // Al final de todo, cuando ya existen reclamos y empresas
        ]);

        // 2. Ahora que las tablas base existen, creamos los usuarios de prueba con nombre, apellido y rol
        // Todos heredarán automáticamente el 'user_status_id' => 1 por el Factory o la migración 

        // El Vecino (Usuario Público)
        User::factory()->create([
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'vecino@example.com',
            'role' => 'vecino',
            'password' => bcrypt('vecino123'),
            'email_verified_at' => now(),
        ]);

        // El Inspector (Trabajador de Arbolado)
        User::factory()->create([
            'name' => 'Carlos',
            'last_name' => 'Gómez',
            'email' => 'inspector@example.com',
            'role' => 'inspector',
            'password' => bcrypt('inspector123'),
            'email_verified_at' => now(),
        ]);

        // El Usuario de Empresa Tercerizada
        User::factory()->create([
            'name' => 'Mariano',
            'last_name' => 'Rodríguez',
            'email' => 'empresa@example.com',
            'role' => 'empresa',
            'password' => bcrypt('empresa123'),
            'company_id' => 1, // Vinculado a la primera empresa creada en el CompanySeeder
            'email_verified_at' => now(),
        ]);

        // El Administrador (Personal de IT / Soporte)
        User::factory()->create([
            'name' => 'Admin',
            'last_name' => 'Soporte IT',
            'email' => 'admin@example.com',
            'role' => 'admin', 
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
        ]);
    }
}