<?php

namespace Tests\Unit\Models;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_published_when_published_at_is_past(): void
    {
        $announcement = Announcement::factory()->make([
            'published_at' => now()->subDay(),
        ]);

        $this->assertTrue($announcement->isPublished());
    }

    public function test_is_not_published_when_published_at_is_future(): void
    {
        $announcement = Announcement::factory()->make([
            'published_at' => now()->addDay(),
        ]);

        $this->assertFalse($announcement->isPublished());
    }

    public function test_is_not_published_when_published_at_is_null(): void
    {
        $announcement = Announcement::factory()->make([
            'published_at' => null,
        ]);

        $this->assertFalse($announcement->isPublished());
    }

    public function test_published_scope_returns_only_published(): void
    {
        $author = User::factory()->admin()->create();

        Announcement::factory()->create([
            'title'        => 'Publicado',
            'author_id'    => $author->id,
            'published_at' => now()->subHour(),
            'target_role'  => 'all',
        ]);
        Announcement::factory()->create([
            'title'        => 'Borrador',
            'author_id'    => $author->id,
            'published_at' => null,
            'target_role'  => 'all',
        ]);
        Announcement::factory()->create([
            'title'        => 'Futuro',
            'author_id'    => $author->id,
            'published_at' => now()->addDay(),
            'target_role'  => 'all',
        ]);

        $published = Announcement::published()->get();

        $this->assertCount(1, $published);
        $this->assertEquals('Publicado', $published->first()->title);
    }

    public function test_for_role_scope_returns_all_and_specific_role(): void
    {
        $author = User::factory()->admin()->create();

        Announcement::factory()->create(['title' => 'Para todos', 'author_id' => $author->id, 'target_role' => 'all', 'published_at' => now()]);
        Announcement::factory()->create(['title' => 'Para docentes', 'author_id' => $author->id, 'target_role' => 'docente', 'published_at' => now()]);
        Announcement::factory()->create(['title' => 'Para alumnos', 'author_id' => $author->id, 'target_role' => 'alumno', 'published_at' => now()]);

        $forDocente = Announcement::forRole('docente')->get();

        $this->assertCount(2, $forDocente);
        $titles = $forDocente->pluck('title')->toArray();
        $this->assertContains('Para todos', $titles);
        $this->assertContains('Para docentes', $titles);
        $this->assertNotContains('Para alumnos', $titles);
    }

    public function test_image_url_returns_null_when_no_image(): void
    {
        $announcement = Announcement::factory()->make(['image_path' => null]);

        $this->assertNull($announcement->image_url);
    }

    public function test_author_relationship(): void
    {
        $author = User::factory()->admin()->create();
        $announcement = Announcement::factory()->create([
            'author_id'   => $author->id,
            'target_role' => 'all',
        ]);

        $this->assertEquals($author->id, $announcement->author->id);
    }
}
