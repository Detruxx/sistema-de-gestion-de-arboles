<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; 

class RequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Pendiente', 'slug' => 'open', 'sequence' => 1, 'is_terminal' => false, 'color' => '#eab308'],
            ['status_name' => 'Relevado / Inspeccionado', 'slug' => 'relevated', 'sequence' => 2, 'is_terminal' => false, 'color' => '#ea580c'],
            ['status_name' => 'Programado', 'slug' => 'scheduled', 'sequence' => 3, 'is_terminal' => false, 'color' => '#6b21a8'],
            ['status_name' => 'En curso', 'slug' => 'in_progress', 'sequence' => 4, 'is_terminal' => false, 'color' => '#2563eb'],
            ['status_name' => 'Completado', 'slug' => 'resolved', 'sequence' => 5, 'is_terminal' => true, 'color' => '#22c55e'],
            ['status_name' => 'Certificado', 'slug' => 'certified', 'sequence' => 6, 'is_terminal' => true, 'color' => '#15803d'],
            ['status_name' => 'Denegado', 'slug' => 'denied', 'sequence' => null, 'is_terminal' => true, 'color' => '#ef4444'],
            ['status_name' => 'Vinculado (Duplicado)', 'slug' => 'vinculated', 'sequence' => null, 'is_terminal' => true, 'color' => '#d946ef'],
            
            // Nuevos estados
            ['status_name' => 'Cancelado por Vecino', 'slug' => 'cancelled', 'sequence' => null, 'is_terminal' => true, 'color' => '#78909c'],
            ['status_name' => 'Cancelación Solicitada', 'slug' => 'cancel_requested', 'sequence' => null, 'is_terminal' => false, 'color' => '#ff7043'],
        ];

        //Usamos el Facade Schema de Laravel que adapta la desactivación automáticamente según el motor (MySQL o SQLite)
        Schema::disableForeignKeyConstraints();
        
        DB::table('request_statuses')->truncate();
        DB::table('request_statuses')->insert($statuses);
        
        Schema::enableForeignKeyConstraints();
    }
}