<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\DocenteProfile;
use App\Models\AlumnoProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@facultad.edu.pe',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => true,
        ]);

        // Docente de prueba
        $docente = User::create([
            'name'     => 'Dr. Juan Pérez',
            'email'    => 'docente@facultad.edu.pe',
            'password' => Hash::make('password'),
            'role'     => 'docente',
            'dni'      => '12345678',
            'status'   => true,
        ]);
        DocenteProfile::create([
            'user_id'          => $docente->id,
            'title'            => 'Dr.',
            'degree'           => 'Doctor en Ciencias de la Educación',
            'specialty'        => 'Didáctica y Currículum',
            'category'         => 'Principal',
            'years_of_service' => 15,
            'bio'              => 'Especialista en ciencias de la educación con más de 15 años de experiencia docente.',
        ]);

        // Alumno de prueba
        $alumno = User::create([
            'name'     => 'María García López',
            'email'    => 'alumno@facultad.edu.pe',
            'password' => Hash::make('password'),
            'role'     => 'alumno',
            'dni'      => '87654321',
            'status'   => true,
        ]);
        AlumnoProfile::create([
            'user_id'        => $alumno->id,
            'code'           => '2025-001',
            'promotion_year' => 2025,
            'program'        => 'Maestría en Ciencias de la Educación',
        ]);

        // Configuraciones de la institución
        Setting::set('institution_name', 'Facultad de Educación');
        Setting::set('institution_acronym', 'FAEDU');
        Setting::set('institution_subtitle', 'Posgrado');
        Setting::set('institution_year', date('Y'));

        // Programas de posgrado con menciones y plan de estudios
        $this->call(ProgramSeeder::class);

        // Semestres, docentes adicionales, alumnos y cursos
        $this->call(CourseSeeder::class);
    }
}
