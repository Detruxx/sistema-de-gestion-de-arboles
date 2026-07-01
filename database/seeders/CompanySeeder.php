<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        foreach(['Mantenimiento Verde S.A.', 'Veredas del Plata', 'Logística Urbana Porteña'] as $c) {
            Company::create(['company_name' => $c]);
        }
    }
}
