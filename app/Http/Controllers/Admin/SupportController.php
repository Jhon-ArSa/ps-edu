<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketResponded;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SupportController extends Controller
{
    /**
     * Lista todos los tickets de soporte con filtros.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user.alumnoProfile', 'assignedTo'])
            ->orderByDesc('created_at');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        // Estadísticas
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];

        // Para los filtros
        $admins = User::where('role', 'admin')->where('status', true)->get();

        return view('admin.support.index', compact('tickets', 'stats', 'admins'));
    }

    /**
     * Muestra un ticket específico.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user.alumnoProfile', 'assignedTo', 'respondedBy']);

        $admins = User::where('role', 'admin')->where('status', true)->get();

        return view('admin.support.show', compact('ticket', 'admins'));
    }

    /**
     * Actualiza el estado y otros campos del ticket.
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $updateData = $request->only(['status', 'priority', 'assigned_to', 'admin_notes']);

        // Si se marca como resuelto, establecer la fecha
        if ($request->status === 'resolved' && $ticket->status !== 'resolved') {
            $updateData['resolved_at'] = now();
        } elseif ($request->status !== 'resolved') {
            $updateData['resolved_at'] = null;
        }

        $ticket->update($updateData);

        return back()->with('success', 'Ticket actualizado exitosamente.');
    }

    /**
     * Cambia rápidamente el estado de un ticket.
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'resolved' && $ticket->status !== 'resolved') {
            $updateData['resolved_at'] = now();
        } elseif ($request->status !== 'resolved') {
            $updateData['resolved_at'] = null;
        }

        $ticket->update($updateData);

        return back()->with('success', 'Estado del ticket actualizado.');
    }

    /**
     * Envía respuesta al usuario por correo y la registra en el ticket.
     */
    public function respond(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'response_message' => 'required|string|max:3000',
            'status'           => 'nullable|in:in_progress,resolved,closed',
        ]);

        $ticket->update([
            'response_message' => $request->response_message,
            'responded_by'     => auth()->id(),
            'responded_at'     => now(),
            'status'           => $request->status ?? $ticket->status,
            'resolved_at'      => ($request->status === 'resolved') ? now() : $ticket->resolved_at,
        ]);

        $ticket->user->notify(new SupportTicketResponded(
            ticketId: $ticket->id,
            subject: $ticket->subject,
            response: $request->response_message,
            adminName: auth()->user()->name,
        ));

        return back()->with('success', 'Respuesta enviada al usuario por correo y registrada en el ticket.');
    }

    /**
     * Asigna un ticket a un administrador.
     */
    public function assign(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => $request->assigned_to ? 'in_progress' : 'open',
        ]);

        $message = $request->assigned_to
            ? 'Ticket asignado exitosamente.'
            : 'Asignación del ticket removida.';

        return back()->with('success', $message);
    }

    /**
     * Elimina un ticket (solo si es necesario).
     */
    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();

        return redirect()->route('admin.support.index')
            ->with('success', 'Ticket eliminado exitosamente.');
    }

    /**
     * Exporta los tickets como CSV.
     */
    public function exportCsv(Request $request): Response
    {
        $query = SupportTicket::with(['user.alumnoProfile', 'assignedTo']);

        // Aplicar mismos filtros que en index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderByDesc('created_at')->get();

        $rows = [];
        $rows[] = [
            'ID', 'Fecha', 'Usuario', 'Email', 'Asunto', 'Categoría',
            'Prioridad', 'Estado', 'Asignado a', 'Resuelto'
        ];

        foreach ($tickets as $ticket) {
            $rows[] = [
                $ticket->id,
                $ticket->created_at->format('d/m/Y H:i'),
                $ticket->user->name,
                $ticket->user->email,
                $ticket->subject,
                $ticket->category_label,
                $ticket->priority_label,
                $ticket->status_label,
                $ticket->assignedTo?->name ?? '—',
                $ticket->resolved_at?->format('d/m/Y H:i') ?? '—',
            ];
        }

        $filename = 'tickets_soporte_' . now()->format('Ymd_His') . '.csv';
        return $this->csvResponse($filename, $rows);
    }

    /**
     * Genera respuesta CSV.
     */
    private function csvResponse(string $filename, array $rows): Response
    {
        $csv = "\xEF\xBB\xBF"; // BOM para compatibilidad con Excel
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($cell) {
                $cell = str_replace('"', '""', (string) $cell);
                return '"' . $cell . '"';
            }, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}