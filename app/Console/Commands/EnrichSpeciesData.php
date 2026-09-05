<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Specie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EnrichSpeciesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'species:enrich';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enriquece los datos botanicos de las especies consultando GBIF y Wikidata';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Buscando especies 'Sin clasificar'...");

        $species = Specie::where('common_name', 'Sin clasificar')
            ->orWhereNull('common_name')
            ->get();

        if ($species->isEmpty()) {
            $this->info('No hay especies sin clasificar.');
            return;
        }

        $this->info("Se encontraron {$species->count()} especies. Comenzando enriquecimiento...");

        $bar = $this->output->createProgressBar($species->count());

        foreach ($species as $specie) {
            $sciName = $specie->scientific_name;
            
            // Ignorar basura obvia
            if (stripos($sciName, 'No identificado') !== false || stripos($sciName, 'Sin datos') !== false) {
                $bar->advance();
                continue;
            }

            $family = 'Sin clasificar';
            $commonName = 'Sin clasificar';

            // 1. GBIF - Para Familia y posible Common Name
            try {
                $gbifResponse = Http::timeout(5)->get('https://api.gbif.org/v1/species/match', [
                    'name' => $sciName
                ]);
                
                if ($gbifResponse->successful()) {
                    $gbifData = $gbifResponse->json();
                    if (!empty($gbifData['family'])) {
                        $family = ucfirst(strtolower($gbifData['family']));
                    }

                    // Intentar sacar nombre vernáculo en español
                    if (!empty($gbifData['usageKey'])) {
                        $vResponse = Http::timeout(5)->get("https://api.gbif.org/v1/species/{$gbifData['usageKey']}/vernacularNames");
                        if ($vResponse->successful()) {
                            $vData = $vResponse->json();
                            foreach ($vData['results'] ?? [] as $v) {
                                if (($v['language'] ?? '') === 'spa') {
                                    $commonName = Str::title($v['vernacularName']);
                                    break;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore API failures for GBIF
            }

            // 2. WIKIDATA - (Mejor para nombres comunes) solo si GBIF no dio nombre comun o queremos la mejor
            try {
                // Buscamos en Wikipedia ES primero para sacar el ID de Wikidata
                $wikiResponse = Http::withHeaders([
                    'User-Agent' => 'Arborea/1.0 (contacto@arborea.ar)'
                ])->timeout(5)->get('https://es.wikipedia.org/w/api.php', [
                    'action' => 'query',
                    'prop' => 'pageprops',
                    'ppprop' => 'wikibase_item',
                    'redirects' => '1',
                    'titles' => $sciName,
                    'format' => 'json'
                ]);

                if ($wikiResponse->successful()) {
                    $wikiData = $wikiResponse->json();
                    $pages = $wikiData['query']['pages'] ?? [];
                    $page = reset($pages);
                    $wikidataId = $page['pageprops']['wikibase_item'] ?? null;

                    if ($wikidataId) {
                        $wdResponse = Http::withHeaders([
                            'User-Agent' => 'Arborea/1.0'
                        ])->timeout(5)->get('https://www.wikidata.org/w/api.php', [
                            'action' => 'wbgetentities',
                            'ids' => $wikidataId,
                            'props' => 'labels',
                            'languages' => 'es',
                            'format' => 'json'
                        ]);

                        if ($wdResponse->successful()) {
                            $wdData = $wdResponse->json();
                            $entity = $wdData['entities'][$wikidataId] ?? null;
                            if (isset($entity['labels']['es']['value'])) {
                                $wdName = $entity['labels']['es']['value'];
                                // Wikidata suele dar mejores nombres que GBIF (ej: Jacarandá vs Cacha cacha)
                                $commonName = Str::title($wdName);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore Wikipedia API errors
            }

            // Si ambos fallan pero tenemos el científico, a veces el científico es el único nombre
            if ($commonName === 'Sin clasificar') {
                 // Si no encontramos nada, queda sin clasificar, pero si queremos podemos usar la primer palabra
                 // del científico. Dejamos Sin clasificar para que sea evidente que falta.
            }

            $specie->family = $family;
            $specie->common_name = $commonName;
            $specie->save();

            // Pequeña pausa para no saturar las APIs
            usleep(200000); // 200ms

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('¡Enriquecimiento completado!');
    }
}
