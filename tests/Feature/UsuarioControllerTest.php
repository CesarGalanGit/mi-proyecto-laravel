<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateAsAdmin(): self
    {
        $email = (string) config('admin.email', 'test@example.com');

        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => $email,
        ]);

        return $this->actingAs($admin);
    }

    public function test_index_muestra_lista_de_usuarios(): void
    {
        User::factory(3)->create();

        $response = $this->get('/usuarios');

        $response->assertStatus(200);
        $response->assertViewIs('usuarios');
        $response->assertViewHas('usuarios');
    }

    public function test_store_crea_usuario_valido(): void
    {
        $response = $this->authenticateAsAdmin()->post('/usuarios', [
            'nombre' => 'María García',
            'correo' => 'maria@test.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'María García',
            'email' => 'maria@test.com',
        ]);
    }

    public function test_store_rechaza_datos_invalidos(): void
    {
        $response = $this->authenticateAsAdmin()->post('/usuarios', [
            'nombre' => '',
            'correo' => 'no-es-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['nombre', 'correo', 'password']);
    }

    public function test_store_rechaza_email_duplicado(): void
    {
        User::factory()->create(['email' => 'duplicado@test.com']);

        $response = $this->authenticateAsAdmin()->post('/usuarios', [
            'nombre' => 'Otro Usuario',
            'correo' => 'duplicado@test.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ]);

        $response->assertSessionHasErrors('correo');
    }

    public function test_update_modifica_usuario(): void
    {
        $user = User::factory()->create([
            'name' => 'Nombre Viejo',
            'email' => 'viejo@test.com',
        ]);

        $response = $this->authenticateAsAdmin()->put("/usuarios/{$user->id}", [
            'nombre' => 'Nombre Nuevo',
            'correo' => 'nuevo@test.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre Nuevo',
            'email' => 'nuevo@test.com',
        ]);
    }

    public function test_update_valida_datos(): void
    {
        $user = User::factory()->create();

        $response = $this->authenticateAsAdmin()->put("/usuarios/{$user->id}", [
            'nombre' => 'AB',  // min:3
            'correo' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['nombre', 'correo']);
    }

    public function test_destroy_elimina_usuario(): void
    {
        $user = User::factory()->create();

        $response = $this->authenticateAsAdmin()->delete("/usuarios/{$user->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_buscar_filtra_por_nombre(): void
    {
        User::factory()->create(['name' => 'Carlos Pérez']);
        User::factory()->create(['name' => 'Ana López']);

        $response = $this->get('/usuarios?buscar=Carlos');

        $response->assertStatus(200);
        $response->assertSee('Carlos Pérez');
    }

    public function test_paginacion_por_pagina(): void
    {
        User::factory(10)->create();

        $response = $this->get('/usuarios?per_page=5');

        $response->assertStatus(200);
        $response->assertViewHas('usuarios', function ($usuarios) {
            return $usuarios->perPage() === 5;
        });
    }

    public function test_export_descarga_csv(): void
    {
        User::factory(3)->create();

        $response = $this->authenticateAsAdmin()->get('/usuarios/exportar');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_buscar_filtra_por_email(): void
    {
        User::factory()->create(['email' => 'carlos@test.com']);
        User::factory()->create(['email' => 'ana@test.com']);

        $response = $this->get('/usuarios?buscar=carlos@test.com');

        $response->assertStatus(200);
        $response->assertSee('carlos@test.com');
        $response->assertDontSee('ana@test.com');
    }

    public function test_per_page_invalido_vuelve_a_default(): void
    {
        User::factory(10)->create();

        $response = $this->get('/usuarios?per_page=999');

        $response->assertStatus(200);
        $response->assertViewHas('usuarios', function ($usuarios) {
            return $usuarios->perPage() === 5;
        });
    }

    public function test_export_incluye_cabecera_csv(): void
    {
        User::factory(1)->create();

        $response = $this->authenticateAsAdmin()->get('/usuarios/exportar');

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $this->assertStringContainsString('ID,Nombre,Email,', $content);
        $this->assertStringContainsString('Fecha de Registro', $content);
    }

    public function test_dashboard_muestra_estadisticas(): void
    {
        User::factory(5)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('totalUsuarios', 5);
    }
}
