<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Request_type;

class RequestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['task_description' => 'Corte de raíz (vereda)'],
            ['task_description' => 'Poda'],
            ['task_description' => 'Extracción'],
            ['task_description' => 'Plantación'],
            ['task_description' => 'Retiro de ramas'],
            ['task_description' => 'Problemas por intervención'],
            ['task_description' => 'Otros'],
        ];

        DB::table('request_types')->insert($types);
    }
}
