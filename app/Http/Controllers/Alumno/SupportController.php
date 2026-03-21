<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    /**
     * Muestra el formulario de soporte técnico.
     */
    public function index()
    {
        return view('alumno.soporte');
    }

    /**
     * Procesa el envío del mensaje de soporte.
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'category' => 'required|string|in:tecnico,academico,acceso,otro',
            'message'  => 'required|string|max:2000',
        ]);

        // Registrar la solicitud de soporte
        Log::channel('support')->info('Solicitud de soporte de alumno', [
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name,
            'email'     => auth()->user()->email,
            'category'  => $request->category,
            'subject'   => $request->subject,
            'message'   => $request->message,
            'ip'        => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        // En producción, aquí se enviaría un correo o se crearía un ticket
        // Mail::to(config('support.admin_email'))->send(new SupportTicket(...));

        return back()->with('success', '¡Mensaje enviado correctamente! Nuestro equipo de soporte se comunicará contigo a través de tu correo institucional.');
    }
}
