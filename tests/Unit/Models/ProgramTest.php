<?php

namespace Tests\Unit\Models;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    private function makeProgram(array $attrs = []): Program
    {
        return Program::factory()->make(array_merge([
            'name'               => 'Maestría en Educación',
            'code'               => 'MAE-001',
            'degree_type'        => 'maestria',
            'duration_semesters' => 4,
            'has_propedeutic'    => false,
            'total_credits'      => 60,
            'status'             => 'active',
        ], $attrs));
    }

    public function test_degree_type_label_maestria(): void
    {
        $program = $this->makeProgram(['degree_type' => 'maestria']);

        $this->assertEquals('Maestría', $program->degree_type_label);
    }

    public function test_degree_type_label_doctorado(): void
    {
        $program = $this->makeProgram(['degree_type' => 'doctorado']);

        $this->assertEquals('Doctorado', $program->degree_type_label);
    }

    public function test_degree_type_label_segunda_especialidad(): void
    {
        $program = $this->makeProgram(['degree_type' => 'segunda_especialidad']);

        $this->assertEquals('Segunda Especialidad', $program->degree_type_label);
    }

    public function test_duration_years_whole_number(): void
    {
        $program = $this->makeProgram(['duration_semesters' => 4]);

        $this->assertEquals('2 años', $program->duration_years);
    }

    public function test_duration_years_one_year(): void
    {
        $program = $this->makeProgram(['duration_semesters' => 2]);

        $this->assertEquals('1 año', $program->duration_years);
    }

    public function test_status_label_active(): void
    {
        $program = $this->makeProgram(['status' => 'active']);

        $this->assertEquals('Activo', $program->status_label);
        $this->assertTrue($program->is_active);
    }

    public function test_status_label_inactive(): void
    {
        $program = $this->makeProgram(['status' => 'inactive']);

        $this->assertEquals('Inactivo', $program->status_label);
        $this->assertFalse($program->is_active);
    }

    public function test_active_scope(): void
    {
        Program::factory()->create(['name' => 'Activo', 'code' => 'ACT-1', 'degree_type' => 'maestria', 'duration_semesters' => 4, 'status' => 'active']);
        Program::factory()->create(['name' => 'Inactivo', 'code' => 'INA-1', 'degree_type' => 'maestria', 'duration_semesters' => 4, 'status' => 'inactive']);

        $active = Program::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Activo', $active->first()->name);
    }

    public function test_coordinator_relationship(): void
    {
        $docente = User::factory()->docente()->create();
        $program = Program::factory()->create([
            'name'               => 'Test',
            'code'               => 'TST-1',
            'degree_type'        => 'maestria',
            'duration_semesters' => 4,
            'status'             => 'active',
            'coordinator_id'     => $docente->id,
        ]);

        $this->assertEquals($docente->id, $program->coordinator->id);
    }
}
