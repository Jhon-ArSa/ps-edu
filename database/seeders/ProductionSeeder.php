<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Seeder para producción - Solo crea el administrador principal
     */
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════════════
        // ADMINISTRADOR PRINCIPAL
        // ══════════════════════════════════════════════════════════════════
        User::create([
            'name'     => 'Administrador Principal',
            'email'    => 'upeducacionuncp@gmail.com',
            'password' => Hash::make('Admin2024!'),
            'role'     => 'admin',
            'status'   => true,
            'dni'      => null,
            'phone'    => null,
        ]);

        $this->command->info('✓ Administrador principal creado');
        $this->command->info('  Email: upeducacionuncp@gmail.com');
        $this->command->info('  Contraseña: Admin2024!');
        $this->command->line('');

        // ══════════════════════════════════════════════════════════════════
        // CONFIGURACIONES DE LA INSTITUCIÓN
        // ══════════════════════════════════════════════════════════════════
        Setting::set('institution_name', 'Facultad de Educación');
        Setting::set('institution_acronym', 'FAEDU');
        Setting::set('institution_subtitle', 'Posgrado');
        Setting::set('institution_year', date('Y'));

        $this->command->info('✓ Configuraciones de la institución creadas');
        $this->command->line('');
        $this->command->info('══════════════════════════════════════════════════════════════');
        $this->command->info('  BASE DE DATOS LISTA PARA PRODUCCIÓN');
        $this->command->info('══════════════════════════════════════════════════════════════');
        $this->command->line('');
        $this->command->info('Próximos pasos:');
        $this->command->info('1. Iniciar sesión con: upeducacionuncp@gmail.com');
        $this->command->info('2. Crear programas académicos');
        $this->command->info('3. Crear semestres');
        $this->command->info('4. Importar usuarios (docentes y alumnos)');
        $this->command->info('5. Asignar cursos a docentes');
        $this->command->line('');
    }
}
