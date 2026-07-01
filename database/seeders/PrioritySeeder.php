<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        foreach(['Baja', 'Media', 'Alta', 'Urgente'] as $p) {
            Priority::create(['priority_name' => $p]);
        }
    }
}
