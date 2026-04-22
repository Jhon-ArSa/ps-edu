<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // ── Rutas Admin ──────────────────────────────────────────────────────

    public function test_guest_cannot_access_admin_routes(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
    }

    public function test_docente_cannot_access_admin_routes(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_alumno_cannot_access_admin_routes(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)->get('/admin/dashboard')->assertStatus(403);
    }

    // ── Rutas Docente ────────────────────────────────────────────────────

    public function test_guest_cannot_access_docente_routes(): void
    {
        $this->get('/docente/dashboard')->assertRedirect('/login');
    }

    public function test_docente_can_access_docente_dashboard(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)->get('/docente/dashboard')->assertStatus(200);
    }

    public function test_admin_cannot_access_docente_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/docente/dashboard')->assertStatus(403);
    }

    public function test_alumno_cannot_access_docente_routes(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)->get('/docente/dashboard')->assertStatus(403);
    }

    // ── Rutas Alumno ─────────────────────────────────────────────────────

    public function test_guest_cannot_access_alumno_routes(): void
    {
        $this->get('/alumno/dashboard')->assertRedirect('/login');
    }

    public function test_alumno_can_access_alumno_dashboard(): void
    {
        $alumno = User::factory()->alumno()->create();

        $this->actingAs($alumno)->get('/alumno/dashboard')->assertStatus(200);
    }

    public function test_admin_cannot_access_alumno_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/alumno/dashboard')->assertStatus(403);
    }

    public function test_docente_cannot_access_alumno_routes(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)->get('/alumno/dashboard')->assertStatus(403);
    }

    // ── Rutas Profile (cualquier usuario autenticado) ────────────────────

    public function test_guest_cannot_access_profile(): void
    {
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_any_authenticated_user_can_access_profile(): void
    {
        foreach (['admin', 'docente', 'alumno'] as $role) {
            $user = User::factory()->{$role}()->create();
            $this->actingAs($user)->get('/profile')->assertStatus(200);
        }
    }
}
