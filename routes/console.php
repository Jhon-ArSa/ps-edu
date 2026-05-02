<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── TAREAS PROGRAMADAS ───────────────────────────────────────────────────────

// Limpiar sesiones expiradas (diario a las 2 AM)
Schedule::command('session:gc')->daily()->at('02:00');

// Limpiar cache de vistas compiladas (semanal, domingos a las 3 AM)
Schedule::command('view:clear')->weekly()->sundays()->at('03:00');

// Limpiar archivos temporales antiguos (semanal)
Schedule::command('cache:prune-stale-tags')->weekly();

// Limpiar notificaciones leídas antiguas (mensual, primer día del mes a las 1 AM)
Schedule::call(function () {
    \DB::table('notifications')
        ->whereNotNull('read_at')
        ->where('read_at', '<', now()->subMonths(3))
        ->delete();
})->monthly()->at('01:00')->name('prune-old-notifications');

// Recordatorio de tareas próximas a vencer (diario a las 8 AM)
// Descomentar cuando se implemente el comando
// Schedule::command('tasks:send-reminders')->dailyAt('08:00');

// Backup de base de datos (diario a las 1 AM) — requiere spatie/laravel-backup
// Schedule::command('backup:run')->daily()->at('01:00');
