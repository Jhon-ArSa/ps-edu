<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // For now, just log the support request
        // In production, this would send an email or create a ticket
        \Log::info('Support request from docente', [
            'user_id' => auth()->id(),
            'user'    => auth()->user()->name,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', '¡Mensaje enviado! Nos comunicaremos con usted a la brevedad.');
    }
}
