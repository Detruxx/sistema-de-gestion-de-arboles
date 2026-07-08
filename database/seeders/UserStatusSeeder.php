<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserStatus;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserStatus::create([
            'id' => 1,
            'name' => 'Habilitado',
            'slug' => 'active'
        ]);

        UserStatus::create([
            'id' => 2,
            'name' => 'Deshabilitado',
            'slug' => 'inactive'
        ]);
    }
}
