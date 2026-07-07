<?php

namespace App\Services;

use App\Models\Street;

class StreetService
{
    /**
     * Parse an address string and return the corresponding Street model.
     * If the street doesn't exist, it creates a new one.
     *
     * @param string $address
     * @return Street
     */
    public function resolveFromAddress(string $address): Street
    {
        // Parsear la dirección libre para separar calle y altura
        // Ejemplo: "Av. Cabildo 2535, CABA" -> name = "Av. Cabildo", number = 2535
        preg_match('/^([^\d]+)\s+(\d+)/', $address, $matches);
        
        $streetName = isset($matches[1]) ? trim($matches[1]) : trim($address);
        $streetNumber = isset($matches[2]) ? (int)$matches[2] : 0;

        // Buscar la calle o crearla si no existe
        return Street::firstOrCreate(
            ['street_name' => $streetName, 'street_number' => $streetNumber],
            ['district' => 'Espacio Público'] // Opcional por defecto
        );
    }
}
