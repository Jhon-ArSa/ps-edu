<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_role_check(): void
    {
        $user = User::factory()->admin()->make();

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isDocente());
        $this->assertFalse($user->isAlumno());
    }

    public function test_docente_role_check(): void
    {
        $user = User::factory()->docente()->make();

        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isDocente());
        $this->assertFalse($user->isAlumno());
    }

    public function test_alumno_role_check(): void
    {
        $user = User::factory()->alumno()->make();

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isDocente());
        $this->assertTrue($user->isAlumno());
    }

    public function test_avatar_url_returns_default_when_no_avatar(): void
    {
        $user = User::factory()->make(['avatar' => null]);

        $this->assertStringContainsString('default-avatar.png', $user->avatar_url);
    }

    public function test_avatar_url_returns_asset_when_avatar_set(): void
    {
        $user = User::factory()->make(['avatar' => 'avatars/test.jpg']);

        $this->assertStringContainsString('avatars/test.jpg', $user->avatar_url);
    }

    public function test_user_has_fillable_fields(): void
    {
        $user = User::factory()->create([
            'name'   => 'Juan Pérez',
            'email'  => 'juan@example.com',
            'role'   => 'admin',
            'status' => true,
        ]);

        $this->assertEquals('Juan Pérez', $user->name);
        $this->assertEquals('juan@example.com', $user->email);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->status);
    }

    public function test_password_is_hidden(): void
    {
        $user = User::factory()->make();

        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    public function test_status_is_cast_to_boolean(): void
    {
        $user = User::factory()->create(['status' => true]);

        $this->assertIsBool($user->status);
    }
}
