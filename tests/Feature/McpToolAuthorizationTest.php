<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McpToolAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_no_admin_solo_ve_listado_de_coches(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $token = $user->createToken('mcp', ['cars:list'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/mcp/app', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'tools/list',
                'params' => [
                    'per_page' => 50,
                ],
            ]);

        $response->assertStatus(200);

        $tools = $response->json('result.tools');
        $this->assertIsArray($tools);
        $this->assertCount(1, $tools);
        $this->assertSame('list-cars-tool', $tools[0]['name'] ?? null);
    }

    public function test_token_no_admin_no_puede_llamar_herramientas_fuera_de_listado(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $token = $user->createToken('mcp', ['cars:list'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/mcp/app', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'tools/call',
                'params' => [
                    'name' => 'list-users-tool',
                    'arguments' => [],
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('error.code', -32602);
        $response->assertJsonPath('error.message', 'Tool [list-users-tool] not found.');
    }

    public function test_token_admin_ve_todas_las_herramientas(): void
    {
        $adminEmail = (string) config('admin.email', 'test@example.com');

        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => $adminEmail,
        ]);

        $token = $admin->createToken('mcp', ['*'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/mcp/app', [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'tools/list',
                'params' => [
                    'per_page' => 50,
                ],
            ]);

        $response->assertStatus(200);

        $tools = $response->json('result.tools');
        $this->assertIsArray($tools);

        $names = array_values(array_filter(array_map(fn ($t) => $t['name'] ?? null, $tools)));

        $this->assertContains('create-user-tool', $names);
        $this->assertContains('create-car-listing-tool', $names);
        $this->assertContains('list-users-tool', $names);
        $this->assertContains('list-cars-tool', $names);
    }
}
