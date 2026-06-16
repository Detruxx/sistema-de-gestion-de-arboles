<?php

namespace Database\Factories;

use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // El usuario de prueba que creamos en el DatabaseSeeder (ID 1)
            'user_id' => 1, 
            
            // 50% de probabilidad de que el reclamo esté asociado a un árbol físico existente (IDs 1 al 50)
            'tree_id' => $this->faker->boolean(50) ? $this->faker->numberBetween(1, 50) : null,
            
            // Apunta a uno de los 7 tipos de reclamo fijos que creamos arriba
            'request_type_id' => $this->faker->numberBetween(1, 7),
            
            // Apunta a una de las 10 calles existentes
            'street_id' => $this->faker->numberBetween(1, 10),
            
            // Descripción del reclamo que escribe el vecino
            'description' => $this->faker->paragraph(2),
            
            // Tus enums de estado exactos
            'status' => $this->faker->randomElement(['open', 'in_progress', 'resolved']),
        ];
    }
}
