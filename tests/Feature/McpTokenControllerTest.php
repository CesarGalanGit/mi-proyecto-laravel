<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McpTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_no_puede_ver_pantalla_de_token_mcp(): void
    {
        $response = $this->get('/admin/mcp-token');

        $response->assertRedirect('/login');
    }

    public function test_usuario_autenticado_puede_ver_pantalla_de_token_mcp(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $response = $this->actingAs($user)->get('/admin/mcp-token');

        $response->assertStatus(200);
        $response->assertViewIs('admin.mcp-token');
        $response->assertSee('Generar nuevo token');
    }

    public function test_usuario_autenticado_puede_generar_token_mcp(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $response = $this->actingAs($user)->post('/admin/mcp-token');

        $response->assertStatus(200);
        $response->assertViewIs('admin.mcp-token');
        $response->assertSee('Copia y guarda este token ahora');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'mcp',
            'abilities' => '["cars:list"]',
        ]);
    }

    public function test_admin_genera_token_mcp_con_acceso_completo(): void
    {
        $adminEmail = (string) config('admin.email', 'test@example.com');

        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => $adminEmail,
        ]);

        $this->actingAs($admin)->post('/admin/mcp-token')
            ->assertStatus(200);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $admin->id,
            'name' => 'mcp',
            'abilities' => '["*"]',
        ]);
    }
}
