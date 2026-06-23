<?php

namespace Database\Factories;

use App\Models\Tree;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tree>
 */
class TreeFactory extends Factory
{
    protected $model = Tree::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Decidimos aleatoriamente si el árbol será de "Vereda" (contexto 1) o de "Plaza" (contexto 2)
        $isStreetTree = $this->faker->boolean(50); // 50% de probabilidad para cada uno

        return [
            // DATOS PRINCIPALES (Asumimos que existen IDs del 1 al 5 en especies)
            'species_id' => $this->faker->numberBetween(1, 5), 

            // CONTEXTO 1: Árbol de Vereda
            'planter_id' => $isStreetTree ? $this->faker->numberBetween(1, 5) : null,
            'street_id' => $isStreetTree ? $this->faker->numberBetween(1, 10) : null,
            'reference' => $isStreetTree ? 'Frente a chapa ' . $this->faker->numberBetween(100, 3000) : null,

            // CONTEXTO 2: Árbol de Plaza
            'park_id' => !$isStreetTree ? $this->faker->numberBetween(1, 3) : null,

            // Datos geográficos (Obligatorios según tu migración)
            // Ajusta las coordenadas al rango aproximado de tu ciudad de estudio
            'latitude' => $this->faker->latitude($min = -34.70, $max = -34.50), 
            'longitude' => $this->faker->longitude($min = -58.60, $max = -58.30),

            // Datos forestales y secundarios
            'years' => $this->faker->numberBetween(1, 80), //Edad aleatoria entre 1 y 80 años
            'height' => $this->faker->randomFloat(2, 1, 25), // Altura entre 1 y 25 metros
            'dap' => $this->faker->randomFloat(2, 10, 150),  // Diámetro a la altura del pecho (cm)
            'maintenance_status' => $this->faker->randomElement(['Bueno', 'Poda Urgente', 'Estable', 'Extracción']),
            
            // Estructura JSON para vitalidad
            'vitality' => json_encode([
                'follaje' => $this->faker->randomElement(['Completo', 'Ralo', 'Seco']),
                'plagas' => $this->faker->boolean(20) ? 'Cochinilla' : 'Ninguna'
            ]),
            
            'structure' => $this->faker->randomElement(['Equilibrada', 'Inclinada', 'Bifurcada']),
            'degree' => $this->faker->numberBetween(1, 4), // Ej: Grado de peligrosidad o inclinación
            'observations' => $this->faker->boolean(40) ? $this->faker->sentence() : null, // 40% de probabilidad de tener observaciones
        ];
    }
}
