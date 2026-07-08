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
        preg_match('/^([^\d]+)\s+(\d+)/', $address, $matches);
        
        $streetName = isset($matches[1]) ? trim($matches[1]) : trim($address);
        $doorPlate = isset($matches[2]) ? (int)$matches[2] : 0;
        
        // Calculamos la cuadra (street_number) redondeando hacia abajo a la centena más cercana
        $streetNumber = floor($doorPlate / 100) * 100;

        // Buscar la calle exacta (con su chapa) o crearla si no existe
        return Street::firstOrCreate(
            [
                'street_name' => $streetName, 
                'street_number' => $streetNumber,
                'door_plate' => $doorPlate
            ],
            ['district' => 'Comuna 13'] // Opcional por defecto
        );
    }
}
