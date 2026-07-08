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
        UserStatus::updateOrCreate(['id' => 1], [
            'name' => 'Habilitado',
            'slug' => 'active'
        ]);

        UserStatus::updateOrCreate(['id' => 2], [
            'name' => 'Deshabilitado',
            'slug' => 'inactive'
        ]);

        UserStatus::updateOrCreate(['id' => 3], [
            'name' => 'Suspendido',
            'slug' => 'suspended'
        ]);

        UserStatus::updateOrCreate(['id' => 4], [
            'name' => 'Bloqueado',
            'slug' => 'banned'
        ]);
    }
}
