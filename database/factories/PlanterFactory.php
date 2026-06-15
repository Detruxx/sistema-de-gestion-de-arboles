<?php

namespace Database\Factories;

use App\Models\Planter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Planter>
 */
class PlanterFactory extends Factory
{
    protected $model = Planter::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Asumimos que existen IDs de calles del 1 al 10 (lo vincularemos en el Seeder maestro)
            'street_id' => $this->faker->numberBetween(1, 10),
            
            // Tus enums exactos de la migración
            'planter_state' => $this->faker->randomElement(['empty', 'ocuppied', 'subocuppied', 'overocuppied']),
            'position' => $this->faker->randomElement(['in line', 'corner', 'out of line']),
            'height' => $this->faker->randomElement(['elevated', 'ground level', 'low level']),
            
            // Ancho de la vereda en centímetros (ej: entre 150cm y 500cm) o metros si prefieres números chicos
            'street_width' => $this->faker->optional(0.8)->numberBetween(150, 450), // 80% de probabilidad de tener dato
        ];
    }
}
