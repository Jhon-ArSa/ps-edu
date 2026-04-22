<?php

namespace Tests\Feature\Alumno;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumno_can_access_dashboard(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)
            ->get('/alumno/dashboard')
            ->assertStatus(200);
    }

    public function test_alumno_can_access_courses_list(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)
            ->get('/alumno/mis-cursos')
            ->assertStatus(200);
    }

    public function test_alumno_can_access_intranet(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)
            ->get('/alumno/intranet')
            ->assertStatus(200);
    }

    public function test_alumno_can_access_support(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)
            ->get('/alumno/soporte')
            ->assertStatus(200);
    }

    public function test_alumno_root_redirects_to_dashboard(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)
            ->get('/')
            ->assertRedirect('/alumno/dashboard');
    }
}
