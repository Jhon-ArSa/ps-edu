<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketReceived;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        return view('docente.soporte');
    }

    public function send(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'category' => 'required|string|in:tecnico,academico,acceso,otro',
            'message'  => 'required|string|max:2000',
        ]);

        $ticket = SupportTicket::create([
            'user_id'  => auth()->id(),
            'subject'  => $request->subject,
            'category' => $request->category,
            'message'  => $request->message,
            'status'   => 'open',
            'priority' => 'medium',
        ]);

        $admins = User::where('role', 'admin')->where('status', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new SupportTicketReceived(
                ticketId: $ticket->id,
                subject: $ticket->subject,
                category: $ticket->category_label,
                requesterName: auth()->user()->name,
            ));
        }

        return back()->with('success', '¡Mensaje enviado! Nos comunicaremos con usted a la brevedad.');
    }
}
