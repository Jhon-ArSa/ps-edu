<?php
/**
 * Script para crear directorios de uploads en public/
 * Ejecutar directamente desde el navegador o vía PHP CLI
 * 
 * URL: https://tu-dominio.com/create-upload-directories.php
 */

// Directorio base
$baseDir = __DIR__ . '/public/uploads';

// Directorios necesarios
$directories = [
    'materials',
    'tasks',
    'evaluations',
    'evaluation-answers',
    'submissions',
    'announcements',
    'avatars',
    'forum',
];

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Crear Directorios - PS-EDU</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{color:green;}.error{color:red;}.warning{color:orange;}</style></head><body>";
echo "<h1>🔧 Creación de Directorios de Uploads</h1>";
echo "<p><strong>Sistema:</strong> PS-EDU FAEDU</p>";
echo "<hr>";

// Verificar que estamos en el directorio correcto
if (!file_exists(__DIR__ . '/public')) {
    echo "<p class='error'>❌ Error: No se encuentra el directorio 'public/'. ";
    echo "Asegúrate de que este script esté en la raíz del proyecto.</p>";
    echo "</body></html>";
    exit;
}

$created = 0;
$existed = 0;
$errors = 0;

echo "<h2>📁 Creando directorios...</h2>";
echo "<ul>";

// Crear directorio base
if (!file_exists($baseDir)) {
    if (@mkdir($baseDir, 0755, true)) {
        echo "<li class='success'>✅ Directorio base creado: <code>public/uploads/</code></li>";
        $created++;
    } else {
        echo "<li class='error'>❌ Error al crear: <code>public/uploads/</code></li>";
        $errors++;
    }
} else {
    echo "<li class='warning'>⚠️ Ya existe: <code>public/uploads/</code></li>";
    $existed++;
}

// Crear subdirectorios
foreach ($directories as $dir) {
    $fullPath = $baseDir . '/' . $dir;
    
    if (!file_exists($fullPath)) {
        if (@mkdir($fullPath, 0755, true)) {
            echo "<li class='success'>✅ Creado: <code>public/uploads/{$dir}/</code></li>";
            $created++;
        } else {
            echo "<li class='error'>❌ Error al crear: <code>public/uploads/{$dir}/</code></li>";
            $errors++;
        }
    } else {
        echo "<li class='warning'>⚠️ Ya existe: <code>public/uploads/{$dir}/</code></li>";
        $existed++;
    }
}

echo "</ul>";

// Crear archivo .htaccess para seguridad
$htaccessPath = $baseDir . '/.htaccess';
$htaccessContent = <<<'HTACCESS'
# Protección de directorios de uploads
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Bloquear acceso a archivos PHP en uploads
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteCond %{REQUEST_FILENAME} \.(php|phtml|php3|php4|php5|php7|phar)$ [NC]
    RewriteRule .* - [F,L]
</IfModule>

# Permitir solo ciertos tipos de archivos
<FilesMatch "\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|ppt|pptx|zip|mp4|mp3)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Denegar acceso a todo lo demás
<FilesMatch "^.*$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# No listar directorios
Options -Indexes
HTACCESS;

if (!file_exists($htaccessPath)) {
    if (@file_put_contents($htaccessPath, $htaccessContent)) {
        echo "<p class='success'>✅ Archivo de seguridad creado: <code>.htaccess</code></p>";
    } else {
        echo "<p class='warning'>⚠️ No se pudo crear .htaccess (no crítico)</p>";
    }
} else {
    echo "<p class='warning'>⚠️ .htaccess ya existe</p>";
}

// Crear archivo index.php para protección
$indexPath = $baseDir . '/index.php';
if (!file_exists($indexPath)) {
    $indexContent = "<?php\n// Acceso denegado\nheader('HTTP/1.0 403 Forbidden');\ndie('Acceso denegado');";
    if (@file_put_contents($indexPath, $indexContent)) {
        echo "<p class='success'>✅ Archivo de protección creado: <code>index.php</code></p>";
    }
}

echo "<hr>";
echo "<h2>📊 Resumen</h2>";
echo "<ul>";
echo "<li>Directorios creados: <strong>{$created}</strong></li>";
echo "<li>Directorios existentes: <strong>{$existed}</strong></li>";
echo "<li>Errores: <strong>{$errors}</strong></li>";
echo "</ul>";

if ($errors === 0) {
    echo "<h3 class='success'>🎉 ¡Proceso completado exitosamente!</h3>";
    echo "<p>La estructura de directorios está lista para usar.</p>";
    echo "<h3>📋 Siguientes pasos:</h3>";
    echo "<ol>";
    echo "<li>Elimina este archivo <code>create-upload-directories.php</code> del servidor</li>";
    echo "<li>Sube todos los archivos del proyecto al hosting</li>";
    echo "<li>Ejecuta: <code>php artisan config:clear</code> (si tienes acceso SSH)</li>";
    echo "<li>O simplemente accede al sitio y los cambios se aplicarán</li>";
    echo "<li>Prueba subir un material o evaluación</li>";
    echo "</ol>";
} else {
    echo "<h3 class='error'>❌ Hubo errores</h3>";
    echo "<p>Verifica los permisos del servidor. Necesitas permisos de escritura en <code>public/</code></p>";
    echo "<p>Contacta con el soporte de tu hosting si el problema persiste.</p>";
}

// Verificar permisos
echo "<hr>";
echo "<h2>🔐 Verificación de Permisos</h2>";
$testFile = $baseDir . '/test_write.txt';
if (@file_put_contents($testFile, 'test')) {
    echo "<p class='success'>✅ Permisos de escritura: OK</p>";
    @unlink($testFile);
} else {
    echo "<p class='error'>❌ Sin permisos de escritura en <code>public/uploads/</code></p>";
    echo "<p>Solución: Cambia permisos a 755 o 775 desde cPanel File Manager</p>";
}

echo "<hr>";
echo "<p><small>PS-EDU FAEDU - Sistema de Posgrado UNCP</small></p>";
echo "</body></html>";
