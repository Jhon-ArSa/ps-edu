<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Sistema</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        h1 { color: #60a5fa; margin-bottom: 2rem; }
        h2 {color: #94a3b8; font-size: 1.1rem; margin-bottom: 1rem; }
        .status { 
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .ok { background: rgba(16,185,129,0.2); color: #10b981; }
        .error { background: rgba(239,68,68,0.2); color: #ef4444; }
        .info { background: rgba(59,130,246,0.2); color: #3b82f6; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        td {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(148,163,184,0.1);
        }
        td:first-child {
            font-weight: 600;
            color: #94a3b8;
            width: 200px;
        }
        .test-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 10px;
        }
    </style>
</head>
<body>

<h1>🔍 Diagnóstico del Sistema PS-EDU</h1>

<?php
// Cargar Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
?>

<div class="card">
    <h2>📊 Información del Servidor</h2>
    <table>
        <tr>
            <td>PHP Version</td>
            <td><?= phpversion() ?> <span class="status ok">OK</span></td>
        </tr>
        <tr>
            <td>Laravel Version</td>
            <td><?= app()->version() ?> <span class="status ok">OK</span></td>
        </tr>
        <tr>
            <td>Environment</td>
            <td><?= config('app.env') ?> <span class="status info"><?= config('app.env') === 'production' ? '✓' : '!' ?></span></td>
        </tr>
        <tr>
            <td>Debug Mode</td>
            <td><?= config('app.debug') ? 'ON ⚠️' : 'OFF ✓' ?> <span class="status <?= config('app.debug') ? 'error' : 'ok' ?>"><?= config('app.debug') ? 'UNSAFE' : 'SAFE' ?></span></td>
        </tr>
        <tr>
            <td>App URL</td>
            <td><?= config('app.url') ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h2>🗄️ Base de Datos</h2>
    <?php
    try {
        DB::connection()->getPdo();
        echo '<p><span class="status ok">✓ CONECTADO</span></p>';
        echo '<table>';
        echo '<tr><td>Driver</td><td>' . config('database.default') . '</td></tr>';
        echo '<tr><td>Host</td><td>' . config('database.connections.mysql.host') . '</td></tr>';
        echo '<tr><td>Database</td><td>' . config('database.connections.mysql.database') . '</td></tr>';
        
        $tablesCount = count(DB::select('SHOW TABLES'));
        echo '<tr><td>Tablas</td><td>' . $tablesCount . ' tablas</td></tr>';
        
        $usersCount = DB::table('users')->count();
        echo '<tr><td>Usuarios</td><td>' . $usersCount . ' usuarios</td></tr>';
        echo '</table>';
    } catch (Exception $e) {
        echo '<p><span class="status error">✗ ERROR</span></p>';
        echo '<p style="color:#ef4444;font-size:0.875rem;">' . $e->getMessage() . '</p>';
    }
    ?>
</div>

<div class="card">
    <h2>⚙️ Settings del Sistema</h2>
    <?php
    try {
        $settings = [
            'institution_name' => App\Models\Setting::get('institution_name', 'No configurado'),
            'institution_subtitle' => App\Models\Setting::get('institution_subtitle', 'No configurado'),
            'institution_acronym' => App\Models\Setting::get('institution_acronym', 'No configurado'),
        ];
        
        echo '<table>';
        foreach ($settings as $key => $value) {
            echo '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
        }
        echo '</table>';
        echo '<p style="margin-top:1rem;"><span class="status ok">✓ Settings cargados correctamente</span></p>';
    } catch (Exception $e) {
        echo '<p><span class="status error">✗ ERROR</span></p>';
        echo '<p style="color:#ef4444;font-size:0.875rem;">' . $e->getMessage() . '</p>';
    }
    ?>
</div>

<div class="card">
    <h2>🖼️ Assets y Archivos</h2>
    <table>
        <tr>
            <td>Logo Principal</td>
            <td>
                <?php if (file_exists(__DIR__.'/logo/logo-educacion.png')): ?>
                    <span class="status ok">✓ Existe</span>
                    <br><img src="logo/logo-educacion.png" class="test-img" alt="Logo">
                <?php else: ?>
                    <span class="status error">✗ No existe</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Imagen Posgrado</td>
            <td>
                <?php if (file_exists(__DIR__.'/logo/posgrado-uncp.jpg')): ?>
                    <span class="status ok">✓ Existe</span>
                <?php else: ?>
                    <span class="status error">✗ No existe</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Vite Build</td>
            <td>
                <?php if (file_exists(__DIR__.'/build/manifest.json')): ?>
                    <span class="status ok">✓ Compilado</span>
                    <?php
                    $manifest = json_decode(file_get_contents(__DIR__.'/build/manifest.json'), true);
                    echo '<br><small style="color:#94a3b8;">Archivos: ' . count($manifest) . '</small>';
                    ?>
                <?php else: ?>
                    <span class="status error">✗ No compilado</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<div class="card">
    <h2>🔐 Cachés y Optimizaciones</h2>
    <table>
        <tr>
            <td>Config Cache</td>
            <td>
                <?php if (file_exists(__DIR__.'/../bootstrap/cache/config.php')): ?>
                    <span class="status ok">✓ Cacheado</span>
                <?php else: ?>
                    <span class="status info">No cacheado</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Routes Cache</td>
            <td>
                <?php if (file_exists(__DIR__.'/../bootstrap/cache/routes-v7.php')): ?>
                    <span class="status ok">✓ Cacheado</span>
                <?php else: ?>
                    <span class="status info">No cacheado</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Storage Writable</td>
            <td>
                <?php if (is_writable(__DIR__.'/../storage')): ?>
                    <span class="status ok">✓ Escribible</span>
                <?php else: ?>
                    <span class="status error">✗ No escribible</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<div class="card">
    <h2>🔗 Rutas de Prueba</h2>
    <p style="margin-bottom:1rem;color:#94a3b8;">Prueba estos enlaces para verificar el sistema:</p>
    <ul style="list-style:none;padding:0;">
        <li style="margin-bottom:0.5rem;">
            <a href="test-login.html" style="color:#60a5fa;text-decoration:none;">→ Test Login (HTML estático)</a>
        </li>
        <li style="margin-bottom:0.5rem;">
            <a href="../" style="color:#60a5fa;text-decoration:none;">→ Login Laravel (Ruta principal)</a>
        </li>
        <li style="margin-bottom:0.5rem;">
            <a href="../up" style="color:#60a5fa;text-decoration:none;">→ Health Check</a>
        </li>
    </ul>
</div>

<p style="margin-top:2rem;text-align:center;color:#64748b;font-size:0.875rem;">
    Diagnóstico generado el <?= date('Y-m-d H:i:s') ?>
</p>

</body>
</html>
