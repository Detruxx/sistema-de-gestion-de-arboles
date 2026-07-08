<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tree;
use App\Models\Street;
use Faker\Factory as Faker;

class TreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void 
    {
        $faker = Faker::create('es_AR');
        
        // 📍 1. Extraemos todos los IDs de tus calles reales
        $streetIds = Street::pluck('id')->toArray();

        if (empty($streetIds)) {
            $this->command->warn('¡Alerta! No hay calles en la base de datos. Corré primero el StreetSeeder.');
            return;
        }

        $totalRegistros = 5000; 
        $lote = 100; 

        for ($i = 0; $i < $totalRegistros; $i += $lote) {
            // Generamos el set de datos en memoria con el Factory
            $datos = \App\Models\Tree::factory()->count($lote)->make()->toArray();
            
            foreach ($datos as &$dato) {
                // Convertimos el array de vitalidad a JSON si corresponde
                if (isset($dato['vitality'])) {
                    $dato['vitality'] = is_array($dato['vitality']) ? json_encode($dato['vitality']) : $dato['vitality'];
                }

                // 📍 2. Vinculamos lógicamente a una calle de tu tabla 'streets'
                $dato['street_id'] = $faker->randomElement($streetIds);

                // 📍 3. FILTRO GEOGRÁFICO CONTRA EL AGUA (CABA / GBA CONTINENTAL)
                // Acotamos el rango general de latitudes del AMBA
                $latitude = $faker->latitude($min = -34.6600, $max = -34.5500);
                
                // Recortamos la longitud en base a la línea costera del Río de la Plata
                if ($latitude > -34.5800) {
                    // Zona Norte (Palermo, Belgrano, Núñez): El río avanza hacia el Oeste.
                    // Nos movemos al Oeste para garantizar tierra firme.
                    $longitude = $faker->longitude($min = -34.5950, $max = -34.5550);
                } else {
                    // Zona Centro/Sur (Flores, Caballito, Lanús): Hay más masa continental.
                    // Nos extendemos un poco más hacia el Este sin peligro.
                    $longitude = $faker->longitude($min = -34.6700, $max = -34.5850);
                }

                // Sobrescribimos la data aleatoria original con nuestro cuadrante seguro
                $dato['latitude'] = $latitude;
                $dato['longitude'] = $longitude;

                // Forzamos los timestamps obligatorios de Laravel para el insert masivo
                $dato['created_at'] = now();
                $dato['updated_at'] = now();
            }

            // Inserción en bloques de 100 para un rendimiento óptimo
            \App\Models\Tree::insert($datos);
        }
    }
}