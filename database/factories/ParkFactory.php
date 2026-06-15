<?php

namespace Database\Factories;

use App\Models\Park;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Park>
 */
class ParkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Generamos nombres típicos de plazas/parques
            'park_name' => $this->faker->randomElement([
                'Plaza San Martín', 'Plaza Belgrano', 'Parque Centenario', 
                'Plaza Sarmiento', 'Plaza Urquiza', 'Parque de la Ciudad'
            ]),
            
            // Reutilizamos distritos similares a los de las calles
            'district' => $this->faker->optional(0.9)->randomElement([
                'Distrito Centro', 'Zona Norte', 'Comuna 1', 'Comuna 2', 'Barrio San Martín'
            ]),
            
            // Coordenadas geográficas de prueba (90% de probabilidad de que tengan)
            // Ajustadas al mismo rango que usamos en los árboles
            'latitude' => $this->faker->optional(0.9)->latitude($min = -34.70, $max = -34.50),
            'longitude' => $this->faker->optional(0.9)->longitude($min = -58.60, $max = -58.30)
        ];
    }
}
