<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Street;

class StreetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $totalRegistros = 200000; // 100.000 puntos
    $lote = 5000; // Crear de a 5.000 para no saturar la RAM

    for ($i = 0; $i < $totalRegistros; $i += $lote) {
        // En lugar de usar ->create() que hace un INSERT por cada uno, 
        // usamos ->make() para generar la data y luego un insert() masivo
        $datos = \App\Models\Street::factory()->count($lote)->make()->toArray();
        
        \App\Models\Street::insert($datos);
    }
}

}
