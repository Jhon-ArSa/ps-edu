<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PrepareProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:prepare-production {--force : Forzar sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preparar la base de datos para producción (limpia todo y crea solo el admin)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════════════════════════╗');
        $this->info('║                                                                              ║');
        $this->info('║                  PREPARAR BASE DE DATOS PARA PRODUCCIÓN                      ║');
        $this->info('║                                                                              ║');
        $this->info('╚══════════════════════════════════════════════════════════════════════════════╝');
        $this->info('');

        // Advertencia
        $this->warn('⚠️  ADVERTENCIA: Esta operación eliminará TODOS los datos de la base de datos.');
        $this->warn('⚠️  Solo se creará el administrador principal.');
        $this->info('');

        // Confirmación
        if (!$this->option('force')) {
            if (!$this->confirm('¿Está seguro de que desea continuar?', false)) {
                $this->error('Operación cancelada.');
                return Command::FAILURE;
            }
        }

        $this->info('');
        $this->info('Iniciando limpieza de base de datos...');
        $this->info('');

        // Paso 1: Limpiar cache
        $this->info('⏳ Paso 1/4: Limpiando cache...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('✓ Cache limpiado');
        $this->info('');

        // Paso 2: Resetear base de datos
        $this->info('⏳ Paso 2/4: Reseteando base de datos...');
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->info('✓ Base de datos reseteada');
        $this->info('');

        // Paso 3: Ejecutar migraciones de índices de rendimiento
        $this->info('⏳ Paso 3/4: Aplicando índices de rendimiento...');
        Artisan::call('migrate', ['--force' => true]);
        $this->info('✓ Índices aplicados');
        $this->info('');

        // Paso 4: Crear administrador principal
        $this->info('⏳ Paso 4/4: Creando administrador principal...');
        Artisan::call('db:seed', [
            '--class' => 'ProductionSeeder',
            '--force' => true,
        ]);
        $this->info('');

        // Resumen
        $this->info('╔══════════════════════════════════════════════════════════════════════════════╗');
        $this->info('║                                                                              ║');
        $this->info('║                    ✅ BASE DE DATOS LISTA PARA PRODUCCIÓN                    ║');
        $this->info('║                                                                              ║');
        $this->info('╚══════════════════════════════════════════════════════════════════════════════╝');
        $this->info('');
        $this->info('Credenciales del administrador:');
        $this->info('  Email: upeducacionuncp@gmail.com');
        $this->info('  Contraseña: Admin2024!');
        $this->info('');
        $this->info('Próximos pasos:');
        $this->info('  1. Iniciar sesión en el sistema');
        $this->info('  2. Crear programas académicos');
        $this->info('  3. Crear semestres');
        $this->info('  4. Importar usuarios (docentes y alumnos)');
        $this->info('  5. Asignar cursos a docentes');
        $this->info('');

        return Command::SUCCESS;
    }
}
