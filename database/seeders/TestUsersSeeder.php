<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@test.com'], [
            'name'     => 'Administrador Prueba',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => true,
        ]);

        User::updateOrCreate(['email' => 'docente@test.com'], [
            'name'     => 'Docente Prueba',
            'email'    => 'docente@test.com',
            'password' => Hash::make('password'),
            'role'     => 'docente',
            'status'   => true,
        ]);

        User::updateOrCreate(['email' => 'alumno@test.com'], [
            'name'     => 'Alumno Prueba',
            'email'    => 'alumno@test.com',
            'password' => Hash::make('password'),
            'role'     => 'alumno',
            'status'   => true,
        ]);

        $this->command->info('✅ Usuarios de prueba creados:');
        $this->command->line('   admin@test.com   → contraseña: password  (rol: admin)');
        $this->command->line('   docente@test.com → contraseña: password  (rol: docente)');
        $this->command->line('   alumno@test.com  → contraseña: password  (rol: alumno)');
    }
}
