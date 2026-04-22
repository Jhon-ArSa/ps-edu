<?php

namespace Tests\Feature\Admin;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_programs(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/programs')
            ->assertStatus(200);
    }

    public function test_admin_can_see_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/programs/create')
            ->assertStatus(200);
    }

    public function test_admin_can_create_program(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/programs', [
                'name'               => 'Maestría en Administración',
                'code'               => 'MAD-001',
                'degree_type'        => 'maestria',
                'duration_semesters' => 4,
                'total_credits'      => 60,
                'status'             => 'active',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('programs', [
            'name' => 'Maestría en Administración',
            'code' => 'MAD-001',
        ]);
    }

    public function test_create_program_requires_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/programs', [
                'code'               => 'TST-001',
                'degree_type'        => 'maestria',
                'duration_semesters' => 4,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_create_program_requires_unique_code(): void
    {
        Program::factory()->create(['code' => 'DUP-001', 'name' => 'Original', 'degree_type' => 'maestria', 'duration_semesters' => 4]);

        $response = $this->actingAs($this->admin)
            ->post('/admin/programs', [
                'name'               => 'Duplicado',
                'code'               => 'DUP-001',
                'degree_type'        => 'maestria',
                'duration_semesters' => 4,
            ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_admin_can_edit_program(): void
    {
        $program = Program::factory()->create();

        $this->actingAs($this->admin)
            ->get("/admin/programs/{$program->id}/edit")
            ->assertStatus(200);
    }

    public function test_admin_can_update_program(): void
    {
        $program = Program::factory()->create(['name' => 'Nombre original', 'code' => 'ORI-001', 'degree_type' => 'maestria', 'duration_semesters' => 4]);

        $this->actingAs($this->admin)
            ->put("/admin/programs/{$program->id}", [
                'name'               => 'Nombre actualizado',
                'code'               => 'ORI-001',
                'degree_type'        => 'maestria',
                'duration_semesters' => 4,
                'total_credits'      => 60,
                'status'             => 'active',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('programs', [
            'id'   => $program->id,
            'name' => 'Nombre actualizado',
        ]);
    }

    public function test_admin_can_assign_coordinator_to_program(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($this->admin)
            ->post('/admin/programs', [
                'name'               => 'Con Coordinador',
                'code'               => 'COO-001',
                'degree_type'        => 'maestria',
                'duration_semesters' => 4,
                'total_credits'      => 60,
                'status'             => 'active',
                'coordinator_id'     => $docente->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('programs', [
            'name'           => 'Con Coordinador',
            'coordinator_id' => $docente->id,
        ]);
    }

    public function test_non_admin_cannot_manage_programs(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)
            ->get('/admin/programs')
            ->assertStatus(403);
    }
}
