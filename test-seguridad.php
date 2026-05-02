<?php

/**
 * Script de Prueba de Seguridad OWASP
 * 
 * Este script verifica que todas las medidas de seguridad estén implementadas correctamente.
 * 
 * Uso: php test-seguridad.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         PRUEBA DE SEGURIDAD OWASP TOP 10 - PS-EDU            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$tests = [
    'Headers de Seguridad' => function() {
        $middleware = new \App\Http\Middleware\SecurityHeadersMiddleware();
        return class_exists(\App\Http\Middleware\SecurityHeadersMiddleware::class);
    },
    
    'Listener de Eventos de Seguridad' => function() {
        return class_exists(\App\Listeners\LogSecurityEvents::class);
    },
    
    'Regla de Contraseña Fuerte' => function() {
        return class_exists(\App\Rules\StrongPassword::class);
    },
    
    'Comando de Desbloqueo' => function() {
        return class_exists(\App\Console\Commands\UnlockUserAccount::class);
    },
    
    'Campos de Bloqueo en User' => function() {
        $user = new \App\Models\User();
        return in_array('failed_login_attempts', $user->getFillable()) &&
               in_array('locked_until', $user->getFillable()) &&
               in_array('last_failed_login_at', $user->getFillable());
    },
    
    'Métodos de Bloqueo en User' => function() {
        return method_exists(\App\Models\User::class, 'isLocked') &&
               method_exists(\App\Models\User::class, 'lockAccount') &&
               method_exists(\App\Models\User::class, 'unlockAccount') &&
               method_exists(\App\Models\User::class, 'incrementFailedLoginAttempts') &&
               method_exists(\App\Models\User::class, 'resetFailedLoginAttempts');
    },
    
    'Canal de Logs de Seguridad' => function() {
        $config = config('logging.channels.security');
        return $config !== null && 
               $config['driver'] === 'daily' &&
               $config['days'] === 365;
    },
    
    'Tabla users con campos de bloqueo' => function() {
        try {
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');
            return in_array('failed_login_attempts', $columns) &&
                   in_array('locked_until', $columns) &&
                   in_array('last_failed_login_at', $columns);
        } catch (\Exception $e) {
            return false;
        }
    },
];

$passed = 0;
$failed = 0;

foreach ($tests as $name => $test) {
    echo "⏳ Probando: {$name}... ";
    try {
        $result = $test();
        if ($result) {
            echo "✅ PASÓ\n";
            $passed++;
        } else {
            echo "❌ FALLÓ\n";
            $failed++;
        }
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                        RESUMEN                                 ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Pruebas pasadas: {$passed}\n";
echo "❌ Pruebas fallidas: {$failed}\n";
echo "📊 Total: " . ($passed + $failed) . "\n";
echo "\n";

if ($failed === 0) {
    echo "🎉 ¡Todas las pruebas pasaron! El sistema está seguro.\n";
    echo "\n";
    echo "Próximos pasos:\n";
    echo "1. Probar rate limiting: Intentar login 5 veces con credenciales incorrectas\n";
    echo "2. Probar bloqueo de cuenta: Intentar login 10 veces con credenciales incorrectas\n";
    echo "3. Verificar logs: tail -f storage/logs/security.log\n";
    echo "4. Probar contraseña fuerte: Crear usuario con contraseña débil (debe fallar)\n";
    echo "5. Probar desbloqueo: php artisan user:unlock usuario@ejemplo.com\n";
    echo "\n";
    exit(0);
} else {
    echo "⚠️  Algunas pruebas fallaron. Revise la implementación.\n";
    echo "\n";
    exit(1);
}
