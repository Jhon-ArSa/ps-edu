<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_admin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email'    => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_docente_can_login_and_redirects_to_docente_dashboard(): void
    {
        $docente = User::factory()->docente()->create();

        $response = $this->post('/login', [
            'email'    => $docente->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/docente/dashboard');
        $this->assertAuthenticatedAs($docente);
    }

    public function test_alumno_can_login_and_redirects_to_alumno_dashboard(): void
    {
        $alumno = User::factory()->alumno()->create();

        $response = $this->post('/login', [
            'email'    => $alumno->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/alumno/dashboard');
        $this->assertAuthenticatedAs($alumno);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->post('/login', [
            'email'    => 'noexiste@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'email'    => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }
}
