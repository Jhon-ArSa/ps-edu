<?php

namespace Tests\Feature\Admin;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_announcements(): void
    {
        Announcement::factory()->count(3)->create(['author_id' => $this->admin->id, 'target_role' => 'all']);

        $this->actingAs($this->admin)
            ->get('/admin/announcements')
            ->assertStatus(200);
    }

    public function test_admin_can_see_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/announcements/create')
            ->assertStatus(200);
    }

    public function test_admin_can_create_announcement(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/announcements', [
                'title'       => 'Comunicado de prueba',
                'content'     => 'Contenido del comunicado de prueba.',
                'target_role' => 'all',
                'published_at' => now()->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect('/admin/announcements');

        $this->assertDatabaseHas('announcements', [
            'title'     => 'Comunicado de prueba',
            'author_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_create_draft_announcement(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/announcements', [
                'title'       => 'Borrador',
                'content'     => 'Contenido borrador.',
                'target_role' => 'docente',
            ])
            ->assertRedirect('/admin/announcements');

        $this->assertDatabaseHas('announcements', [
            'title'        => 'Borrador',
            'published_at' => null,
        ]);
    }

    public function test_create_announcement_requires_title(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/announcements', [
                'content'     => 'Sin título',
                'target_role' => 'all',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_create_announcement_requires_content(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/announcements', [
                'title'       => 'Sin contenido',
                'target_role' => 'all',
            ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_admin_can_edit_announcement(): void
    {
        $announcement = Announcement::factory()->create([
            'author_id'   => $this->admin->id,
            'target_role' => 'all',
        ]);

        $this->actingAs($this->admin)
            ->get("/admin/announcements/{$announcement->id}/edit")
            ->assertStatus(200);
    }

    public function test_admin_can_update_announcement(): void
    {
        $announcement = Announcement::factory()->create([
            'author_id'   => $this->admin->id,
            'target_role' => 'all',
        ]);

        $this->actingAs($this->admin)
            ->put("/admin/announcements/{$announcement->id}", [
                'title'       => 'Título actualizado',
                'content'     => 'Contenido actualizado.',
                'target_role' => 'docente',
            ])
            ->assertRedirect('/admin/announcements');

        $this->assertDatabaseHas('announcements', [
            'id'    => $announcement->id,
            'title' => 'Título actualizado',
        ]);
    }

    public function test_admin_can_delete_announcement(): void
    {
        $announcement = Announcement::factory()->create([
            'author_id'   => $this->admin->id,
            'target_role' => 'all',
        ]);

        $this->actingAs($this->admin)
            ->delete("/admin/announcements/{$announcement->id}")
            ->assertRedirect('/admin/announcements');

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_non_admin_cannot_manage_announcements(): void
    {
        $docente = User::factory()->docente()->create();

        $this->actingAs($docente)
            ->get('/admin/announcements')
            ->assertStatus(403);
    }
}
