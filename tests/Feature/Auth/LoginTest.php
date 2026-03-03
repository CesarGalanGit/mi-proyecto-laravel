<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_pantalla_se_muestra(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Iniciar sesión');
    }

    public function test_guest_no_puede_crear_usuario_y_redirige_a_login(): void
    {
        $response = $this->post('/usuarios', [
            'nombre' => 'Test',
            'correo' => 'test@test.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_login_valido_redirige_a_usuarios(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => 'secret1234',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret1234',
        ]);

        $response->assertRedirect('/usuarios');
        $this->assertAuthenticated();
    }

    public function test_login_invalido_muestra_error(): void
    {
        User::factory()->create([
            'email' => 'user@test.com',
            'password' => 'secret1234',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'user@test.com',
            'password' => 'wrong',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_cierra_sesion(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/usuarios');
        $this->assertGuest();
    }
}
