<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tree;

class TreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {

        $totalRegistros = 200000; // 200.000 puntos
        $lote = 2000; // Reducido a 2.000 para evitar el límite de "Prepared statement contains too many placeholders" de MySQL (máximo 65,535)

        for ($i = 0; $i < $totalRegistros; $i += $lote) {
            // En lugar de usar ->create() que hace un INSERT por cada uno, 
            // usamos ->make() para generar la data y luego un insert() masivo
            $datos = \App\Models\Tree::factory()->count($lote)->make()->toArray();
            
            // Convertimos a JSON los campos de tipo array uno por uno ya que el seed no lo hace
            foreach ($datos as &$dato) {
                if (isset($dato['vitality'])) {
                    $dato['vitality'] = is_array($dato['vitality']) ? json_encode($dato['vitality']) : $dato['vitality'];
                }
            }

            \App\Models\Tree::insert($datos);
        }
    }

}
