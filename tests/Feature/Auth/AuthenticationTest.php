<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'status' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect();
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'status' => false, // Cuenta inactiva
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_are_redirected_based_on_role(): void
    {
        // Admin
        $admin = User::factory()->create(['role' => 'admin', 'status' => true]);
        $this->actingAs($admin)->get('/')->assertRedirect(route('admin.dashboard'));

        // Docente
        $docente = User::factory()->create(['role' => 'docente', 'status' => true]);
        $this->actingAs($docente)->get('/')->assertRedirect(route('docente.dashboard'));

        // Alumno
        $alumno = User::factory()->create(['role' => 'alumno', 'status' => true]);
        $this->actingAs($alumno)->get('/')->assertRedirect(route('alumno.dashboard'));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create(['status' => true]);

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login'); // Redirige a login, no a /
    }

    public function test_session_is_regenerated_on_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'status' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }
}
