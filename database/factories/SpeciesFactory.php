<?php

namespace Database\Factories;

use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Species>
 */
class SpeciesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Lista de especies urbanas reales para que los datos sean lógicos
        $speciesList = [
            [
                'scientific_name' => 'Jacaranda mimosifolia',
                'common_name' => 'Jacarandá',
                'family' => 'Bignoniaceae',
                'origin' => 'Nativo',
                'foliage_type' => 'Caduco'
            ],
            [
                'scientific_name' => 'Fraxinus pennsylvanica',
                'common_name' => 'Fresno Americano',
                'family' => 'Oleaceae',
                'origin' => 'Exótico',
                'foliage_type' => 'Caduco'
            ],
            [
                'scientific_name' => 'Handroanthus impetiginosus',
                'common_name' => 'Lapacho Rosado',
                'family' => 'Bignoniaceae',
                'origin' => 'Nativo',
                'foliage_type' => 'Caduco'
            ],
            [
                'scientific_name' => 'Platanus x acerifolia',
                'common_name' => 'Plátano',
                'family' => 'Platanaceae',
                'origin' => 'Exótico',
                'foliage_type' => 'Caduco'
            ],
            [
                'scientific_name' => 'Citrus aurantium',
                'common_name' => 'Naranjo Amargo',
                'family' => 'Rutaceae',
                'origin' => 'Exótico',
                'foliage_type' => 'Perenne'
            ]
        ];

        // Seleccionamos una especie al azar de la lista
        return $this->faker->unique()->randomElement($speciesList);
    }
}
