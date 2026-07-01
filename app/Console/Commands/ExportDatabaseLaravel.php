<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ExportDatabaseLaravel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export-laravel {--file=database_backup.sql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporta la base de datos usando Laravel (sin necesidad de mysqldump)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->option('file');
        $backupPath = storage_path('app/backups');
        $fullPath = $backupPath . '/' . $filename;

        // Crear directorio de backups
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $this->info('🔵 Exportando base de datos usando Laravel...');
        $this->newLine();

        try {
            // Obtener información de la conexión
            $dbName = DB::getDatabaseName();
            $this->info("Base de datos: {$dbName}");
            
            // Iniciar archivo SQL
            $sql = "-- PS-EDU Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: {$dbName}\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Obtener todas las tablas
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $dbName;
            
            $this->info("Total de tablas: " . count($tables));
            $this->newLine();

            $bar = $this->output->createProgressBar(count($tables));
            $bar->start();

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Estructura de la tabla
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sql .= "\n-- Estructura de tabla: {$tableName}\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable->{'Create Table'} . ";\n\n";

                // Datos de la tabla
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $sql .= "-- Datos de tabla: {$tableName}\n";
                    
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        
                        $columns = implode('`, `', array_keys((array)$row));
                        $valuesStr = implode(', ', $values);
                        
                        $sql .= "INSERT INTO `{$tableName}` (`{$columns}`) VALUES ({$valuesStr});\n";
                    }
                    $sql .= "\n";
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->newLine();

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // Guardar archivo
            File::put($fullPath, $sql);

            $fileSize = round(File::size($fullPath) / 1024 / 1024, 2);
            $this->info("✅ Base de datos exportada exitosamente: {$filename} ({$fileSize} MB)");
            $this->info("📁 Ubicación: {$fullPath}");
            
            $this->newLine();
            $this->info('📊 Resumen de exportación:');
            $this->table(
                ['Tabla', 'Registros'],
                collect($tables)->map(function ($table) use ($dbName, $tableKey) {
                    $tableName = $table->$tableKey;
                    $count = DB::table($tableName)->count();
                    return [$tableName, $count];
                })->take(10)->toArray()
            );

            if (count($tables) > 10) {
                $this->info('... y ' . (count($tables) - 10) . ' tablas más');
            }

            $this->newLine();
            $this->info('✅ Ahora puedes importar este archivo a tu base de datos local:');
            $this->line("mysql -h 127.0.0.1 -u root -padmin posgrado_intranet < {$fullPath}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error al exportar la base de datos: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
