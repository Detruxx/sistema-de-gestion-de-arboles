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
            ['status_name' => 'Pendiente', 'slug' => 'open', 'sequence' => 1, 'is_terminal' => false, 'color' => 'primary'],
            ['status_name' => 'Relevado / Inspeccionado', 'slug' => 'relevated', 'sequence' => 2, 'is_terminal' => false, 'color' => 'info'],
            ['status_name' => 'Programado', 'slug' => 'scheduled', 'sequence' => 3, 'is_terminal' => false, 'color' => 'warning'],
            ['status_name' => 'En curso', 'slug' => 'in_progress', 'sequence' => 4, 'is_terminal' => false, 'color' => 'warning'],
            ['status_name' => 'Completado', 'slug' => 'resolved', 'sequence' => 5, 'is_terminal' => true, 'color' => 'success'],
            ['status_name' => 'Denegado', 'slug' => 'denied', 'sequence' => null, 'is_terminal' => true, 'color' => 'danger'],
            ['status_name' => 'Vinculado (Duplicado)', 'slug' => 'vinculated', 'sequence' => null, 'is_terminal' => true, 'color' => 'secondary'],
        ];

        DB::table('request_statuses')->insert($statuses);
    }
}
