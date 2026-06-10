#!/usr/bin/env php
<?php

/**
 * Script de Verificación del Sistema PS-EDU
 * 
 * Este script verifica que todas las configuraciones del sistema estén correctas
 * para producción antes de subirlo al servidor.
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║     VERIFICACIÓN DEL SISTEMA PS-EDU - AUDITORÍA COMPLETA    ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errores = [];
$advertencias = [];
$exitos = 0;
$total = 0;

function verificar($nombre, $condicion, $mensajeExito, $mensajeError, $esAdvertencia = false) {
    global $errores, $advertencias, $exitos, $total;
    $total++;
    
    if ($condicion) {
        echo "✅ $mensajeExito\n";
        $exitos++;
    } else {
        if ($esAdvertencia) {
            echo "⚠️  $mensajeError\n";
            $advertencias[] = $mensajeError;
        } else {
            echo "❌ $mensajeError\n";
            $errores[] = $mensajeError;
        }
    }
}

// Cargar entorno
if (!file_exists('.env')) {
    die("❌ ERROR CRÍTICO: Archivo .env no encontrado\n\n");
}

$env = parse_ini_file('.env');

echo "📋 VERIFICANDO CONFIGURACIÓN DE ENTORNO\n";
echo "─────────────────────────────────────────\n";

verificar(
    'APP_ENV',
    isset($env['APP_ENV']) && $env['APP_ENV'] === 'production',
    'APP_ENV está configurado como "production"',
    'APP_ENV NO está en modo "production" (actual: ' . ($env['APP_ENV'] ?? 'no definido') . ')'
);

verificar(
    'APP_DEBUG',
    isset($env['APP_DEBUG']) && ($env['APP_DEBUG'] === 'false' || $env['APP_DEBUG'] === false),
    'APP_DEBUG está desactivado (false)',
    'APP_DEBUG está activado - DEBE estar en false en producción'
);

verificar(
    'APP_URL',
    isset($env['APP_URL']) && strpos($env['APP_URL'], 'https://') === 0,
    'APP_URL usa HTTPS (' . $env['APP_URL'] . ')',
    'APP_URL no usa HTTPS o no está configurado'
);

verificar(
    'APP_KEY',
    isset($env['APP_KEY']) && strlen($env['APP_KEY']) > 30,
    'APP_KEY está configurado',
    'APP_KEY no está configurado o es muy corto'
);

verificar(
    'APP_LOCALE',
    isset($env['APP_LOCALE']) && $env['APP_LOCALE'] === 'es',
    'APP_LOCALE configurado en español',
    'APP_LOCALE no está en español',
    true
);

echo "\n📊 VERIFICANDO BASE DE DATOS\n";
echo "─────────────────────────────────────────\n";

verificar(
    'DB_HOST',
    isset($env['DB_HOST']) && !empty($env['DB_HOST']),
    'DB_HOST configurado (' . $env['DB_HOST'] . ')',
    'DB_HOST no está configurado'
);

verificar(
    'DB_DATABASE',
    isset($env['DB_DATABASE']) && !empty($env['DB_DATABASE']),
    'DB_DATABASE configurado (' . $env['DB_DATABASE'] . ')',
    'DB_DATABASE no está configurado'
);

verificar(
    'DB_USERNAME',
    isset($env['DB_USERNAME']) && !empty($env['DB_USERNAME']),
    'DB_USERNAME configurado',
    'DB_USERNAME no está configurado'
);

verificar(
    'DB_PASSWORD',
    isset($env['DB_PASSWORD']) && !empty($env['DB_PASSWORD']),
    'DB_PASSWORD configurado',
    'DB_PASSWORD no está configurado'
);

echo "\n📧 VERIFICANDO CONFIGURACIÓN DE EMAIL\n";
echo "─────────────────────────────────────────\n";

verificar(
    'MAIL_MAILER',
    isset($env['MAIL_MAILER']) && $env['MAIL_MAILER'] === 'smtp',
    'MAIL_MAILER configurado como SMTP',
    'MAIL_MAILER no está configurado como SMTP'
);

verificar(
    'MAIL_HOST',
    isset($env['MAIL_HOST']) && !empty($env['MAIL_HOST']),
    'MAIL_HOST configurado (' . $env['MAIL_HOST'] . ')',
    'MAIL_HOST no está configurado'
);

verificar(
    'MAIL_USERNAME',
    isset($env['MAIL_USERNAME']) && !empty($env['MAIL_USERNAME']),
    'MAIL_USERNAME configurado',
    'MAIL_USERNAME no está configurado'
);

verificar(
    'MAIL_PASSWORD',
    isset($env['MAIL_PASSWORD']) && !empty($env['MAIL_PASSWORD']),
    'MAIL_PASSWORD configurado',
    'MAIL_PASSWORD no está configurado'
);

echo "\n🔐 VERIFICANDO CONFIGURACIÓN DE SEGURIDAD\n";
echo "─────────────────────────────────────────\n";

verificar(
    'SESSION_SECURE_COOKIE',
    isset($env['SESSION_SECURE_COOKIE']) && ($env['SESSION_SECURE_COOKIE'] === 'true' || $env['SESSION_SECURE_COOKIE'] === true),
    'SESSION_SECURE_COOKIE está activado (HTTPS only)',
    'SESSION_SECURE_COOKIE no está activado - Las cookies no serán seguras',
    true
);

verificar(
    'FORCE_HTTPS',
    isset($env['FORCE_HTTPS']) && ($env['FORCE_HTTPS'] === 'true' || $env['FORCE_HTTPS'] === true),
    'FORCE_HTTPS está activado',
    'FORCE_HTTPS no está activado',
    true
);

verificar(
    'BCRYPT_ROUNDS',
    isset($env['BCRYPT_ROUNDS']) && $env['BCRYPT_ROUNDS'] >= 12,
    'BCRYPT_ROUNDS configurado adecuadamente (' . $env['BCRYPT_ROUNDS'] . ')',
    'BCRYPT_ROUNDS es menor a 12 (recomendado mínimo 12)',
    true
);

echo "\n📁 VERIFICANDO ARCHIVOS CRÍTICOS\n";
echo "─────────────────────────────────────────\n";

$archivos = [
    'composer.json' => 'Configuración de Composer',
    'composer.lock' => 'Lock de dependencias',
    'bootstrap/app.php' => 'Bootstrap de Laravel',
    'public/index.php' => 'Punto de entrada',
    'public/.htaccess' => 'Configuración Apache',
    'artisan' => 'CLI de Laravel',
];

foreach ($archivos as $archivo => $descripcion) {
    verificar(
        $archivo,
        file_exists($archivo),
        "$descripcion existe",
        "$descripcion NO existe"
    );
}

echo "\n📦 VERIFICANDO CARPETAS CRÍTICAS\n";
echo "─────────────────────────────────────────\n";

$carpetas = [
    'vendor' => 'Dependencias de Composer',
    'app' => 'Código de la aplicación',
    'config' => 'Configuraciones',
    'database' => 'Migraciones y seeders',
    'resources' => 'Vistas y assets',
    'routes' => 'Rutas',
    'storage' => 'Logs y cache',
    'public' => 'Punto de entrada público',
];

foreach ($carpetas as $carpeta => $descripcion) {
    verificar(
        $carpeta,
        is_dir($carpeta),
        "Carpeta $descripcion existe",
        "Carpeta $descripcion NO existe"
    );
}

// Verificar que vendor tiene contenido
if (is_dir('vendor')) {
    $vendorSize = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('vendor'));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $vendorSize++;
        }
    }
    
    verificar(
        'vendor_contenido',
        $vendorSize > 1000,
        "Carpeta vendor tiene contenido ($vendorSize archivos)",
        "Carpeta vendor parece vacía o incompleta ($vendorSize archivos)"
    );
}

echo "\n🔧 VERIFICANDO PERMISOS\n";
echo "─────────────────────────────────────────\n";

verificar(
    'storage_writable',
    is_writable('storage'),
    'Carpeta storage es escribible',
    'Carpeta storage NO es escribible - Configurar permisos 755',
    true
);

verificar(
    'bootstrap_cache_writable',
    is_writable('bootstrap/cache'),
    'Carpeta bootstrap/cache es escribible',
    'Carpeta bootstrap/cache NO es escribible - Configurar permisos 755',
    true
);

echo "\n🚀 VERIFICANDO OPTIMIZACIONES\n";
echo "─────────────────────────────────────────\n";

verificar(
    'config_cached',
    file_exists('bootstrap/cache/config.php'),
    'Configuración está cacheada',
    'Configuración NO está cacheada - Ejecutar: php artisan config:cache',
    true
);

verificar(
    'routes_cached',
    file_exists('bootstrap/cache/routes-v7.php'),
    'Rutas están cacheadas',
    'Rutas NO están cacheadas - Ejecutar: php artisan route:cache',
    true
);

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "                    RESUMEN DE VERIFICACIÓN\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";
echo "✅ Verificaciones exitosas: $exitos/$total\n";
echo "⚠️  Advertencias: " . count($advertencias) . "\n";
echo "❌ Errores críticos: " . count($errores) . "\n";
echo "\n";

if (count($errores) > 0) {
    echo "❌ ERRORES CRÍTICOS ENCONTRADOS:\n";
    echo "─────────────────────────────────────────\n";
    foreach ($errores as $error) {
        echo "  • $error\n";
    }
    echo "\n";
    echo "⚠️  EL SISTEMA NO ESTÁ LISTO PARA PRODUCCIÓN\n";
    echo "    Corrige los errores antes de continuar.\n";
    echo "\n";
    exit(1);
}

if (count($advertencias) > 0) {
    echo "⚠️  ADVERTENCIAS ENCONTRADAS:\n";
    echo "─────────────────────────────────────────\n";
    foreach ($advertencias as $advertencia) {
        echo "  • $advertencia\n";
    }
    echo "\n";
}

if (count($errores) === 0) {
    echo "✅ SISTEMA VERIFICADO Y LISTO PARA PRODUCCIÓN\n";
    echo "\n";
    echo "Calificación: " . round(($exitos / $total) * 10, 1) . "/10\n";
    echo "\n";
    echo "Próximo paso:\n";
    echo "1. Leer: INSTRUCCIONES-SUBIDA-PASO-A-PASO.md\n";
    echo "2. Subir archivos al servidor\n";
    echo "3. Configurar Document Root\n";
    echo "4. ¡Disfrutar! 🚀\n";
    echo "\n";
}

exit(0);
