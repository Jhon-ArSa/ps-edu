<?php

namespace Tests\Feature\Docente;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_docente_can_access_dashboard(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)
            ->get('/docente/dashboard')
            ->assertStatus(200);
    }

    public function test_docente_can_access_courses_list(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)
            ->get('/docente/cursos')
            ->assertStatus(200);
    }

    public function test_docente_can_access_intranet(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)
            ->get('/docente/intranet')
            ->assertStatus(200);
    }

    public function test_docente_can_access_support(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)
            ->get('/docente/soporte')
            ->assertStatus(200);
    }

    public function test_docente_root_redirects_to_dashboard(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)
            ->get('/')
            ->assertRedirect('/docente/dashboard');
    }
}
