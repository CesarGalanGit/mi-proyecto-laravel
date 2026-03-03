<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShopControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): self
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => (string) config('admin.email', 'test@example.com'),
        ]);

        return $this->actingAs($admin);
    }

    public function test_admin_puede_ver_panel_de_coches(): void
    {
        Car::factory()->create();

        $this->actingAsAdmin()
            ->get(route('admin.shop.cars.index'))
            ->assertOk()
            ->assertSee('Nuevo coche');
    }

    public function test_usuario_sin_permiso_no_puede_entrar_al_panel_admin_tienda(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'cliente@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('admin.shop.cars.index'))
            ->assertForbidden();
    }

    public function test_admin_puede_crear_coche_desde_panel(): void
    {
        $response = $this->actingAsAdmin()->post(route('admin.shop.cars.store'), [
            'brand' => 'Lexus',
            'model' => 'NX 350h',
            'year' => 2024,
            'price' => 52500,
            'mileage' => 8500,
            'fuel_type' => 'Híbrido',
            'transmission' => 'Automática',
            'color' => 'Gris',
            'city' => 'Madrid',
            'status' => 'available',
            'source_name' => 'Coches.net',
            'source_url' => 'https://www.coches.net/lexus-nx-350h-2024-123',
            'featured' => '1',
            'thumbnail_url' => 'https://example.com/lexus.jpg',
            'gallery_urls' => "https://example.com/lexus-1.jpg\nhttps://example.com/lexus-2.jpg",
            'description' => 'Unidad nacional con revisiones oficiales.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cars', [
            'brand' => 'Lexus',
            'model' => 'NX 350h',
            'status' => 'available',
            'source_name' => 'Coches.net',
        ]);
    }

    public function test_admin_puede_marcar_pedido_como_completado_y_vende_coches(): void
    {
        $buyer = User::factory()->create();

        $car = Car::factory()->create([
            'status' => 'reserved',
        ]);

        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'status' => 'confirmed',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'car_id' => $car->id,
            'car_name' => $car->brand.' '.$car->model,
            'car_slug' => $car->slug,
            'unit_price' => $car->price,
            'line_total' => $car->price,
        ]);

        $response = $this->actingAsAdmin()->patch(route('admin.shop.orders.update', $order), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'status' => 'sold',
        ]);
    }
}
