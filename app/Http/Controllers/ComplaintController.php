<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    private $jsonPath = 'reclamos.json';

    private function getInitialMockData()
    {
        return [
            [
                'id' => 'REC-2026-001',
                'vecino' => 'Laura Gómez',
                'categoria' => 'Poda Urgente',
                'fecha' => '2026-06-20',
                'estado' => 'inspeccion',
                'descripcion' => 'Hay una rama gigante del Jacarandá que se está apoyando peligrosamente sobre los cables de luz y hace chispas cuando hay viento fuerte.',
                'direccion' => 'Av. Cabildo 2800, Belgrano',
                'especie' => 'Jacarandá',
                'email' => 'laura.gomez@gmail.com'
            ],
            [
                'id' => 'REC-2026-002',
                'vecino' => 'Carlos Bianchi',
                'categoria' => 'Solicitud de Plantación',
                'fecha' => '2026-06-18',
                'estado' => 'poda',
                'descripcion' => 'Solicito la plantación de un árbol autóctono en la cazuela que quedó vacía frente a mi domicilio tras la última tormenta de viento.',
                'direccion' => 'Mendoza 1500, Belgrano',
                'especie' => 'Fresno',
                'email' => 'carlos.b@yahoo.com.ar'
            ],
            [
                'id' => 'REC-2026-003',
                'vecino' => 'Sofía Martínez',
                'categoria' => 'Plantera Obstruida',
                'fecha' => '2026-06-17',
                'estado' => 'recibido',
                'descripcion' => 'Un comercio vecino cementó por completo la plantera del fresno de la vereda, impidiendo el drenaje. El árbol empezó a secarse rápidamente.',
                'direccion' => 'Vuelta de Obligado 2200, Belgrano',
                'especie' => 'Fresno',
                'email' => 'sofia.martinez@live.com'
            ],
            [
                'id' => 'REC-2026-004',
                'vecino' => 'Marcos Paz',
                'categoria' => 'Extracción por Peligro',
                'fecha' => '2026-06-15',
                'estado' => 'resuelto',
                'descripcion' => 'Árbol totalmente inclinado con raíces levantadas luego del temporal del fin de semana. Peligro de caída inminente en zona peatonal.',
                'direccion' => 'La Pampa 1900, Belgrano',
                'especie' => 'Palo Borracho',
                'email' => 'paz.marcos@gmail.com'
            ]
        ];
    }

    private function readComplaints()
    {
        if (!Storage::disk('local')->exists($this->jsonPath)) {
            $initialData = $this->getInitialMockData();
            Storage::disk('local')->put($this->jsonPath, json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $initialData;
        }

        $content = Storage::disk('local')->get($this->jsonPath);
        return json_decode($content, true) ?: [];
    }

    private function saveComplaints($complaints)
    {
        Storage::disk('local')->put($this->jsonPath, json_encode($complaints, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index()
    {
        $complaints = $this->readComplaints();
        return response()->json([
            'status' => 'success',
            'data' => $complaints
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string',
            'direccion' => 'required|string',
            'descripcion' => 'required|string',
            'arbol_id' => 'nullable|string',
            'vecino' => 'nullable|string',
            'email' => 'nullable|string|email'
        ]);

        $complaints = $this->readComplaints();

        // Generar un ID único simple
        $newIdNumber = count($complaints) + 1;
        $formattedId = 'REC-' . date('Y') . '-' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);

        $newComplaint = [
            'id' => $formattedId,
            'vecino' => $request->input('vecino', 'Vecino Anónimo'),
            'categoria' => $request->input('categoria'),
            'fecha' => date('Y-m-d'),
            'estado' => 'recibido',
            'descripcion' => $request->input('descripcion'),
            'direccion' => $request->input('direccion'),
            'especie' => $request->input('especie', $request->input('arbol_id') ? 'Especie ID ' . $request->input('arbol_id') : 'No especificada'),
            'email' => $request->input('email', 'sin-email@treeba.gob.ar'),
            'arbol_id' => $request->input('arbol_id')
        ];

        // Mapear categorías si vienen en formato corto del select de reclamos.blade.php
        $categoryMap = [
            'caido' => 'Extracción por Peligro',
            'seco' => 'Árbol Seco / Caída',
            'ramas' => 'Poda Urgente',
            'raices' => 'Plantera Obstruida',
            'otro' => 'Otros Daños'
        ];

        if (array_key_exists($newComplaint['categoria'], $categoryMap)) {
            $newComplaint['categoria'] = $categoryMap[$newComplaint['categoria']];
        }

        // Agregar al principio para que los más nuevos aparezcan primeros
        array_unshift($complaints, $newComplaint);

        $this->saveComplaints($complaints);

        return response()->json([
            'status' => 'success',
            'message' => 'Reclamo guardado con éxito',
            'data' => $newComplaint
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'nullable|string',
            'respuesta' => 'nullable|string'
        ]);

        $complaints = $this->readComplaints();
        $updated = false;

        foreach ($complaints as &$c) {
            if ($c['id'] === $id) {
                if ($request->has('estado')) {
                    $c['estado'] = $request->input('estado');
                }
                if ($request->has('respuesta')) {
                    $c['respuesta_admin'] = $request->input('respuesta');
                }
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reclamo no encontrado'
            ], 404);
        }

        $this->saveComplaints($complaints);

        return response()->json([
            'status' => 'success',
            'message' => 'Reclamo actualizado con éxito'
        ], 200);
    }

    public function show($id)
    {
        $complaints = $this->readComplaints();
        foreach ($complaints as $c) {
            if ($c['id'] === $id) {
                return response()->json([
                    'status' => 'success',
                    'data' => $c
                ], 200);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Reclamo no encontrado'
        ], 404);
    }
}
