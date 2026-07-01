<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    /**
     *  Muestra todas las prioridades
     */
    public function index()
    {
        $priorities = Priority::OrderBy('id', 'asc')->get();

        return view('admin.priorities.index', compact('priorities'));
    }

    /**
     * Guarda la prioridad creada
     */
    public function store(Request $request)
    {
        // Validamos que los datos sean correctos
        $data = $request->validate([
            'name' => ['required','string','max: 255', 'unique:priorities,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ],[
            'name.required' => 'El nombre de la prioridad es obligatorio.',
            'name.unique'   => 'Ya existe una prioridad con ese nombre.',
        ]);

        // Creamos la prioridad
        $priority = Priority::create($data);

        // Enviamos un mensaje flash de éxito
        return redirect()->back()->with('success', 'Prioridad creada correctamente.');   
    }


    /**
     * Actualiza la prioridad
     */
    public function update(Request $request, $id)
    {
        // Buscamos la prioridad
        $priority = Priority::findOrFail($id);

        // Validamos que los datos sean correctos
        $data = $request->validate([
            'name'        => ['required','string','max: 255', 'unique:priorities,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ],[
            'name.required' => 'El nombre de la prioridad es obligatorio.',
            'name.unique'   => 'Ya existe una prioridad con ese nombre.',
        ]);

        // Actualizamos la prioridad
        $priority->update($data);

        // Enviamos un mensaje flash de éxito
        return redirect()->route('admin.priorities.index')->with('success', 'Prioridad actualizada correctamente.');
    }

    /**
     * Elimina la prioridad
     */
    public function destroy($id)
    {
        // Buscamos la prioridad
        $priority = Priority::findOrFail($id);

        // Eliminamos la prioridad
        $priority->delete();

        // Enviamos un mensaje flash de éxito
        return redirect()->back()->with('success', 'Prioridad eliminada correctamente.');
    }
  
}
