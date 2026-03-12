<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Lista paginada de todas las notificaciones del usuario autenticado.
     */
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marcar una notificación como leída y redirigir a su URL destino.
     * Verifica que la notificación pertenezca al usuario (prevención de IDOR).
     * Si la petición es AJAX, retorna 204 (el frontend maneja la navegación).
     */
    public function markAsRead(Request $request, string $id): Response|RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        // Petición AJAX (dropdown del header): solo confirmar éxito, el JS navega
        if ($request->expectsJson() || $request->ajax()) {
            return response()->noContent();
        }

        // Formulario HTML (página de notificaciones): redirigir al recurso
        $url = $notification->data['url'] ?? url('/');

        // Prevenir open redirect: solo URLs del mismo dominio o rutas relativas
        if (! str_starts_with($url, url('/'))) {
            $url = url('/');
        }

        return redirect($url);
    }

    /**
     * Marcar todas las notificaciones no leídas del usuario como leídas.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
