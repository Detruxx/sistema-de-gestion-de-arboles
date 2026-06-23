<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Request;

class RequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Pendiente', 'slug' => 'open'],                    // ID 1 (Default)
            ['status_name' => 'Relevado / Inspeccionado', 'slug' => 'relevated'], // ID 2
            ['status_name' => 'Programado', 'slug' => 'scheduled'],              // ID 3
            ['status_name' => 'En curso', 'slug' => 'in_progress'],              // ID 4
            ['status_name' => 'Completado', 'slug' => 'resolved'],               // ID 5
            ['status_name' => 'Denegado', 'slug' => 'denied'],                   // ID 6
            ['status_name' => 'Vinculado (Duplicado)', 'slug' => 'vinculated'],  // ID 7
        ];

        DB::table('request_statuses')->insert($statuses);
    }
}
