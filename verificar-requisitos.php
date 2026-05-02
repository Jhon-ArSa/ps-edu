<?php

/**
 * Script de Verificación de Requisitos del Sistema
 * 
 * Este script verifica que el servidor cumpla con los requisitos mínimos
 * para ejecutar PS-EDU (Laravel 12).
 * 
 * Uso: php verificar-requisitos.php
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                                                                              ║\n";
echo "║              VERIFICACIÓN DE REQUISITOS DEL SISTEMA - PS-EDU                ║\n";
echo "║                                                                              ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errors = [];
$warnings = [];
$success = [];

// ══════════════════════════════════════════════════════════════════════════════
// VERIFICAR VERSIÓN DE PHP
// ══════════════════════════════════════════════════════════════════════════════
echo "⏳ Verificando versión de PHP...\n";
$phpVersion = PHP_VERSION;
$phpVersionId = PHP_VERSION_ID;

if ($phpVersionId >= 80300) {
    $success[] = "✅ PHP $phpVersion (Excelente - Recomendado)";
} elseif ($phpVersionId >= 80200) {
    $success[] = "✅ PHP $phpVersion (Bueno - Mínimo requerido)";
} else {
    $errors[] = "❌ PHP $phpVersion (Requiere PHP 8.2.0 o superior)";
}

// ══════════════════════════════════════════════════════════════════════════════
// VERIFICAR EXTENSIONES DE PHP
// ══════════════════════════════════════════════════════════════════════════════
echo "⏳ Verificando extensiones de PHP...\n";

$requiredExtensions = [
    'bcmath' => 'BCMath',
    'ctype' => 'Ctype',
    'fileinfo' => 'Fileinfo',
    'json' => 'JSON',
    'mbstring' => 'Mbstring',
    'openssl' => 'OpenSSL',
    'pdo' => 'PDO',
    'pdo_mysql' => 'PDO MySQL',
    'tokenizer' => 'Tokenizer',
    'xml' => 'XML',
    'curl' => 'cURL',
    'zip' => 'Zip',
];

$optionalExtensions = [
    'gd' => 'GD (para procesamiento de imágenes)',
    'imagick' => 'Imagick (alternativa a GD)',
    'redis' => 'Redis (para cache y colas)',
];

foreach ($requiredExtensions as $ext => $name) {
    if (extension_loaded($ext)) {
        $success[] = "✅ Extensión $name";
    } else {
        $errors[] = "❌ Extensión $name (REQUERIDA)";
    }
}

$hasImageExtension = extension_loaded('gd') || extension_loaded('imagick');
if (!$hasImageExtension) {
    $warnings[] = "⚠️  No se encontró GD ni Imagick (recomendado para imágenes)";
}

foreach ($optionalExtensions as $ext => $name) {
    if (extension_loaded($ext)) {
        $success[] = "✅ Extensión $name (Opcional)";
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// VERIFICAR CONFIGURACIÓN DE PHP
// ══════════════════════════════════════════════════════════════════════════════
echo "⏳ Verificando configuración de PHP...\n";

$memoryLimit = ini_get('memory_limit');
$memoryLimitBytes = return_bytes($memoryLimit);
if ($memoryLimitBytes >= 256 * 1024 * 1024 || $memoryLimit == '-1') {
    $success[] = "✅ Memory Limit: $memoryLimit";
} else {
    $warnings[] = "⚠️  Memory Limit: $memoryLimit (Recomendado: 256M o superior)";
}

$maxExecutionTime = ini_get('max_execution_time');
if ($maxExecutionTime >= 60 || $maxExecutionTime == 0) {
    $success[] = "✅ Max Execution Time: $maxExecutionTime segundos";
} else {
    $warnings[] = "⚠️  Max Execution Time: $maxExecutionTime segundos (Recomendado: 60 o superior)";
}

$uploadMaxFilesize = ini_get('upload_max_filesize');
$uploadMaxBytes = return_bytes($uploadMaxFilesize);
if ($uploadMaxBytes >= 10 * 1024 * 1024) {
    $success[] = "✅ Upload Max Filesize: $uploadMaxFilesize";
} else {
    $warnings[] = "⚠️  Upload Max Filesize: $uploadMaxFilesize (Recomendado: 10M o superior)";
}

$postMaxSize = ini_get('post_max_size');
$postMaxBytes = return_bytes($postMaxSize);
if ($postMaxBytes >= 10 * 1024 * 1024) {
    $success[] = "✅ Post Max Size: $postMaxSize";
} else {
    $warnings[] = "⚠️  Post Max Size: $postMaxSize (Recomendado: 10M o superior)";
}

// ══════════════════════════════════════════════════════════════════════════════
// VERIFICAR PERMISOS DE ESCRITURA
// ══════════════════════════════════════════════════════════════════════════════
echo "⏳ Verificando permisos de escritura...\n";

$writableDirs = [
    'storage',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($writableDirs as $dir) {
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            $success[] = "✅ $dir (escribible)";
        } else {
            $errors[] = "❌ $dir (NO escribible - ejecutar: chmod -R 775 $dir)";
        }
    } else {
        $warnings[] = "⚠️  $dir (no existe - se creará automáticamente)";
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// VERIFICAR COMPOSER
// ══════════════════════════════════════════════════════════════════════════════
echo "⏳ Verificando Composer...\n";

$composerVersion = shell_exec('composer --version 2>&1');
if ($composerVersion && strpos($composerVersion, 'Composer') !== false) {
    preg_match('/(\d+\.\d+\.\d+)/', $composerVersion, $matches);
    $version = $matches[1] ?? 'desconocida';
    $success[] = "✅ Composer $version instalado";
} else {
    $errors[] = "❌ Composer no encontrado (instalar desde: https://getcomposer.org)";
}

// ══════════════════════════════════════════════════════════════════════════════
// VERIFICAR ARCHIVO .env
// ══════════════════════════════════════════════════════════════════════════════
echo "⏳ Verificando archivo .env...\n";

if (file_exists('.env')) {
    $success[] = "✅ Archivo .env existe";
    
    // Verificar APP_KEY
    $envContent = file_get_contents('.env');
    if (preg_match('/APP_KEY=base64:.+/', $envContent)) {
        $success[] = "✅ APP_KEY configurado";
    } else {
        $errors[] = "❌ APP_KEY no configurado (ejecutar: php artisan key:generate)";
    }
} else {
    $errors[] = "❌ Archivo .env no existe (copiar desde .env.example)";
}

// ══════════════════════════════════════════════════════════════════════════════
// MOSTRAR RESULTADOS
// ══════════════════════════════════════════════════════════════════════════════
echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                              RESULTADOS                                      ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (!empty($success)) {
    echo "✅ REQUISITOS CUMPLIDOS:\n";
    foreach ($success as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  ADVERTENCIAS:\n";
    foreach ($warnings as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORES CRÍTICOS:\n";
    foreach ($errors as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

// ══════════════════════════════════════════════════════════════════════════════
// RESUMEN FINAL
// ══════════════════════════════════════════════════════════════════════════════
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                              RESUMEN                                         ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$totalSuccess = count($success);
$totalWarnings = count($warnings);
$totalErrors = count($errors);

echo "✅ Requisitos cumplidos: $totalSuccess\n";
echo "⚠️  Advertencias: $totalWarnings\n";
echo "❌ Errores críticos: $totalErrors\n";
echo "\n";

if ($totalErrors === 0) {
    echo "🎉 ¡El servidor cumple con todos los requisitos mínimos!\n";
    echo "\n";
    echo "Próximos pasos:\n";
    echo "1. Instalar dependencias: composer install --optimize-autoloader --no-dev\n";
    echo "2. Configurar .env con tus datos\n";
    echo "3. Generar APP_KEY: php artisan key:generate\n";
    echo "4. Ejecutar migraciones: php artisan migrate --force\n";
    echo "5. Crear administrador: php artisan db:seed --class=ProductionSeeder --force\n";
    echo "\n";
    exit(0);
} else {
    echo "⚠️  El servidor NO cumple con todos los requisitos.\n";
    echo "\n";
    echo "Por favor, corrige los errores críticos antes de continuar.\n";
    echo "Consulta SOLUCION-ERROR-PHP.md para más información.\n";
    echo "\n";
    exit(1);
}

// ══════════════════════════════════════════════════════════════════════════════
// FUNCIONES AUXILIARES
// ══════════════════════════════════════════════════════════════════════════════

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int) $val;
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}
