<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Redirigir usuarios ya autenticados que intenten ir a rutas guest (/login)
        $middleware->redirectUsersTo(function () {
            if (!auth()->check()) return route('login');
            return match(auth()->user()->role) {
                'admin'   => route('admin.dashboard'),
                'docente' => route('docente.dashboard'),
                'alumno'  => route('alumno.dashboard'),
                default   => route('login'),
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sesión expirada (CSRF mismatch) → redirigir al login con mensaje amigable
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            return redirect()->route('login')
                ->with('error', 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.');
        });

        // Demasiados intentos de login → redirigir al login con mensaje
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->is('login')) {
                return redirect()->route('login')
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Demasiados intentos fallidos. Espera 1 minuto antes de intentarlo nuevamente.']);
            }
        });
    })->create();
