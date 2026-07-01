<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrateDatabaseToLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-to-local 
                            {--export-only : Solo exportar, no importar}
                            {--import-only : Solo importar desde archivo existente}
                            {--file=database_backup.sql : Nombre del archivo SQL}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra toda la base de datos desde AWS RDS a MySQL local';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exportOnly = $this->option('export-only');
        $importOnly = $this->option('import-only');
        $filename = $this->option('file');
        $backupPath = storage_path('app/backups');
        $fullPath = $backupPath . '/' . $filename;

        // Crear directorio de backups si no existe
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        // PASO 1: EXPORTAR desde AWS RDS
        if (!$importOnly) {
            $this->info('🔵 PASO 1: Exportando base de datos desde AWS RDS...');
            $this->newLine();

            // Obtener credenciales actuales (AWS RDS)
            $awsHost = env('DB_HOST');
            $awsPort = env('DB_PORT', 3306);
            $awsDatabase = env('DB_DATABASE');
            $awsUsername = env('DB_USERNAME');
            $awsPassword = env('DB_PASSWORD');

            $this->table(
                ['Parámetro', 'Valor'],
                [
                    ['Host origen', $awsHost],
                    ['Puerto', $awsPort],
                    ['Base de datos', $awsDatabase],
                    ['Usuario', $awsUsername],
                ]
            );

            // Comando mysqldump
            $mysqldumpCmd = sprintf(
                'mysqldump --host=%s --port=%d --user=%s --password=%s --single-transaction --routines --triggers --events --add-drop-table --databases %s > %s 2>&1',
                escapeshellarg($awsHost),
                $awsPort,
                escapeshellarg($awsUsername),
                escapeshellarg($awsPassword),
                escapeshellarg($awsDatabase),
                escapeshellarg($fullPath)
            );

            $this->info('Ejecutando mysqldump...');
            exec($mysqldumpCmd, $output, $returnCode);

            if ($returnCode !== 0) {
                $this->error('❌ Error al exportar la base de datos.');
                $this->error('Salida: ' . implode("\n", $output));
                $this->newLine();
                $this->warn('💡 Verifica que mysqldump esté instalado: mysqldump --version');
                return Command::FAILURE;
            }

            if (!File::exists($fullPath) || File::size($fullPath) < 100) {
                $this->error('❌ El archivo de backup no se generó correctamente.');
                return Command::FAILURE;
            }

            $fileSize = round(File::size($fullPath) / 1024 / 1024, 2);
            $this->info("✅ Base de datos exportada exitosamente: {$filename} ({$fileSize} MB)");
            $this->newLine();

            if ($exportOnly) {
                $this->info("📁 Archivo guardado en: {$fullPath}");
                return Command::SUCCESS;
            }
        }

        // PASO 2: IMPORTAR a MySQL local
        if (!$exportOnly) {
            $this->info('🔵 PASO 2: Importando a base de datos MySQL local...');
            $this->newLine();

            if (!File::exists($fullPath)) {
                $this->error("❌ El archivo {$filename} no existe en {$backupPath}");
                return Command::FAILURE;
            }

            // Credenciales locales
            $localHost = '127.0.0.1';
            $localPort = 3306;
            $localDatabase = 'posgrado_intranet';
            $localUsername = 'root';
            $localPassword = 'admin';

            $this->table(
                ['Parámetro', 'Valor'],
                [
                    ['Host destino', $localHost],
                    ['Puerto', $localPort],
                    ['Base de datos', $localDatabase],
                    ['Usuario', $localUsername],
                ]
            );

            // Verificar conexión local
            try {
                $this->info('Verificando conexión a MySQL local...');
                $testConnection = new \PDO(
                    "mysql:host={$localHost};port={$localPort}",
                    $localUsername,
                    $localPassword
                );
                $this->info('✅ Conexión exitosa a MySQL local');
            } catch (\PDOException $e) {
                $this->error('❌ No se pudo conectar a MySQL local: ' . $e->getMessage());
                $this->newLine();
                $this->warn('💡 Verifica que MySQL esté corriendo y las credenciales sean correctas.');
                return Command::FAILURE;
            }

            // Crear base de datos si no existe
            $this->info("Creando base de datos '{$localDatabase}' si no existe...");
            try {
                $testConnection->exec("CREATE DATABASE IF NOT EXISTS `{$localDatabase}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->info("✅ Base de datos '{$localDatabase}' lista");
            } catch (\PDOException $e) {
                $this->error('❌ Error al crear la base de datos: ' . $e->getMessage());
                return Command::FAILURE;
            }

            // Importar el dump SQL
            $this->info('Importando datos (esto puede tomar varios minutos)...');
            $this->newLine();

            // Leer el archivo SQL y reemplazar el nombre de la base de datos
            $sqlContent = File::get($fullPath);
            $awsDatabase = env('DB_DATABASE');
            
            // Reemplazar referencias a la base de datos antigua con la nueva
            $sqlContent = str_replace(
                ["CREATE DATABASE `{$awsDatabase}`", "USE `{$awsDatabase}`"],
                ["CREATE DATABASE IF NOT EXISTS `{$localDatabase}`", "USE `{$localDatabase}`"],
                $sqlContent
            );

            // Guardar archivo temporal modificado
            $tempFile = $backupPath . '/temp_import.sql';
            File::put($tempFile, $sqlContent);

            $mysqlCmd = sprintf(
                'mysql --host=%s --port=%d --user=%s --password=%s < %s 2>&1',
                escapeshellarg($localHost),
                $localPort,
                escapeshellarg($localUsername),
                escapeshellarg($localPassword),
                escapeshellarg($tempFile)
            );

            exec($mysqlCmd, $output, $returnCode);

            // Eliminar archivo temporal
            if (File::exists($tempFile)) {
                File::delete($tempFile);
            }

            if ($returnCode !== 0) {
                $this->error('❌ Error al importar la base de datos.');
                $this->error('Salida: ' . implode("\n", $output));
                return Command::FAILURE;
            }

            $this->info('✅ Datos importados exitosamente a MySQL local');
            $this->newLine();

            // Verificar tablas importadas
            try {
                $pdo = new \PDO(
                    "mysql:host={$localHost};port={$localPort};dbname={$localDatabase}",
                    $localUsername,
                    $localPassword
                );
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                
                $this->info("📊 Total de tablas importadas: " . count($tables));
                $this->newLine();
                
                // Mostrar algunas tablas importantes
                $importantTables = ['users', 'courses', 'enrollments', 'evaluations', 'forum_topics'];
                $foundTables = array_intersect($tables, $importantTables);
                
                if (count($foundTables) > 0) {
                    $this->info('✅ Tablas principales verificadas:');
                    foreach ($foundTables as $table) {
                        $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
                        $count = $countStmt->fetchColumn();
                        $this->line("  • {$table}: {$count} registros");
                    }
                }
            } catch (\PDOException $e) {
                $this->warn('⚠️  No se pudo verificar las tablas: ' . $e->getMessage());
            }

            $this->newLine();
        }

        // PASO 3: Actualizar archivo .env
        $this->info('🔵 PASO 3: ¿Actualizar archivo .env con las credenciales locales?');
        
        if ($this->confirm('¿Deseas actualizar el archivo .env ahora?', true)) {
            // Hacer backup del .env actual
            $envPath = base_path('.env');
            $envBackupPath = base_path('.env.backup.' . date('Ymd_His'));
            
            if (File::exists($envPath)) {
                File::copy($envPath, $envBackupPath);
                $this->info("✅ Backup del .env creado: " . basename($envBackupPath));
            }

            // Actualizar .env
            $envContent = File::get($envPath);
            
            $envContent = preg_replace(
                '/DB_HOST=.+/',
                'DB_HOST=127.0.0.1',
                $envContent
            );
            $envContent = preg_replace(
                '/DB_PORT=.+/',
                'DB_PORT=3306',
                $envContent
            );
            $envContent = preg_replace(
                '/DB_DATABASE=.+/',
                'DB_DATABASE=posgrado_intranet',
                $envContent
            );
            $envContent = preg_replace(
                '/DB_USERNAME=.+/',
                'DB_USERNAME=root',
                $envContent
            );
            $envContent = preg_replace(
                '/DB_PASSWORD=.+/',
                'DB_PASSWORD=admin',
                $envContent
            );

            File::put($envPath, $envContent);
            
            $this->info('✅ Archivo .env actualizado con credenciales locales');
            $this->newLine();
            $this->warn('⚠️  Recuerda ejecutar: php artisan config:clear');
        }

        $this->newLine();
        $this->info('🎉 ¡Migración completada exitosamente!');
        $this->newLine();
        $this->table(
            ['Siguiente paso', 'Comando'],
            [
                ['Limpiar configuración', 'php artisan config:clear'],
                ['Limpiar caché', 'php artisan cache:clear'],
                ['Verificar conexión', 'php artisan db:show'],
                ['Probar aplicación', 'php artisan serve'],
            ]
        );

        return Command::SUCCESS;
    }
}
