<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        
        // 1. Desactivar llaves foráneas y limpiar la tabla para evitar duplicados al re-vincular
        Schema::disableForeignKeyConstraints();
        Priority::truncate();

        // 2. Definimos las prioridades originales junto a las nuevas que pide el Front
        $priorities = [
            ['priority_name' => 'Baja', 'slug' => 'low'],
            ['priority_name' => 'Media', 'slug' => 'medium'],
            ['priority_name' => 'Alta', 'slug' => 'high'],
            ['priority_name' => 'Urgente', 'slug' => 'urgent'],
            ['priority_name' => 'Auto Media (Sistema)', 'slug' => 'auto-media'],
            ['priority_name' => 'Auto Alta (Sistema)', 'slug' => 'auto-alta'],
        ];

        // 3. Las creamos usando Eloquent tal como te gusta a vos
        foreach ($priorities as $p) {
            Priority::create($p);
        }

        // 4. Volver a activar las restricciones
        Schema::enableForeignKeyConstraints();
    }
}
