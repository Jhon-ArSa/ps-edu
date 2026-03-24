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

        $url = $this->resolveNotificationDestination($notification->data, (string) auth()->user()->role);

        // Petición AJAX (dropdown del header): devolver destino para que JS navegue
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['redirect' => $url]);
        }

        // Formulario HTML (página de notificaciones): redirigir al recurso
        return redirect($url);
    }

    /**
     * Resuelve el destino de una notificación, con fallbacks por tipo.
     */
    private function resolveNotificationDestination(array $data, string $role): string
    {
        $icon = (string) ($data['icon'] ?? '');

        // Comunicados siempre deben llevar a Intranet (corrige payloads antiguos).
        if ($icon === 'announcement') {
            $route = match ($role) {
                'admin' => 'admin.intranet',
                'docente' => 'docente.intranet',
                default => 'alumno.intranet',
            };

            return route($route, [], false);
        }

        return $this->normalizeNotificationUrl((string) ($data['url'] ?? '/'));
    }

    /**
     * Normaliza URLs de notificaciones para trabajar entre entornos y prevenir open redirect.
     */
    private function normalizeNotificationUrl(string $url): string
    {
        if ($url === '') {
            return '/';
        }

        $parts = parse_url($url);

        // Ruta relativa simple
        if ($parts === false || ! isset($parts['host'])) {
            return str_starts_with($url, '/') ? $url : '/' . ltrim($url, '/');
        }

        // URL absoluta del mismo dominio actual
        if (($parts['host'] ?? null) === request()->getHost()) {
            return $url;
        }

        // URL absoluta de otro dominio (ej. 127.0.0.1 guardado en DB): conservar solo path local
        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return ($path !== '' ? $path : '/') . $query . $fragment;
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
