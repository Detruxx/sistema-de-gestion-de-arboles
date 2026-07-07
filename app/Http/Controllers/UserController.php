<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    /**
     * Muestra el listado de usuarios para la administracion
     */
    public function index(\Illuminate\Http\Request $request)
    { 
        $query = User::orderBy('id','desc');
        
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        // Retornamos los usuarios en formato JSON con estado de éxito
        return response()->json([
            'status' => 'success',
            'data'   => $users,
        ], 200);
    }

    public function adminStats()
    {
        return response()->json([
            'residents' => User::where('role', 'vecino')->count(),
            'inspectors' => User::where('role', 'inspector')->count(),
            'companies' => \App\Models\Company::count(),
            'pendingCompanies' => \App\Models\Company::where('status', 'Pendiente')->count()
        ], 200);
    }

    /**
     * Muestra el formulario para editar un usuario (ej: cambiar el rol de vecino a trabajador).
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('profile.configuracion', compact('user'));
    }

    /**
     * Actualiza el perfil del usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validamos que los datos sean correctos
        $data = $request->validate([
           'name'       => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'role'      => ['required', 'string', 'in:admin,inspector,worker,vecino'],
        ],
        [
            'name.required'      => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Debes ingresar un correo electrónico válido.',
            'email.unique'       => 'Este correo electrónico ya está registrado.',
            'role.required'      => 'El rol es obligatorio.',
            'role.in'            => 'El rol debe ser uno de los siguientes: admin, inspector, worker, vecino.',
        ]);

        // Actualizamos el usuario
        $user->update($data);

        // Enviamos un mensaje flash de éxito
        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');

    }

    /**
     * Elimina el usuario
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // No se puede eliminar la cuenta propia
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'No se puede eliminar la cuenta propia.');
        }

        // Elimina el usuario
        $user->delete();
        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Actualiza el rol del usuario
     */ 
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validamos que los datos sean correctos
        $request->validate([ 
            'role' => ['required','string', 'in:admin,inspector,vecino']    
        ],[
            'role.required' => 'El rol es obligatorio.',
            'role.in'       => 'El rol debe ser uno de los siguientes: admin, inspector, vecino.',
        ]);

        // No se puede cambiar el rol de usuario mientras esté logueado 
        if(auth()->user()->id == $user->id && $request->role !== 'admin'){
            return response()->json([
                'status'  => 'error',
                'message' => 'No podés quitarte el rol de Administrador a vos mismo mientras estés logueado.'
            ], 403);
        }

        // Actualizamos el rol
        $user->update([
            'role' => $request->role, 
        ]);
        
        // Enviamos un mensaje flash de éxito
        return response()->json([
            'status'  => 'success',
            'message' => 'Rol actualizado correctamente.',
            'data'    => $user,
        ], 200); 
    }

}
