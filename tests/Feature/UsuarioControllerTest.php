<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $response = $this->post('/usuarios', [
            'nombre' => 'María García',
            'correo' => 'maria@test.com',
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
        $response = $this->post('/usuarios', [
            'nombre' => '',
            'correo' => 'no-es-email',
        ]);

        $response->assertSessionHasErrors(['nombre', 'correo']);
    }

    public function test_store_rechaza_email_duplicado(): void
    {
        User::factory()->create(['email' => 'duplicado@test.com']);

        $response = $this->post('/usuarios', [
            'nombre' => 'Otro Usuario',
            'correo' => 'duplicado@test.com',
        ]);

        $response->assertSessionHasErrors('correo');
    }

    public function test_update_modifica_usuario(): void
    {
        $user = User::factory()->create([
            'name' => 'Nombre Viejo',
            'email' => 'viejo@test.com',
        ]);

        $response = $this->put("/usuarios/{$user->id}", [
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

        $response = $this->put("/usuarios/{$user->id}", [
            'nombre' => 'AB',  // min:3
            'correo' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['nombre', 'correo']);
    }

    public function test_destroy_elimina_usuario(): void
    {
        $user = User::factory()->create();

        $response = $this->delete("/usuarios/{$user->id}");

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

        $response = $this->get('/usuarios/exportar');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_dashboard_muestra_estadisticas(): void
    {
        User::factory(5)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('totalUsuarios', 5);
    }
}
