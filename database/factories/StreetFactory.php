<?php

namespace Database\Factories;

use App\Models\Street;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Street>
 */
class StreetFactory extends Factory
{
    protected $model = Street::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Genera nombres de calles realistas (Ej: "Av. San Martín", "Rivadavia")
            'street_name' => $this->faker->streetName(),
            
            // Altura de la calle (Ej: entre el 100 y el 5000)
            'street_number' => $this->faker->numberBetween(100, 5000),
            
            // Chapa física. Opcional, combinando el número o agregando letras (Ej: "1425", "1425-A")
            'door_plate' => $this->faker->optional(0.7)->passthrough(function() {
                return $this->faker->numberBetween(100, 5000) . ($this->faker->boolean(10) ? '-A' : '');
            }),
            
            // Comuna, barrio o distrito urbano
            'district' => $this->faker->optional(0.8)->randomElement([
                'Distrito Centro', 'Zona Norte', 'Comuna 1', 'Comuna 2', 'Barrio San Martín'
            ]),    
        ];
    }
}
