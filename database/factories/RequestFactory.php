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
        // Simulamos un array dinámico de fotos falsas (entre 1 y 3 fotos) o null (30% de probabilidad vacío)
        $hasPhotos = $this->faker->boolean(70);
        $photosArray = null;

        if ($hasPhotos) {
            $photosCount = $this->faker->numberBetween(1, 3); // Soporta hasta 3 fotografías 
            $photosArray = [];
            for ($i = 0; $i < $photosCount; $i++) {
                $photosArray[] = 'fotos/reclamo_prueba_' . $this->faker->numberBetween(1, 5) . '.jpg';
            }
        }

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
            
            // Tus estados del reclamo (Ajustado dinámicamente si querés abarcar los nuevos estados)
            'request_status_id' => $this->faker->numberBetween(1, 8),

            // Ahora almacena el arreglo para cumplir con el formato JSON requerido 
            'path' => $photosArray,

            // Dejamos explícitos los campos que ya tenías en tu estructura física de la tabla por consistencia
            'cancellation_reason' => null,
            'priority_id' => null,
            'linked_to' => null,
            'suggested_duplicate_id' => null,
        ];
    }
}
