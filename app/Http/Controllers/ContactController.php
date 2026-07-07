<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    /**
     * Guarda el mensaje enviado por el vecino.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|min:5',
        ], [
            'mensaje.required' => 'El cuerpo del mensaje es obligatorio.',
            'mensaje.min' => 'El mensaje debe contener al menos 5 caracteres.',
        ]);

        $user = Auth::user();
        $userId = $user ? $user->id : 1; // Fallback al vecino de pruebas si no se puede determinar

        ContactMessage::create([
            'user_id' => $userId,
            'message' => $request->mensaje,
            'status' => 'unread'
        ]);
        
        return back()->with('success', '¡Tu mensaje ha sido enviado con éxito! Nos pondremos en contacto a la brevedad.');
    }

    /**
     * Lista los mensajes de contacto para el inspector/admin (los mas viejos arriba).
     */
    public function index(Request $request)
    {
        $mensajes = ContactMessage::with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('profile.mensajes', compact('mensajes'));
    }

    /**
     * Marca un mensaje como leido.
     */
    public function markRead(Request $request, $id)
    {
        $msg = ContactMessage::find($id);
        if ($msg) {
            $msg->status = 'read';
            $msg->save();
            return back()->with('success', 'Mensaje marcado como leído.');
        }

        return back()->with('error', 'Mensaje no encontrado.');
    }
    /**
     * Responde a un mensaje de contacto (Solo Inspector/Admin).
     */
    public function reply(Request $request, $id)
    {
        // =========================================================================
        // SKELETON PARA BACKEND: Lógica de respuesta oficial al vecino
        // =========================================================================
        $request->validate([
            'reply_message' => 'required|string|min:5'
        ]);

        $msg = ContactMessage::findOrFail($id);
        
        // 1. Guardar la respuesta y cambiar estado
        $msg->inspector_response = $request->reply_message;
        $msg->is_new_for_user = true; // Activa el punto rojo en la campana del vecino
        
        $msg->status = 'answered';
        $msg->save();

        // 2. Enviar correo electrónico al vecino notificando la respuesta
        // TODO (BACKEND): Ej -> Mail::to($msg->user->email)->send(new ContactRepliedMail($msg));

        return back()->with('success', 'Respuesta oficial enviada con éxito.');
    }
}
