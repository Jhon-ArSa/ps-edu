<?php

namespace Tests\Feature\Admin;

use App\Models\AlumnoProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_visible_users_table_with_current_filters(): void
    {
        $admin = $this->makeUser([
            'role' => 'admin',
            'status' => true,
        ]);

        $targetUser = $this->makeUser([
            'name' => 'Docente Exportable',
            'email' => 'docente.export@example.com',
            'role' => 'docente',
            'status' => true,
        ]);

        $otherUser = $this->makeUser([
            'name' => 'Docente Inactivo',
            'email' => 'docente.inactivo@example.com',
            'role' => 'docente',
            'status' => false,
        ]);

        $this->makeUser([
            'name' => 'Alumno No Exportable',
            'email' => 'alumno.noexport@example.com',
            'role' => 'alumno',
            'status' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export', [
                'format' => 'csv',
                'search' => 'Exportable',
                'role' => 'docente',
                'status' => '1',
            ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString(
            '.csv',
            (string) $response->headers->get('content-disposition')
        );

        $filteredPdf = $this->actingAs($admin)
            ->get(route('admin.users.export', [
                'format' => 'pdf',
                'search' => 'Exportable',
                'role' => 'docente',
                'status' => '1',
            ]));

        $filteredPdf->assertOk();
        $filteredPdf->assertSee($targetUser->name);
        $filteredPdf->assertDontSee($otherUser->name);
        $filteredPdf->assertDontSee('Alumno No Exportable');
    }

    public function test_admin_pdf_export_shows_filtered_users(): void
    {
        $admin = $this->makeUser([
            'role' => 'admin',
            'status' => true,
        ]);

        $docente = $this->makeUser([
            'name' => 'Docente PDF',
            'email' => 'docente.pdf@example.com',
            'role' => 'docente',
            'status' => true,
        ]);

        $alumno = $this->makeUser([
            'name' => 'Alumno PDF',
            'email' => 'alumno.pdf@example.com',
            'role' => 'alumno',
            'status' => true,
        ]);

        AlumnoProfile::create([
            'user_id' => $alumno->id,
            'code' => 'ALU-PDF',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export', [
                'format' => 'pdf',
                'role' => 'docente',
            ]));

        $response->assertOk();
        $response->assertSee('Lista de usuarios (resultados filtrados)');
        $response->assertSee($docente->name);
        $response->assertDontSee($alumno->name);
    }

    public function test_csv_export_includes_all_filtered_results_not_just_current_page(): void
    {
        $admin = $this->makeUser([
            'role' => 'admin',
            'status' => true,
        ]);

        for ($i = 1; $i <= 48; $i++) {
            $this->makeUser([
                'name' => "Docente Lote {$i}",
                'email' => "docente.lote{$i}@example.com",
                'role' => 'docente',
                'status' => true,
            ]);
        }

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export', [
                'format' => 'csv',
                'search' => 'Docente Lote',
                'role' => 'docente',
                'status' => '1',
                'page' => 1,
            ]));

        $response->assertOk();

        $csvContent = $response->streamedContent();
        $lines = preg_split('/\r\n|\r|\n/', trim($csvContent)) ?: [];

        // 1 encabezado + 48 filas
        $this->assertCount(49, $lines);
        $this->assertStringContainsString('Docente Lote 1', $csvContent);
        $this->assertStringContainsString('Docente Lote 48', $csvContent);
    }

    private function makeUser(array $attributes): User
    {
        return User::create(array_merge([
            'name' => 'Usuario Test',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'alumno',
            'status' => true,
        ], $attributes));
    }
}
