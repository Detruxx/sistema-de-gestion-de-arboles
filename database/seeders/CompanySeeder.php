<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\CompanyRole; // 📍 Importamos el nuevo modelo

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Empresa de prueba fija (ID 1)
        $company1 = Company::create([
            'id'            => 1,
            'name'          => 'Arboricultura BA',
            'business_name' => 'Arboricultura Buenos Aires S.A.',
            'cuit'          => '30-12345678-9',
            'email'         => 'contacto@arboriculturaba.com.ar',
            'location'      => 'Comuna 3, CABA',
        ]);

        // 📍 Asignamos los roles usando el modelo CompanyRole
        CompanyRole::create(['company_id' => $company1->id, 'job_role' => 'Poda Integral']);
        CompanyRole::create(['company_id' => $company1->id, 'job_role' => 'Extracción y Destoconado']);


        // 2. Segunda empresa de ejemplo (ID 2)
        $company2 = Company::create([
            'id'            => 2,
            'name'          => 'Verde Urbano Mantenimiento',
            'business_name' => 'Verde Urbano SRL',
            'cuit'          => '30-98765432-1',
            'email'         => 'licitaciones@verdeurbano.com',
            'location'      => 'Comuna 12 y 14, CABA',
        ]);

        CompanyRole::create(['company_id' => $company2->id, 'job_role' => 'Tratamiento Fitosanitario']);
    }
}
