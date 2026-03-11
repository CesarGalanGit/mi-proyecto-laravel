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

    public function test_login_valido_redirige_a_dashboard_para_usuario_normal(): void
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

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_login_con_recordarme_crea_cookie_de_recuerdo(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'remember@test.com',
            'password' => 'secret1234',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret1234',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $rememberCookie = collect($response->headers->getCookies())->first(
            fn ($cookie) => str_starts_with($cookie->getName(), 'remember_web_')
        );

        $this->assertNotNull($rememberCookie);
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

    public function test_bloquea_login_despues_de_demasiados_intentos_fallidos(): void
    {
        User::factory()->create([
            'email' => 'limit@test.com',
            'password' => 'secret1234',
        ]);

        for ($intento = 0; $intento < 5; $intento++) {
            $this->from('/login')->post('/login', [
                'email' => 'limit@test.com',
                'password' => 'incorrecta',
            ])->assertRedirect('/login');
        }

        $response = $this->from('/login')->post('/login', [
            'email' => 'limit@test.com',
            'password' => 'incorrecta',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');

        $mensajeError = $response->getSession()->get('errors')->first('email');

        $this->assertStringContainsString('Demasiados intentos.', $mensajeError);
    }

    public function test_logout_cierra_sesion(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_login_admin_redirige_a_usuarios(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => (string) config('admin.email', 'test@example.com'),
            'password' => 'secret1234',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'secret1234',
        ]);

        $response->assertRedirect('/usuarios');
        $this->assertAuthenticated();
    }
}
