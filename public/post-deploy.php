<?php
/**
 * POST-DEPLOY SCRIPT
 * Ejecutar UNA VEZ después de git pull en el servidor.
 * ELIMINAR INMEDIATAMENTE después de usar.
 */

// Clave de seguridad - cambiar antes de subir al servidor
define('DEPLOY_KEY', 'deploy_2026_uncp');

if (!isset($_GET['key']) || $_GET['key'] !== DEPLOY_KEY) {
    die('<b>Acceso denegado.</b> Usa: post-deploy.php?key=deploy_2026_uncp');
}

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre style='font-family:monospace;font-size:13px;padding:20px'>";
echo "=== POST-DEPLOY SCRIPT ===\n\n";

// 1. Limpiar caches
$kernel->call('config:clear');  echo "✓ Config cache limpiada\n";
$kernel->call('cache:clear');   echo "✓ App cache limpiada\n";
$kernel->call('view:clear');    echo "✓ Views cache limpiada\n";
$kernel->call('route:clear');   echo "✓ Route cache limpiada\n";

// 2. Crear directorios necesarios
$dirs = [
    __DIR__.'/../storage/framework/sessions',
    __DIR__.'/../storage/framework/cache/data',
    __DIR__.'/../storage/framework/views',
    __DIR__.'/../storage/logs',
    __DIR__.'/../bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    chmod($dir, 0775);
}
echo "✓ Directorios y permisos OK\n";

// 3. Correr migraciones pendientes
$kernel->call('migrate', ['--force' => true]);
echo "✓ Migraciones:\n" . $kernel->output();

// 4. Regenerar caches de producción
$kernel->call('config:cache');  echo "✓ Config cacheada\n";
$kernel->call('route:cache');   echo "✓ Rutas cacheadas\n";
$kernel->call('view:cache');    echo "✓ Views cacheadas\n";

echo "\n=== LISTO ===\n";
echo "⚠️  ELIMINA ESTE ARCHIVO DEL SERVIDOR AHORA!\n";
echo "</pre>";
