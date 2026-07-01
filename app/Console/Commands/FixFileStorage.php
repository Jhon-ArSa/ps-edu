<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class FixFileStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix 
                            {--dry-run : Mostrar qué se haría sin ejecutar los cambios}
                            {--force : Forzar la ejecución sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra archivos de public/ a storage/app/public/ y actualiza referencias en la BD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔧 Corrección del Sistema de Almacenamiento de Archivos');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 Modo DRY-RUN: No se realizarán cambios');
            $this->newLine();
        }

        // Directorios a migrar
        $directories = [
            'materials' => 'Materiales educativos',
            'tasks' => 'Archivos de tareas',
            'evaluations' => 'Archivos de evaluaciones',
            'evaluation-answers' => 'Respuestas de evaluaciones',
            'submissions' => 'Entregas de alumnos',
            'announcements' => 'Imágenes de anuncios',
            'avatars' => 'Avatares de usuarios',
        ];

        $publicPath = public_path();
        $storagePath = storage_path('app/public');

        // Verificar que storage/app/public existe
        if (!File::exists($storagePath)) {
            $this->info('📁 Creando directorio: storage/app/public');
            if (!$dryRun) {
                File::makeDirectory($storagePath, 0755, true);
            }
        }

        $totalFiles = 0;
        $movedFiles = 0;
        $errors = 0;

        foreach ($directories as $dir => $description) {
            $sourcePath = $publicPath . '/' . $dir;
            $destPath = $storagePath . '/' . $dir;

            if (!File::exists($sourcePath)) {
                $this->line("⏭️  Omitiendo {$description}: {$dir}/ no existe");
                continue;
            }

            $files = File::allFiles($sourcePath);
            $count = count($files);
            $totalFiles += $count;

            if ($count === 0) {
                $this->line("⏭️  Omitiendo {$description}: {$dir}/ está vacío");
                continue;
            }

            $this->info("📦 {$description}: {$count} archivo(s) encontrado(s)");

            if (!$dryRun) {
                if (!File::exists($destPath)) {
                    File::makeDirectory($destPath, 0755, true);
                }

                try {
                    // Mover directorio completo
                    File::copyDirectory($sourcePath, $destPath);
                    $movedFiles += $count;
                    $this->line("  ✅ Movidos {$count} archivo(s) a storage/app/public/{$dir}/");
                } catch (\Exception $e) {
                    $this->error("  ❌ Error al mover {$dir}/: " . $e->getMessage());
                    $errors++;
                }
            } else {
                $this->line("  🔍 Se moverían {$count} archivo(s) a storage/app/public/{$dir}/");
            }
        }

        $this->newLine();

        // Actualizar referencias en la base de datos
        $this->info('🔄 Actualizando referencias en la base de datos...');
        $this->newLine();

        $tables = [
            'materials' => ['file_path'],
            'task_files' => ['file_path'],
            'evaluations' => ['file_path'],
            'evaluation_attempts' => ['file_upload'],
            'submission_files' => ['file_path'],
            'announcements' => ['image_path'],
            'users' => ['avatar'],
        ];

        $updatedRecords = 0;

        foreach ($tables as $table => $columns) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $this->line("⏭️  Tabla {$table} no existe");
                continue;
            }

            foreach ($columns as $column) {
                if (!DB::getSchemaBuilder()->hasColumn($table, $column)) {
                    continue;
                }

                // Contar registros que necesitan actualización
                $count = DB::table($table)
                    ->whereNotNull($column)
                    ->where($column, '!=', '')
                    ->where($column, 'not like', 'storage/%')
                    ->count();

                if ($count > 0) {
                    $this->info("📝 Tabla {$table}.{$column}: {$count} registro(s) a actualizar");

                    if (!$dryRun) {
                        // Actualizar rutas: materials/1/file.pdf → storage/materials/1/file.pdf
                        foreach ($directories as $dir => $desc) {
                            DB::table($table)
                                ->whereNotNull($column)
                                ->where($column, 'like', "{$dir}/%")
                                ->update([
                                    $column => DB::raw("CONCAT('storage/', {$column})")
                                ]);
                        }

                        $updatedRecords += $count;
                        $this->line("  ✅ Actualizado");
                    } else {
                        $this->line("  🔍 Se actualizarían {$count} registro(s)");
                    }
                }
            }
        }

        $this->newLine();

        // Crear/Verificar symlink
        $this->info('🔗 Verificando symlink storage...');
        
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');

        if (File::exists($linkPath)) {
            if (is_link($linkPath)) {
                $this->info('✅ Symlink ya existe: public/storage → storage/app/public');
            } else {
                $this->warn('⚠️  public/storage existe pero NO es un symlink');
                
                if (!$dryRun && ($force || $this->confirm('¿Eliminar y recrear como symlink?', false))) {
                    File::deleteDirectory($linkPath);
                    if (windows_os()) {
                        $this->warn('⚠️  En Windows, ejecuta como administrador: php artisan storage:link');
                    } else {
                        File::link($targetPath, $linkPath);
                        $this->info('✅ Symlink creado exitosamente');
                    }
                }
            }
        } else {
            if (!$dryRun) {
                $this->info('Creando symlink...');
                if (windows_os()) {
                    $this->warn('⚠️  En Windows, ejecuta como administrador: php artisan storage:link');
                } else {
                    File::link($targetPath, $linkPath);
                    $this->info('✅ Symlink creado exitosamente');
                }
            } else {
                $this->line('🔍 Se crearía el symlink: public/storage → storage/app/public');
            }
        }

        $this->newLine();

        // Resumen
        if ($dryRun) {
            $this->info('📊 RESUMEN (DRY-RUN):');
            $this->line("  • Archivos a mover: {$totalFiles}");
            $this->line("  • Registros de BD a actualizar: (contados arriba)");
            $this->newLine();
            $this->info('Ejecuta sin --dry-run para aplicar los cambios');
        } else {
            $this->info('🎉 PROCESO COMPLETADO');
            $this->newLine();
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Archivos movidos', $movedFiles],
                    ['Registros actualizados', $updatedRecords],
                    ['Errores', $errors],
                ]
            );

            if ($errors === 0 && $movedFiles > 0) {
                $this->newLine();
                $this->info('✅ Migración completada exitosamente');
                $this->newLine();
                $this->warn('⚠️  IMPORTANTE: Puedes eliminar los directorios antiguos de public/ si todo funciona:');
                foreach ($directories as $dir => $desc) {
                    if (File::exists($publicPath . '/' . $dir)) {
                        $this->line("  rm -rf public/{$dir}/");
                    }
                }
            }
        }

        $this->newLine();
        $this->info('📚 Siguientes pasos:');
        $this->line('1. Ejecuta: php artisan storage:link (si aún no lo hiciste)');
        $this->line('2. Prueba subir un archivo nuevo');
        $this->line('3. Verifica que los archivos existentes se vean correctamente');
        $this->line('4. Si todo funciona, elimina los directorios antiguos de public/');

        return $errors === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
