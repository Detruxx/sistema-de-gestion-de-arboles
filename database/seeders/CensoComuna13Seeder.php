<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Specie;
use App\Models\Street;
use App\Models\Planter;
use App\Models\Tree;

class CensoComuna13Seeder extends Seeder
{
    public function run(): void
    {
        $file_path = base_path('Bdd_censo_2018.json');
        
        if (!file_exists($file_path)) {
            $this->command->error("No se encontró el archivo $file_path");
            return;
        }

        $this->command->info('Procesando datos de la Comuna 13...');

        // 1. Extraer datos únicos en una primera pasada
        $file_handle = fopen($file_path, 'r');
        $uniqueSpecies = [];
        $uniqueStreets = [];
        
        while (($line = fgets($file_handle)) !== false) {
            $line = trim($line);
            if (strpos($line, '"comuna": 13,') === false) continue;
            
            $clean_line = rtrim($line, ',');
            $feature = json_decode($clean_line, true);
            if (!$feature || !isset($feature['properties']) || $feature['properties']['comuna'] !== 13) continue;
            
            $props = $feature['properties'];
            
            $sp = $props['nombre_cientifico'] ?? 'Sin clasificar';
            $uniqueSpecies[$sp] = true;
            
            $stName = $props['calle_nombre'];
            $stNumber = (int)$props['calle_altura'];
            $doorPlate = $props['calle_chapa'] !== null ? (int)$props['calle_chapa'] : null;
            
            // Agrupamos por calle, manzana Y chapa para tener la dirección exacta
            $key = $stName . '|' . $stNumber . '|' . $doorPlate;
            if (!isset($uniqueStreets[$key])) {
                $uniqueStreets[$key] = [
                    'street_name' => $stName,
                    'street_number' => $stNumber,
                    'door_plate' => $doorPlate,
                    'district' => 'Comuna 13',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        fclose($file_handle);

        // 2. Insertar Especies
        $this->command->info('Insertando Especies (' . count($uniqueSpecies) . ')...');
        $speciesMap = [];
        foreach ($uniqueSpecies as $name => $_) {
            $specie = Specie::firstOrCreate(
                ['scientific_name' => $name],
                [
                    'common_name' => 'Sin clasificar',
                    'family' => 'Sin clasificar',
                    'origin' => 'Sin clasificar',
                    'foliage_type' => 'Sin clasificar'
                ]
            );
            $speciesMap[$name] = $specie->id;
        }

        // 3. Insertar Calles
        $this->command->info('Insertando Calles (' . count($uniqueStreets) . ')...');
        $streetChunks = array_chunk(array_values($uniqueStreets), 500);
        foreach ($streetChunks as $chunk) {
            Street::insert($chunk);
        }
        
        $streetsDb = Street::where('district', 'Comuna 13')->get();
        $streetMap = [];
        foreach ($streetsDb as $st) {
            $streetMap[$st->street_name . '|' . $st->street_number . '|' . $st->door_plate] = $st->id;
        }

        // 4. Insertar Planteras y Árboles en la segunda pasada
        $this->command->info('Insertando Planteras y Árboles (esto puede demorar unos minutos)...');
        
        $file_handle = fopen($file_path, 'r');
        $plantersToInsert = [];
        $treesToInsert = [];
        $count = 0;
        
        $nextPlanterId = DB::table('planters')->max('id') + 1;
        
        while (($line = fgets($file_handle)) !== false) {
            $line = trim($line);
            if (strpos($line, '"comuna": 13,') === false) continue;
            
            $clean_line = rtrim($line, ',');
            $feature = json_decode($clean_line, true);
            if (!$feature || !isset($feature['properties']) || $feature['properties']['comuna'] !== 13) continue;
            
            $props = $feature['properties'];
            
            // --- CALLE ---
            $stName = $props['calle_nombre'];
            $stNumber = (int)$props['calle_altura'];
            $doorPlate = $props['calle_chapa'] !== null ? (int)$props['calle_chapa'] : null;
            $key = $stName . '|' . $stNumber . '|' . $doorPlate;
            $streetId = $streetMap[$key] ?? null;
            
            // --- ESPECIE ---
            $sp = $props['nombre_cientifico'] ?? 'Sin clasificar';
            $speciesId = $speciesMap[$sp] ?? null;

            // --- PLANTERA ---
            $planterState = 'empty';
            $ep = $props['estado_plantera'];
            if ($ep) {
                if (strpos($ep, 'Sobreocupada') !== false) $planterState = 'overocuppied';
                elseif ($ep === 'Subocupada') $planterState = 'subocuppied';
                elseif (strpos($ep, 'cerrada') !== false || strpos($ep, 'cerrado') !== false) {
                    if (strpos($ep, 'Parcialmente') !== false) $planterState = 'partially closed';
                    else $planterState = 'closed';
                }
                elseif (strpos($ep, 'Ocupada') !== false || strpos($ep, 'ocupado') !== false) $planterState = 'ocuppied';
            }

            $position = null;
            $up = $props['ubicacion_plantera'];
            if ($up === 'Regular') $position = 'in line';
            elseif ($up === 'Ochava') $position = 'corner';
            elseif ($up === 'Fuera de línea') $position = 'out of line';

            $height = 'ground level';
            $np = $props['nivel_plantera'];
            if ($np) {
                $npLow = strtolower($np);
                if (strpos($npLow, 'elevada') !== false) $height = 'elevated';
                elseif (strpos($npLow, 'bajo') !== false || $npLow === 'bn') $height = 'low level';
            }

            $streetWidth = null;
            if ($props['ancho_acera'] !== null) {
                $streetWidth = (int)(floatval($props['ancho_acera']) * 100); 
            }

            $plantersToInsert[] = [
                'id' => $nextPlanterId,
                'street_id' => $streetId,
                'planter_state' => $planterState,
                'position' => $position,
                'height' => $height,
                'street_width' => $streetWidth,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // --- REFERENCIA (Ubicación) ---
            $ref = $props['ubicacion'];
            if ($ref !== null) {
                $ref = trim($ref);
                if (strtoupper($ref) === 'EX') {
                    $ref = 'Exacta';
                } else if (preg_match('/^(LA|LD)\s*(\d+)$/i', $ref, $matches)) {
                    $ref = strtoupper($matches[1]) . $matches[2]; 
                }
            }

            // --- ÁRBOL ---
            $treesToInsert[] = [
                'id' => (int)$props['nro_registro'],
                'species_id' => $speciesId,
                'planter_id' => $nextPlanterId,
                'street_id' => $streetId,
                'reference' => $ref,
                'park_id' => null,
                'latitude' => (float)$props['lat'],
                'longitude' => (float)$props['long'],
                'years' => null,
                'height' => $props['altura_arbol'] !== null ? (float)$props['altura_arbol'] : 0.00,
                'dap' => $props['diametro_altura_pecho'] !== null ? (float)$props['diametro_altura_pecho'] : 0.00,
                'maintenance_status' => null,
                'vitality' => null,
                'structure' => null,
                'degree' => null,
                'observations' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $nextPlanterId++;
            $count++;

            // Insertamos cada 1000
            if (count($treesToInsert) >= 1000) {
                Planter::insert($plantersToInsert);
                Tree::insert($treesToInsert);
                $plantersToInsert = [];
                $treesToInsert = [];
                $this->command->info("  Procesados $count árboles...");
            }
        }
        fclose($file_handle);

        if (count($treesToInsert) > 0) {
            Planter::insert($plantersToInsert);
            Tree::insert($treesToInsert);
        }

        // Adjust auto-increment to prevent future insert collisions if needed
        $maxTreeId = DB::table('trees')->max('id') + 1;
        DB::statement("ALTER TABLE trees AUTO_INCREMENT = $maxTreeId");
        DB::statement("ALTER TABLE planters AUTO_INCREMENT = $nextPlanterId");

        $this->command->info("¡Importación finalizada! Total importados: $count árboles.");
    }
}
